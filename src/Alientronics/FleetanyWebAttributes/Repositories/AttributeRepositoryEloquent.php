<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AttributeRepository;
use App\Entities\Key;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Prettus\Validator\Exceptions\ValidatorException;

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
        $attributes = $this->scopeQuery(function ($query) use ($filters) {

            $query = $query->select('*', 'entity_key as entity-key', 'entity_key as entity-key');
            
            if (!empty($filters['entity-key'])) {
                $query = $query->where('entity_key', 'like', '%'.$filters['entity-key'].'%');
            }
            if (!empty($filters['description'])) {
                $query = $query->where('description', 'like', '%'.$filters['description'].'%');
            }

            $query = $query->where('company_id', Auth::user()['company_id']);
            $query = $query->orderBy($filters['sort'], $filters['order']);
            
            return $query;
        })->paginate($filters['paginate']);
        
        return $attributes;
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
    
    public static function getKey($idKey)
    {
        $client = new Client();
        $response = $client->request('GET', 'http://localhost:8000/api/v1/key/' . $idKey);
        
        return json_decode((string)$response->getBody());
    }
    
    public static function updateKey($idKey, $inputs) {
        $client = new Client();
        $response = $client->request('PUT', 'http://localhost:8000/api/v1/key/' . $idKey, [
            'form_params' => $inputs
        ]);
        
        if((string)$response->getBody() == '"updated"') {
            return true;
        } else {
            return false;
        }
    }
    
    public static function createKey($inputs) {
        
        $client = new Client();
        $response = $client->request('POST', 'http://localhost:8000/api/v1/key', [
            'form_params' => $inputs
        ]);
        
        if((string)$response->getBody() == '"created"') {
            return true;
        } else {
            return false;
        }
    }
    
    public static function deleteKey($idKey) {
        $client = new Client();
        $response = $client->request('DELETE', 'http://localhost:8000/api/v1/key/' . $idKey);
        
        if((string)$response->getBody() == '"deleted"') {
            return true;
        } else {
            return false;
        }
    }
    
    public static function getKeys($entity_key)
    {
        try {
            $client = new Client();
            $response = $client->request('GET', 'http://localhost:8000/api/v1/key/'.
                Auth::user()['company_id'].'/'.$entity_key);
        
            return json_decode((string)$response->getBody());
            
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }
    
    public static function getValues($entity_key, $entity_id)
    {
        try {
            $client = new Client();
            $response = $client->request('GET', 'http://localhost:8000/api/v1/values/'.
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
                    $attributes[$key]->value = "";
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
                    if(!empty($valuesIndexedByAttr[$value->id])) {
                        $attributes[$key]->value = $valuesIndexedByAttr[$value->id];
                    } else {
                        $attributes[$key]->value = "";
                    }
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
    
    public static function setValues($inputs)
    {
        try {

            $entity_id = $inputs['entity_id'];
            $entity_key = $inputs['entity_key'];
            
            foreach ($inputs as $key => $value) {
                if(substr($key, 0, 9) == "attribute") {
                    $inputs[substr($key, 9)] = $value;
                }
                unset($inputs[$key]);
            }

            $client = new Client();
            $response = $client->request('POST', 'http://localhost:8000/api/v1/values/'.
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
