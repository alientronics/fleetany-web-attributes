<?php

namespace Tests;

use Tests\AcceptanceTestCase;

class AttributesTest extends AcceptanceTestCase
{

    public function testList()
    {
        $this->get('/attribute');
        $this->assertEquals($this->response->status(), 401);
    }

    public function testEdit()
    {
        $this->get('/attribute/create');
        $this->assertEquals($this->response->status(), 401);
    }
}
