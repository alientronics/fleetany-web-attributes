<?php

namespace Alientronics\FleetanyWebAttributes\Repositories;

use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Pagination\LengthAwarePaginator;
use GuzzleHttp\json_decode;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Support\Facades\Input;

class AttributeRepositoryEloquent
{

    private static $client;

    protected $rules = [
        'description'      => 'min:3|required',
        'entity_key' => 'min:3|required',
        ];

    public static function setClient($client)
    {
        self::$client = $client;
    }

    public static function getClient()
    {
        if (empty(self::$client)) {
            self::$client = new Client();
        }
        return self::$client;
    }
    
    public function results($filters = [])
    {
        if (empty($filters['entity-key'])) {
            $filters['entity-key'] = '-';
        }
        
        if (empty($filters['description'])) {
            $filters['description'] = '-';
        }
        
        $attributes = self::getKeys($filters['entity-key'], $filters['description']);
        
        $filters['page'] = 1;
        $curPageSearchResults = is_array($attributes) ? array_slice(
            $attributes,
            $filters['paginate'] * ($filters['page'] - 1),
            $filters['paginate']
        ) : [];
        $pagSearchResults = new LengthAwarePaginator(
            $curPageSearchResults,
            count($attributes),
            $filters['paginate'],
            $filters['page']
        );

        return $pagSearchResults;
    }
    
    public function getKey($key)
    {
        $client = self::getClient();
        $response = $client->request('GET', config('app.attributes_api_url').'/api/v1/key/' . $key.
            '?api_token=' . config('app.attributes_api_key'));
        
        return json_decode((string)$response->getBody());
    }
    
    public function updateKey($key, $inputs)
    {
        $client = self::getClient();
        $response = $client->request('PUT', config('app.attributes_api_url').'/api/v1/key/' . $key.
            '?api_token=' . config('app.attributes_api_key'), [
            'form_params' => $inputs
            ]);
        
        if ((string)$response->getBody() == '"updated"') {
            return true;
        } else {
            return false;
        }
    }
    
    public function createKey($inputs)
    {
        
        $client = self::getClient();
        $response = $client->request('POST', config('app.attributes_api_url').'/api/v1/key'.
            '?api_token=' . config('app.attributes_api_key'), [
            'form_params' => $inputs
            ]);
        
        if ((string)$response->getBody() == '"created"') {
            return true;
        } else {
            return false;
        }
    }
    
    public function deleteKey($key)
    {
        $client = self::getClient();
        $response = $client->request('DELETE', config('app.attributes_api_url').'/api/v1/key/' . $key.
            '?api_token=' . config('app.attributes_api_key'));
        
        if ((string)$response->getBody() == '"deleted"') {
            return true;
        } else {
            return false;
        }
    }
    
    public static function getKeys($entity_key, $description = '-')
    {
        try {
            $client = self::getClient();
            $response = $client->request('GET', config('app.attributes_api_url').'/api/v1/keys/'.
                Auth::user()['company_id'].'/'.$entity_key.'/'.$description.
                '?api_token=' . config('app.attributes_api_key'));
        
            return json_decode((string)$response->getBody());
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }
    
    public static function getValues($entity_key, $entity_id)
    {
        try {
            $client = self::getClient();
            $response = $client->request('GET', config('app.attributes_api_url').'/api/v1/values/'.
               $entity_key.'/'.$entity_id . '?api_token=' . config('app.attributes_api_key'));
        
            return json_decode((string)$response->getBody());
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }
    
    public static function getAttributes($entity_key)
    {
        if (config('app.attributes_api_url') == null) {
            return [];
        }

        try {
            $attributes = self::getKeys($entity_key);

            if (empty($attributes)) {
                return [];
            }

            foreach (array_keys($attributes) as $key) {
                $attributes[$key] = self::setAttributesProperties($attributes[$key]);
            }

            return $attributes;
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }

    public static function getAttributesWithValues($entity_key, $entity_id)
    {

        if (config('app.attributes_api_url') == null) {
            return [];
        }

        try {
            $attributes = self::getKeys($entity_key);

            if (empty($attributes)) {
                return [];
            }


            $values = self::getValues($entity_key, $entity_id);
            
            $valuesIndexedByAttr = [];
            if (!empty($values)) {
                foreach ($values as $value) {
                    $valuesIndexedByAttr[$value->attribute_id] = $value->value;
                }
            }
            
            foreach ($attributes as $key => $value) {
                if (empty($valuesIndexedByAttr[$value->id])) {
                    $valuesIndexedByAttr[$value->id] = [];
                }
                $attributes[$key] = self::setAttributesProperties(
                    $attributes[$key],
                    $valuesIndexedByAttr[$value->id]
                );
            }

            return $attributes;
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }
    
    private static function setAttributesProperties($attribute, $value = [])
    {

        if ($attribute->type == 'checkbox' && empty($value)) {
            $attribute->value = [];
        } elseif (empty($value)) {
            $attribute->value = "";
        } elseif ($attribute->type == 'checkbox') {
            $attribute->value = json_decode($value);
        } else {
            $attribute->value = $value;
        }
        $attribute->options = self::getOptions($attribute->options);
        
        return $attribute;
    }
    
    private static function getOptions($options)
    {
        if (empty(($options))) {
            return [];
        }
        
        $options = explode(",", $options);
        
        $returnOptions = [];
        foreach ($options as $key => $value) {
            $key = $key;
            $returnOptions[$value] = $value;
        }

        return $returnOptions;
    }
    
    public static function setValues($inputs)
    {

        if (config('app.attributes_api_url') == null) {
            return [];
        }
        
        try {
            $entity_id = $inputs['entity_id'];
            $entity_key = $inputs['entity_key'];
            
            foreach ($inputs as $key => $value) {
                if (substr($key, 0, 9) == "attribute") {
                    $filename = "";
                    if (is_object($value) && get_class($value) == "Illuminate\\Http\\UploadedFile") {
                        $filename = Input::file($key)->getClientOriginalName();
                        $extension = pathinfo($filename, PATHINFO_EXTENSION);
                        $filename = substr($filename, 0, -strlen($extension) - 1) .
                                        date("-YmdHis.") . $extension;
                        $value = fopen($value, 'r');
                    } elseif (is_array($value)) {
                        $value = json_encode($value);
                        $filename = "";
                    }
                    
                    $inputs[] = ["name" => substr($key, 9),
                                "contents" => $value,
                                "filename" => $filename,
                    ];
                }
                unset($inputs[$key]);
            }

            $client = self::getClient();
            $response = $client->request('POST', config('app.attributes_api_url').'/api/v1/values/'.
                $entity_key.'/'.$entity_id.'/'.Auth::user()['company_id'] . '?api_token=' . config('app.attributes_api_key'), [
                    'multipart' => $inputs
                ]);

            return json_decode((string)$response->getBody());
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }
    
    public function download($fileNameEncoded)
    {
        $client = new Client();
        $response = $client->request('GET', config('app.attributes_api_url').'/api/v1/values/download'.
            '?api_token=' . config('app.attributes_api_key').
            '&file=' . $fileNameEncoded.
            '&company_id=' . Auth::user()['company_id'], ['stream' => true]);

        return $response->getBody();
    }
}
