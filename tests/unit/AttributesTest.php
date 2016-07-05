<?php

namespace Tests\Unit;

use Tests\UnitTestCase;
use Alientronics\FleetanyWebAttributes\Repositories\AttributeRepositoryEloquent;
use Illuminate\Pagination\LengthAwarePaginator;

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
    
    public function testResults()
    {
        $filters = [];
        $filters['entity-key'] = 'vehicle';
        $filters['description'] = 'description';
        $filters['paginate'] = 10;
        
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('');
        $mockClient->shouldReceive('request')->andReturn($response);
        
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($mockClient);
        $return = $attributeRepo->results($filters);

        $this->assertInstanceOf(LengthAwarePaginator::class, $return);
    }
    
    public function testGetKey()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('');
        $mockClient->shouldReceive('request')->andReturn($response);

        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($mockClient);
        $return = $attributeRepo->getKey(1);

        $this->assertEquals($return, null);
    }
    
    public function testUpdateKey()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('');
        $mockClient->shouldReceive('request')->andReturn($response);

        $inputs = ['description' => 'description', 'type' => 'string'];
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($mockClient);
        $return = $attributeRepo->getKey(1, $inputs);

        $this->assertEquals($return, null);
    }
    
    public function testCreateKey()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('');
        $mockClient->shouldReceive('request')->andReturn($response);

        $inputs = ['description' => 'description', 'type' => 'string'];
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($mockClient);
        $return = $attributeRepo->createKey($inputs);

        $this->assertEquals($return, null);
    }
    
    public function testDeleteKey()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('');
        $mockClient->shouldReceive('request')->andReturn($response);

        $inputs = ['description' => 'description', 'type' => 'string'];
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($mockClient);
        $return = $attributeRepo->deleteKey(1);

        $this->assertEquals($return, null);
    }
    
    public function testGetValues()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('');
        $mockClient->shouldReceive('request')->andReturn($response);

        AttributeRepositoryEloquent::setClient($mockClient);
        $return = AttributeRepositoryEloquent::getValues('vehicle', 1);

        $this->assertEquals($return, null);
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
