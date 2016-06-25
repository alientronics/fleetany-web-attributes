<?php

namespace Tests\Acceptance;

use Tests\AcceptanceTestCase;
use App\Entities\Attribute;
use App\Entities\Type;

class AttributeControllerTest extends AcceptanceTestCase
{
    public function testView()
    {
        $this->visit('/')->see('mdl-navigation__link" href="'.$this->baseUrl.'/attribute">');
    
        $this->visit('/attribute')
            ->see('<i class="material-icons">filter_list</i>')
        ;
    }
    
    public function testCreate()
    {
        $this->visit('/attribute')->see('<a href="'.$this->baseUrl.'/attribute/create');
        
        $this->visit('/attribute/create');
    
        $this->select('vehicle', 'entity_key')
            ->type('Description', 'description')
            ->select('string', 'type')
            ->type('option1,option2', 'options')
            ->press('Enviar')
            ->seePageIs('/attribute')
        ;
    
        $this->seeInDatabase(
            'keys',
            [
                    'entity_key' => 'vehicle',
                    'description' => 'Description',
                    'type' => 'string',
                    'options' => 'option1,option2',
            ]
        );
    }

    public function testUpdate()
    {
        $this->visit('/attribute/'.Attribute::all()->last()['id'].'/edit');
    
        $this->select('contact', 'entity_key')
            ->type('Description 2', 'description')
            ->select('select', 'type')
            ->type('option1edited,option2edited', 'options')
            ->press('Enviar')
            ->seePageIs('/attribute')
        ;
    
        $this->seeInDatabase(
            'keys',
            [
                    'entity_key' => 'contact',
                    'description' => 'Description 2',
                    'type' => 'select',
                    'options' => 'option1edited,option2edited',
            ]
        );
    }
    
    public function testFilters()
    {
        $this->visit('/attribute')
			->type('contact', 'entity_key')
            ->type('Description 2', 'description')
            ->press('Buscar')
            ->see('contact</div>')
            ->see('Description 2</div>')
        ;
    }
    
    public function testSort()
    {
        $this->visit('/attribute?id=&entity-key=&description=&sort=entity-key-desc')
            ->see('mode_edit</i>');
			
        $this->visit('/attribute?id=&entity-key=&description=&sort=entity-key-asc')
            ->see('mode_edit</i>');
			
        $this->visit('/attribute?id=&entity-key=&description=&sort=edescription-desc')
            ->see('mode_edit</i>');
			
        $this->visit('/attribute?id=&entity-key=&description=&sort=description-asc')
            ->see('mode_edit</i>');      
    }
    
    public function testDelete()
    {
        $idDelete = Attribute::all()->last()['id'];
        $this->seeInDatabase('keys', ['id' => $idDelete]);
        $this->visit('/attribute/destroy/'.$idDelete);
        $this->seeIsSoftDeletedInDatabase('keys', ['id' => $idDelete]);
    }
}
