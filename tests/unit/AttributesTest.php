<?php

namespace Tests\Unit;

use Tests\UnitTestCase;
use Alientronics\FleetanyWebAttributes\Repositories\AttributeRepositoryEloquent;
use Illuminate\Pagination\LengthAwarePaginator;
use GuzzleHttp\Client;

class AttributesTest extends UnitTestCase
{

    private function setGuzzleMock($return)
    {
        $mockClient = \Mockery::mock('\GuzzleHttp\Client');
        $response = \Mockery::mock('GuzzleHttp\ResponseInterface');
        $response->shouldReceive('getBody')
            ->andReturn($return);
        $mockClient->shouldReceive('request')->andReturn($response);
        
        return $mockClient;
    }

    public function testHasVehicle()
    {
        AttributeRepositoryEloquent::setClient($this->setGuzzleMock(''));
        $return = AttributeRepositoryEloquent::getAttributes(1);
    
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
        
        $object = new \stdClass();
        $object->id = 1;
        $object->attribute_id = 1;
        $object->value = 1;
        $object->company_id = 1;
        $object->entity_key = 'vehicle';
        $object->description = 'description';
        $object->type = 'select';
        $object->options = 'first option,second option';
        $returnMockClient[] = $object;
        $object->type = 'checkbox';
        $returnMockClient[] = $object;
        
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($this->setGuzzleMock(json_encode($returnMockClient)));
        $return = $attributeRepo->results($filters);

        $this->assertInstanceOf(LengthAwarePaginator::class, $return);
    }
    
    public function testEmptyResults()
    {
        $filters = [];
        $filters['paginate'] = 10;
        
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($this->setGuzzleMock(''));
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
        $inputs = ['description' => 'description', 'type' => 'string'];
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($this->setGuzzleMock('""'));
        $return = $attributeRepo->updateKey(1, $inputs);

        $this->assertEquals($return, false);
    }
    
    public function testUpdateKeySuccess()
    {
        $inputs = ['description' => 'description', 'type' => 'string'];
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($this->setGuzzleMock('"updated"'));
        $return = $attributeRepo->updateKey(1, $inputs);

        $this->assertEquals($return, true);
    }
    
    public function testCreateKeyFailed()
    {
        $inputs = ['description' => 'description', 'type' => 'string'];
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($this->setGuzzleMock('""'));
        $return = $attributeRepo->createKey($inputs);

        $this->assertEquals($return, false);
    }
    
    public function testCreateKeySuccess()
    {
        $inputs = ['description' => 'description', 'type' => 'string'];
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($this->setGuzzleMock('"created"'));
        $return = $attributeRepo->createKey($inputs);

        $this->assertEquals($return, true);
    }
    
    public function testDeleteKeyFailed()
    {
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($this->setGuzzleMock('""'));
        $return = $attributeRepo->deleteKey(1);

        $this->assertEquals($return, false);
    }
    
    public function testDeleteKeySuccess()
    {
        $attributeRepo = new AttributeRepositoryEloquent();
        $attributeRepo->setClient($this->setGuzzleMock('"deleted"'));
        $return = $attributeRepo->deleteKey(1);

        $this->assertEquals($return, true);
    }
    
    public function testGetValues()
    {
        AttributeRepositoryEloquent::setClient($this->setGuzzleMock('[]'));
        $return = AttributeRepositoryEloquent::getValues('vehicle', 1);

        $this->assertEquals($return, []);
    }
    
    public function testSetValues()
    {
        $inputs = [];
        $inputs['entity_key'] = 'vehicle';
        $inputs['entity_id'] = 1;
        $inputs['attribute1'] = [1];
        
        AttributeRepositoryEloquent::setClient($this->setGuzzleMock('"created"'));
        $return = AttributeRepositoryEloquent::setValues($inputs);

        $this->assertEquals($return, "created");
    }
    
    public function testGetAttributesWithValuesEmptyAttributes()
    {
        AttributeRepositoryEloquent::setClient($this->setGuzzleMock(''));
        $return = AttributeRepositoryEloquent::getAttributesWithValues('vehicle', 1);

        $this->assertEquals($return, []);
    }
    
    public function testGetAttributesWithValuesWithAttributes()
    {
        $returnMockClient = [];
        
        $object = new \stdClass();
        $object->id = 1;
        $object->attribute_id = 1;
        $object->value = 1;
        $object->company_id = 1;
        $object->entity_key = 'vehicle';
        $object->description = 'description';
        $object->type = 'select';
        $object->options = 'first option,second option';
        $returnMockClient[] = $object;
        
        $object = new \stdClass();
        $object->id = 1;
        $object->attribute_id = 1;
        $object->value = 1;
        $object->company_id = 1;
        $object->entity_key = 'vehicle';
        $object->description = 'description';
        $object->options = 'first option,second option';
        $object->type = 'checkbox';
        $returnMockClient[] = $object;
        
        AttributeRepositoryEloquent::setClient($this->setGuzzleMock(json_encode($returnMockClient)));
        $return = AttributeRepositoryEloquent::getAttributesWithValues('vehicle', 1);

        $this->assertEquals(count($return), 2);
    }
    
    public function testGetAttributesWithValuesWithoutEntityId()
    {
        $returnMockClient = [];
        $object = new \stdClass();
        $object->id = 1;
        $object->attribute_id = 0;
        $object->value = 1;
        $object->company_id = 1;
        $object->entity_key = 'vehicle';
        $object->description = 'description';
        $object->type = 'select';
        $object->options = '';
        $returnMockClient[] = $object;
        
        $object = new \stdClass();
        $object->id = 1;
        $object->attribute_id = 0;
        $object->value = 1;
        $object->company_id = 1;
        $object->entity_key = 'vehicle';
        $object->description = 'description';
        $object->options = '';
        $object->type = 'checkbox';
        $returnMockClient[] = $object;
        
        $object = new \stdClass();
        $object->id = 1;
        $object->attribute_id = 0;
        $object->value = 1;
        $object->company_id = 1;
        $object->entity_key = 'vehicle';
        $object->description = 'description';
        $object->options = '';
        $object->type = 'string';
        $returnMockClient[] = $object;
        
        AttributeRepositoryEloquent::setClient($this->setGuzzleMock(json_encode($returnMockClient)));
        $return = AttributeRepositoryEloquent::getAttributes('vehicle');

        $this->assertEquals(count($return), 3);
    }
}
