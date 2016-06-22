<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\AttributeRepositoryEloquent;
use App\Entities\Key;
use Lang;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Support\Facades\Auth;

class AttributeController extends Controller
{

    protected $attributeRepo;
    
    protected $fields = [
        'id',
        'entity-key',
        'description'
    ];
    
    public function __construct(AttributeRepositoryEloquent $attributeRepo)
    {
        parent::__construct();

        $this->middleware('auth');
        $this->attributeRepo = $attributeRepo;
    }

    public function index()
    {
        $filters = $this->helper->getFilters($this->request->all(), $this->fields, $this->request);
        
        $attributes = $this->attributeRepo->results($filters);
                
        return view("attribute.index", compact('attributes', 'filters'));
    }
    
    public function create()
    {
        $attribute = new Key();
        $entity_key = ['vehicle' => 'vehicle'];
        $type = ['string' => 'string', 'numeric' => 'numeric', 'select' => 'select', 
                    'checkbox' => 'checkbox', 'file' => 'file'];
        return view("attribute.edit", compact('attribute', 'entity_key', 'type'));
    }

    public function store()
    {
        try {
            
            $inputs = $this->request->all();
            $inputs['company_id'] = Auth::user()['company_id'];
            
            $response = AttributeRepositoryEloquent::createKey($inputs);
            
            if($response) {
                return $this->redirect->to('attribute')->with('message', Lang::get(
                    'general.succefullcreate',
                    ['table'=> Lang::get('attributes.Attribute')]
                ));
            } else {
                return $this->redirect->back();
            }
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }
    
    public function edit($idAttribute)
    {
        try {
            $attribute = AttributeRepositoryEloquent::getKey($idAttribute);
            $entity_key = ['vehicle' => 'vehicle'];
            $type = ['string' => 'string', 'numeric' => 'numeric', 'select' => 'select',
                'checkbox' => 'checkbox', 'file' => 'file'];
            
            return view("attribute.edit", compact('attribute', 'entity_key', 'type'));
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }
    
    public function update($idAttribute)
    {
        try {
            
            $inputs = $this->request->all();
            $inputs['company_id'] = Auth::user()['company_id'];
            
            $response = AttributeRepositoryEloquent::updateKey($idAttribute, $inputs);
            
            if($response) {
                return $this->redirect->to('attribute')->with('message', Lang::get(
                    'general.succefullupdate',
                    ['table'=> Lang::get('attributes.Attribute')]
                ));
            } else {
                return $this->redirect->back();
            }
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }
    
    public function destroy($idAttribute)
    {
        try {
            
            $response = AttributeRepositoryEloquent::deleteKey($idAttribute);
            
            if($response) {
                return $this->redirect->to('attribute')->with('message', Lang::get("general.deletedregister"));
            } else {
                return $this->redirect->to('attribute')->with('message', Lang::get("general.deletedregistererror"));
            }
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }
}
