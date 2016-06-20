<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\AttributeRepositoryEloquent;
use App\Entities\Key;
use Log;
use Lang;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CompanyRepositoryEloquent;

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
            $this->attributeRepo->validator();
            $inputs = $this->request->all();
            $this->attributeRepo->create($inputs);
            return $this->redirect->to('attribute')->with('message', Lang::get(
                'general.succefullcreate',
                ['table'=> Lang::get('attributes.Attribute')]
            ));
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }
    
    public function edit($idAttribute)
    {
        $attribute = $this->attributeRepo->find($idAttribute);
        $this->helper->validateRecord($attribute);

        $entity_key = ['vehicle' => 'vehicle'];
        $type = ['string' => 'string', 'numeric' => 'numeric', 'select' => 'select',
                    'checkbox' => 'checkbox', 'file' => 'file'];
        
        return view("attribute.edit", compact('attribute', 'entity_key', 'type'));
    }
    
    public function update($idAttribute)
    {
        try {
            $attribute = $this->attributeRepo->find($idAttribute);
            $this->helper->validateRecord($attribute);
            $this->attributeRepo->validator();
            $inputs = $this->request->all();
            $this->attributeRepo->update($inputs, $idAttribute);
            $this->session->flash(
                'message',
                Lang::get(
                    'general.succefullupdate',
                    ['table'=> Lang::get('attributes.Attribute')]
                )
            );
            return $this->redirect->to('attribute');
        } catch (ValidatorException $e) {
            return $this->redirect->back()->withInput()
                    ->with('errors', $e->getMessageBag());
        }
    }
    
    public function destroy($idAttribute)
    {
        $hasReferences = $this->attributeRepo->hasReferences($idAttribute);
        $attribute = $this->attributeRepo->find($idAttribute);
        if ($attribute && !$hasReferences) {
            $this->helper->validateRecord($attribute);
            Log::info('Delete field: '.$idAttribute);
            $this->attributeRepo->delete($idAttribute);
            return $this->redirect->to('attribute')->with('message', Lang::get("general.deletedregister"));
        } elseif ($hasReferences) {
            return $this->redirect->to('attribute')->with('message', Lang::get("general.deletedregisterhasreferences"));
        } else {
            return $this->redirect->to('attribute')->with('message', Lang::get("general.deletedregistererror"));
        }
    }
}
