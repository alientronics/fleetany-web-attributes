<?php

namespace Tests\Unit;

use Tests\UnitTestCase;

class ValueModelTest extends UnitTestCase
{

    public function testHasKey()
    {
    
        $key = factory(\App\Key::class)->create();
        $value = factory(\App\Value::class)->create();
    
        $this->assertEquals($value->key->description, $key->description);
    }
}
