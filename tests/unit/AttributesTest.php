<?php

namespace Tests\Unit;

use Tests\UnitTestCase;
use Alientronics\FleetanyWebAttributes\Repositories\AttributeRepositoryEloquent;

class AttributesTest extends UnitTestCase
{

    

    public function testHasVehicle()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('');
        $mockClient->shouldReceive('request')->andReturn($response);
    
        AttributeRepositoryEloquent::setClient($mockClient);
        $return = AttributeRepositoryEloquent::getAttributesWithValues(1);
    
        $this->assertEquals($return, []);
    }
    
    public function testGetAttributesWithValues()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('');
        $mockClient->shouldReceive('request')->andReturn($response);

        AttributeRepositoryEloquent::setClient($mockClient);
        $return = AttributeRepositoryEloquent::getAttributesWithValues('vehicle', 1);

        $this->assertEquals($return, []);
    }
    
    public function testSetValues()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('');
        $mockClient->shouldReceive('request')->andReturn($response);

        $inputs = [];
        $inputs['entity_key'] = 'vehicle';
        $inputs['entity_id'] = 1;
        $inputs['attribute1'] = [1];
        
        AttributeRepositoryEloquent::setClient($mockClient);
        $return = AttributeRepositoryEloquent::setValues($inputs);

        $this->assertEquals($return, null);
    }
}
