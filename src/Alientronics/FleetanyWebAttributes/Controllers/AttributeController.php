<?php

namespace Alientronics\FleetanyWebAttributes\Controllers;

use App\Http\Controllers\Controller;
use Alientronics\FleetanyWebAttributes\Repositories\AttributeRepositoryEloquent;
use Lang;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Support\Facades\Auth;
use App\Repositories\TypeRepositoryEloquent;

class AttributeController extends Controller
{

    protected $attributeRepo;
    protected $inputs;
    
    protected $fields = [
        'id',
        'entity-key',
        'description'
    ];
    
    public function __construct(AttributeRepositoryEloquent $attributeRepo)
    {
        parent::__construct();

        $this->attributeRepo = $attributeRepo;
        
        $this->inputs = $this->request->all();
        $this->inputs['company_id'] = Auth::user()['company_id'];
    }

    public function index()
    {
        $filters = $this->helper->getFilters($this->request->all(), $this->fields, $this->request);
        
        $attributes = $this->attributeRepo->results($filters);
                
        return view("attribute.index", compact('attributes', 'filters'));
    }
    
    public function create()
    {
        $attribute = (object)[
                'id'=>'',
                'entity_key'=>'',
                'description'=>'',
                'type'=>'',
                'options'=>'',
            ];

        $entity_key = [ 'vehicle' => 'vehicle',
                        'contacts'=>'contacts',
                        'entry'=>'entry',
                        'trip'=>'trip',
                        ];
        
        foreach ($entity_key as $entity_key_value) {
            $entity_key_types = TypeRepositoryEloquent::getTypes($entity_key_value)->toArray();
            if (!empty($entity_key_types)) {
                foreach ($entity_key_types as $entity_key_type) {
                    $entity_key_type = $entity_key_value.".".$entity_key_type;
                    $entity_key[$entity_key_type] = $entity_key_type;
                }
            }
        }
        
        asort($entity_key);
        
        $type = ['string' => 'string', 'numeric' => 'numeric', 'select' => 'select',
                    'checkbox' => 'checkbox', 'file' => 'file'];
        return view("attribute.edit", compact('attribute', 'entity_key', 'type'));
    }

    public function store()
    {
        try {
            $response = $this->attributeRepo->createKey($this->inputs);
            
            if ($response) {
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
            $attribute = $this->attributeRepo->getKey($idAttribute);
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
            $response = $this->attributeRepo->updateKey($idAttribute, $this->inputs);
            
            if ($response) {
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
            $response = $this->attributeRepo->deleteKey($idAttribute);
            
            if ($response) {
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
