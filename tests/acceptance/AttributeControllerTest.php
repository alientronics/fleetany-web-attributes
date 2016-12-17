<?php

namespace Tests;

use Tests\AcceptanceTestCase;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Support\MessageBag;

class AttributeControllerTest extends AcceptanceTestCase
{

    private function setEloquentMock($method, $return)
    {
        $mockRepo = \Mockery::mock('Alientronics\FleetanyWebAttributes\Repositories\AttributeRepositoryEloquent');
        $mockRepo->shouldReceive($method)->andReturn($return);

        $this->app->instance('Alientronics\FleetanyWebAttributes\Repositories\AttributeRepositoryEloquent', $mockRepo);
    }

    private function setEloquentMockException($method)
    {
        $messageBag = new MessageBag();
        $mockRepo = \Mockery::mock('Alientronics\FleetanyWebAttributes\Repositories\AttributeRepositoryEloquent');
        $mockRepo->shouldReceive($method)->andThrow(new ValidatorException($messageBag));

        $this->app->instance('Alientronics\FleetanyWebAttributes\Repositories\AttributeRepositoryEloquent', $mockRepo);
    }
    
    public function testIndex()
    {
        $this->setEloquentMock('results', 'entity attributes');
        $this->get('/attribute')->see('entity attributes');
    }

    public function testCreate()
    {
        $this->get('/attribute/create')->see('vehicle');
    }

    public function testEdit()
    {
        $this->setEloquentMock('getKey', (object)['key'=>'entity key']);
        $this->get('/attribute/1/edit')->see('entity key');
    }

    public function testEditException()
    {
        $this->setEloquentMockException('getKey');
        $this->get('/attribute/1/edit')->assertRedirectedTo('/');
    }

    public function testUpdateTrue()
    {
        $this->setEloquentMock('updateKey', true);
        $this->put('/attribute/1')->assertRedirectedTo('attribute', ['message'=>'general.succefullupdate']);
    }

    public function testUpdateFalse()
    {
        $this->setEloquentMock('updateKey', false);
        $this->put('/attribute/1')->assertRedirectedTo('/');
    }

    public function testUpdateFalseException()
    {
        $this->setEloquentMockException('updateKey');
        $this->put('/attribute/1')->assertRedirectedTo('/');
    }

    public function testStoreTrue()
    {
        $this->setEloquentMock('createKey', true);
        $this->post('/attribute')->assertRedirectedTo('attribute', ['message'=>'general.succefullcreate']);
    }

    public function testStoreFalse()
    {
        $this->setEloquentMock('createKey', false);
        $this->post('/attribute')->assertRedirectedTo('/');
    }

    public function testStoreException()
    {
        $this->setEloquentMockException('createKey');
        $this->post('/attribute')->assertRedirectedTo('/');
    }
    
    public function testDestroyTrue()
    {
        $this->setEloquentMock('deleteKey', true);
        $this->delete('/attribute/1')->assertRedirectedTo('attribute', ['message'=>'general.deletedregister']);
    }

    public function testDestroyFalse()
    {
        $this->setEloquentMock('deleteKey', false);
        $this->delete('/attribute/1')->assertRedirectedTo('attribute', ['message'=>'general.deletedregistererror']);
    }

    public function testDestroyFalseException()
    {
        $this->setEloquentMockException('deleteKey');
        $this->delete('/attribute/1')->assertRedirectedTo('/');
    }

    public function testDestroy()
    {
        $this->setEloquentMock('deleteKey', true);
        $this->get('/attribute/destroy/1')->assertRedirectedTo('attribute');
    }

    public function testDownloadSuccess()
    {

        $mockStream = \Mockery::mock('GuzzleHttp\Psr7\Stream')->makePartial();
        $mockStream->shouldReceive('eof')->twice()->andReturn(false, true);
        $mockStream->shouldReceive('read')->once()->andReturn("content");
        
        $this->setEloquentMock('download', $mockStream);
        $this->get('/attribute/download/dGVzdGUudHh0');
        $this->assertEquals($this->response->status(), 200);
    }

    public function testDownloadFailed()
    {
        $mockStream = \Mockery::mock('GuzzleHttp\Psr7\Stream')->makePartial();
        $mockStream->shouldReceive('eof')->once()->andReturn(true);
        
        $this->setEloquentMock('download', $mockStream);
        $this->get('/attribute/download/dGVzdGUudHh0');
        $this->assertEquals($this->response->status(), 404);
    }

    public function testDownloadException()
    {
        $this->setEloquentMockException('download');
        $this->get('/attribute/download/dGVzdGUudHh0')->assertRedirectedTo('/');
    }
}
