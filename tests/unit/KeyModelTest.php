<?php

namespace Tests\Unit;

use Tests\UnitTestCase;

class KeyModelTest extends UnitTestCase
{

    public function testHasValues()
    {

        $key = factory(\App\Key::class)->create();

        $value1 = factory(\App\Value::class)->create();

        $value2 = factory(\App\Value::class)->create();

        $this->assertEquals(count($key->values), 2);
        $this->assertTrue($key->values->contains($value1));
        $this->assertTrue($key->values->contains($value2));
    }
}
