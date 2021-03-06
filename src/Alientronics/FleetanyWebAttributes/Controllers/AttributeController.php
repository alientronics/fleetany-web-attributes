<?php

namespace Alientronics\FleetanyWebAttributes\Controllers;

use App\Http\Controllers\Controller;
use Alientronics\FleetanyWebAttributes\Repositories\AttributeRepositoryEloquent;
use Lang;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Support\Facades\Auth;
use App\Repositories\TypeRepositoryEloquent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Route;
use Illuminate\Http\Response;

class AttributeController extends Controller
{

    protected $attributeRepo;
    protected $inputs;
    
    protected $fields = [
        'id',
        'entity-key',
        'description'
    ];
    
    protected $entity_key = [
        'vehicle' => 'vehicle',
        'contact'=>'contact',
        'entry'=>'entry',
        'trip'=>'trip',
    ];

    protected $type = [
        'string' => 'string',
        'numeric' => 'numeric',
        'select' => 'select',
        'checkbox' => 'checkbox',
        'file' => 'file'
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
        
        $entity_key = $this->entity_key;
        foreach ($this->entity_key as $entity_key_value) {
            $entity_key_types = TypeRepositoryEloquent::getTypes($entity_key_value)->toArray();
            if (!empty($entity_key_types)) {
                foreach ($entity_key_types as $entity_key_type) {
                    $entity_key_type = $entity_key_value.".".$entity_key_type;
                    $entity_key[$entity_key_type] = $entity_key_type;
                }
            }
        }
        
        asort($entity_key);
        
        $type = $this->type;

        return view("attribute.edit", compact('attribute', 'entity_key', 'type'));
    }

    public function store()
    {
        try {
            $response = $this->attributeRepo->createKey($this->inputs);
            
            if ($response) {
                //Fix Segmentation fault at phpunit --coverage-clover
                $table = Lang::get('attributes.Attribute');
                return $this->redirect->to('attribute')->with('message', Lang::get(
                    'general.succefullcreate',
                    ['table'=> $table]
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
            $entity_key = $this->entity_key;
            $type = $this->type;
            
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
                //Fix Segmentation fault at phpunit --coverage-clover
                $table = Lang::get('attributes.Attribute');
                return $this->redirect->to('attribute')->with('message', Lang::get(
                    'general.succefullupdate',
                    ['table'=> $table]
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

    public function download(Route $route)
    {
        try {
            $file = $this->attributeRepo->download($route->getParameter('file'));
            $content = "";
            while (!$file->eof()) {
                $content .= $file->read(1024);
            }
            if (!empty($content)) {
                $fileName = urldecode(base64_decode($route->getParameter('file')));
                $headers = [
                    //'Content-Type' => 'application/octet-stream',
                    'Content-Disposition: attachment; filename=' => $fileName
                ];
                return (new Response($content, 200, $headers));
            } else {
                return (new Response("", 404));
            }
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }
}
