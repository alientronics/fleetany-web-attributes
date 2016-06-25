<?php

namespace Tests\Unit;

use Tests\UnitTestCase;

class ValueModelTest extends UnitTestCase
{

    public function testHasKey()
    {
    
        $key = factory(\App\Entities\Key::class)->create();
        $value = factory(\App\Entities\Value::class)->create([
            'company_id' => $key->id,
        ]);
    
        $this->assertEquals($value->key->description, $key->description);
    }
    
}
