<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AttributeRepository;
use App\Entities\Key;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Pagination\LengthAwarePaginator;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;

class AttributeRepositoryEloquent extends BaseRepository implements AttributeRepository
{

    protected $rules = [
        'description'      => 'min:3|required',
        'entity_key' => 'min:3|required',
        ];

    public function model()
    {
        return Key::class;
    }

    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
    public function results($filters = [])
    {
        if(empty($filters['entity-key'])) {
            $filters['entity-key'] = '-';
        }
        
        if(empty($filters['description'])) {
            $filters['description'] = '-';
        }
        
        $attributes = self::getKeys($filters['entity-key'], $filters['description']);
        
        $filters['page'] = 1;
        $currentPageSearchResults = array_slice($attributes, $filters['paginate'] * ($filters['page'] - 1), $filters['paginate']);
        $paginatedSearchResults = new LengthAwarePaginator($currentPageSearchResults, count($attributes), $filters['paginate'], $filters['page']);

        return $paginatedSearchResults;
    }
    
    public function hasReferences($idAttribute)
    {
        $attribute = $this->find($idAttribute);
        $countReferences = $attribute->values()->count();
        
        if ($countReferences > 0) {
            return true;
        }
        return false;
    }
    
    public function getKey($idKey)
    {
        $client = new Client();
        $response = $client->request('GET', config('app.attributes_api_url').'/api/v1/key/' . $idKey);
        
        return json_decode((string)$response->getBody());
    }
    
    public function updateKey($idKey, $inputs) {
        $client = new Client();
        $response = $client->request('PUT', config('app.attributes_api_url').'/api/v1/key/' . $idKey, [
            'form_params' => $inputs
        ]);
        
        if((string)$response->getBody() == '"updated"') {
            return true;
        } else {
            return false;
        }
    }
    
    public function createKey($inputs) {
        
        $client = new Client();
        $response = $client->request('POST', config('app.attributes_api_url').'/api/v1/key', [
            'form_params' => $inputs
        ]);
        
        if((string)$response->getBody() == '"created"') {
            return true;
        } else {
            return false;
        }
    }
    
    public function deleteKey($idKey) {
        $client = new Client();
        $response = $client->request('DELETE', config('app.attributes_api_url').'/api/v1/key/' . $idKey);
        
        if((string)$response->getBody() == '"deleted"') {
            return true;
        } else {
            return false;
        }
    }
    
    public static function getKeys($entity_key, $description = '-')
    {
        try {
            $client = new Client();
            $response = $client->request('GET', config('app.attributes_api_url').'/api/v1/keys/'.
                Auth::user()['company_id'].'/'.$entity_key.'/'.$description);
        
            return json_decode((string)$response->getBody());
            
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }
    
    public function getValues($entity_key, $entity_id)
    {
        try {
            $client = new Client();
            $response = $client->request('GET', config('app.attributes_api_url').'/api/v1/values/'.
               $entity_key.'/'.$entity_id);
        
            return json_decode((string)$response->getBody());
            
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }
    
    public static function getAttributesWithValues($entity_key, $entity_id = null)
    {
        try {
            $attributes = self::getKeys($entity_key);

            if(empty($entity_id) && !empty($attributes)) {
                foreach ($attributes as $key => $value) {
                    $attributes[$key] = self::setAttributesProperties($attributes[$key]);
                }
            } else if(!empty($entity_id) && !empty($attributes)) {
                $values = self::getValues($entity_key, $entity_id);
                
                $valuesIndexedByAttr = [];
                if(!empty($values)) {
                    foreach ($values as $value) {
                        $valuesIndexedByAttr[$value->attribute_id] = $value->value;
                    }
                }
                
                foreach ($attributes as $key => $value) {
                    $attributes[$key] = self::setAttributesProperties($attributes[$key], $valuesIndexedByAttr[$value->id]);
                }
            } else {
                $attributes = [];
            }

            return $attributes;
            
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }
    
    private static function setAttributesProperties($attribute, $value = []) {

        if($attribute->type == 'checkbox' && empty($value)) {
            $attribute->value = [];
        } else if(empty($value)) {
            $attribute->value = "";
        } else if($attribute->type == 'checkbox') {
            $attribute->value = json_decode($value);
        } else {
            $attribute->value = $value;
        }
        $attribute->options = self::getOptions($attribute->options);
        
        return $attribute;
    }
    
    private static function getOptions($options) {
        if(empty(($options))) {
            return [];
        }
        
        $options = explode(",", $options);
        
        $returnOptions = [];
        foreach ($options as $key => $value) {
            $returnOptions[$value] = $value;            
        }

        return $returnOptions;
    }
    
    public static function setValues($inputs)
    {
        try {

            $entity_id = $inputs['entity_id'];
            $entity_key = $inputs['entity_key'];
            
            foreach ($inputs as $key => $value) {
                if(substr($key, 0, 9) == "attribute") {
                    $inputs[substr($key, 9)] = is_array($value) ? json_encode($value) : $value;
                }
                unset($inputs[$key]);
            }

            $client = new Client();
            $response = $client->request('POST', config('app.attributes_api_url').'/api/v1/values/'.
                $entity_key.'/'.$entity_id, [
                    'form_params' => ['attributes' => $inputs]
            ]);
        
            return json_decode((string)$response->getBody());
            
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }
}
