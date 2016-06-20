<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AttributeRepository;
use App\Entities\Key;
use Illuminate\Support\Facades\Auth;

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
    
    public static function getAttributes($entity_key = null)
    {
        $attributes = Key::where('company_id', Auth::user()['company_id']);
        
        if (!empty($entity_key)) {
            $attributes = $attributes->where('entity_key', $entity_key);
        }
        
        $attributes = $attributes->lists('description', 'id');
        
        return $attributes;
    }
}
