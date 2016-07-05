<?php

namespace Tests\Unit;

use Tests\UnitTestCase;
use Alientronics\FleetanyWebAttributes\Repositories\AttributeRepositoryEloquent;
use Illuminate\Pagination\LengthAwarePaginator;
use GuzzleHttp\Client;

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
    
    public function testGetClientWithEmptyClient()
    {
        $return = AttributeRepositoryEloquent::getClient();

        $this->assertInstanceOf(Client::class, $return);
    }
    
    public function testResults()
    {
        $filters = [];
        $filters['paginate'] = 10;
        
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('[]');
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
            ->andReturn('[]');
        $mockClient->shouldReceive('request')->andReturn($response);

        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($mockClient);
        $return = $attributeRepo->getKey(1);

        $this->assertEquals($return, []);
    }
    
    public function testUpdateKeyFailed()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('""');
        $mockClient->shouldReceive('request')->andReturn($response);

        $inputs = ['description' => 'description', 'type' => 'string'];
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($mockClient);
        $return = $attributeRepo->updateKey(1, $inputs);

        $this->assertEquals($return, false);
    }
    
    public function testUpdateKeySuccess()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('"updated"');
        $mockClient->shouldReceive('request')->andReturn($response);

        $inputs = ['description' => 'description', 'type' => 'string'];
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($mockClient);
        $return = $attributeRepo->updateKey(1, $inputs);

        $this->assertEquals($return, true);
    }
    
    public function testCreateKeyFailed()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('""');
        $mockClient->shouldReceive('request')->andReturn($response);

        $inputs = ['description' => 'description', 'type' => 'string'];
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($mockClient);
        $return = $attributeRepo->createKey($inputs);

        $this->assertEquals($return, false);
    }
    
    public function testCreateKeySuccess()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('"created"');
        $mockClient->shouldReceive('request')->andReturn($response);

        $inputs = ['description' => 'description', 'type' => 'string'];
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($mockClient);
        $return = $attributeRepo->createKey($inputs);

        $this->assertEquals($return, true);
    }
    
    public function testDeleteKeyFailed()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('""');
        $mockClient->shouldReceive('request')->andReturn($response);

        $inputs = ['description' => 'description', 'type' => 'string'];
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($mockClient);
        $return = $attributeRepo->deleteKey(1);

        $this->assertEquals($return, false);
    }
    
    public function testDeleteKeySuccess()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('"deleted"');
        $mockClient->shouldReceive('request')->andReturn($response);

        $inputs = ['description' => 'description', 'type' => 'string'];
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($mockClient);
        $return = $attributeRepo->deleteKey(1);

        $this->assertEquals($return, true);
    }
    
    public function testGetValues()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('[]');
        $mockClient->shouldReceive('request')->andReturn($response);

        AttributeRepositoryEloquent::setClient($mockClient);
        $return = AttributeRepositoryEloquent::getValues('vehicle', 1);

        $this->assertEquals($return, []);
    }
    
    public function testSetValues()
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('"created"');
        $mockClient->shouldReceive('request')->andReturn($response);

        $inputs = [];
        $inputs['entity_key'] = 'vehicle';
        $inputs['entity_id'] = 1;
        $inputs['attribute1'] = [1];
        
        AttributeRepositoryEloquent::setClient($mockClient);
        $return = AttributeRepositoryEloquent::setValues($inputs);

        $this->assertEquals($return, "created");
    }
}
