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
}
