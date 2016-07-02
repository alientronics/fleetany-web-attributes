<?php

namespace Tests\Unit;

use Tests\UnitTestCase;

class KeyModelTest extends UnitTestCase
{

    public function testHasValues()
    {

        $key = factory(\Alientronics\FleetanyWebAttributes\Entities\Key::class)->create();

        $value1 = factory(\Alientronics\FleetanyWebAttributes\Entities\Value::class)->create();

        $value2 = factory(\Alientronics\FleetanyWebAttributes\Entities\Value::class)->create();

        $this->assertEquals(count($key->values), 2);
        $this->assertTrue($key->values->contains($value1));
        $this->assertTrue($key->values->contains($value2));
    }
}
