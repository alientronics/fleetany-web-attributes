<?php

namespace Tests;

use Tests\AcceptanceTestCase;
use App\Entities\Attribute;
use Lang;

class AttributePermissionTest extends AcceptanceTestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = $this->createExecutive();
        $this->actingAs($user);
    }
    
    public function testViewExecutive()
    {
        $this->get('/')->see('<a class="mdl-navigation__link" href="'.$this->baseUrl.'/attribute">', true);
        
        $this->get('/attribute')->assertResponseStatus(401);
    }
    
    public function testCreateExecutive()
    {
        $this->get('/attribute/create')->assertResponseStatus(401);
    }
    
    public function testUpdateExecutive()
    {
        $this->get('/attribute/'.Attribute::all()->last()['id'].'/edit')
            ->assertResponseStatus(401)
        ;
    }
    
    public function testDeleteExecutive()
    {
        $this->get('/attribute/destroy/'.Attribute::all()->last()['id'])
            ->assertResponseStatus(302)
        ;
    }
    
    public function testAccessDeniedCompany()
    {
        $user = factory(\App\Entities\User::class)->create();
        $user->setUp();
        $this->actingAs($user);

        $this->visit('/attribute/1/edit');
        $this->see(Lang::get('general.accessdenied'));
        
        $this->visit('/attribute/destroy/1');
        $this->see(Lang::get('general.accessdenied'));
    }
}
