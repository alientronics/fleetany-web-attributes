<?php

namespace Tests\Unit;

use Tests\UnitTestCase;

class ValueModelTest extends UnitTestCase
{

    public function testHasKey()
    {
    
        $key = factory(\Alientronics\FleetanyWebAttributes\Entities\Key::class)->create();
        $value = factory(\Alientronics\FleetanyWebAttributes\Entities\Value::class)->create();
    
        $this->assertEquals($value->key->description, $key->description);
    }
}
