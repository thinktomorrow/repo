<?php

namespace Thinktomorrow\Repo\Tests;

use Mockery;
use Thinktomorrow\Repo\BaseRepository;
use Thinktomorrow\Repo\Tests\Stubs\ModelStub;
use Thinktomorrow\Repo\Tests\Stubs\RepoStub;

class BaseRepositoryTest extends TestCase
{
    /** @test */
    function it_is_an_abstract_class()
    {
        $abstract = new \ReflectionClass(BaseRepository::class);

        $this->assertTrue($abstract->isAbstract());
    }

    public function test_can_call_getAll()
    {
        $m = Mockery::mock(ModelStub::class);
        $m->shouldReceive('get')->once()->andReturn('foo');

        $result = (new RepoStub($m))->getAll();

        $this->assertEquals('foo',$result);
    }

    public function test_can_call_getById()
    {
        $m = Mockery::mock(ModelStub::class);

        $m->shouldReceive('getTable')->once()->andReturn('table');
        $m->shouldReceive('where')->with("table.id",1)->once()->andReturn($m);
        $m->shouldReceive('first')->once()->andReturn('foo');

        $result = (new RepoStub($m))->getById(1);

        $this->assertEquals('foo',$result);
    }

    public function test_fetch_equals_getAll()
    {
        $m = Mockery::mock(ModelStub::class);

        $m->shouldReceive('get')->times(1)->andReturn('foo');

        $this->assertEquals('foo',(new RepoStub($m))->getAll());
    }

    /**
     * The paginate option return the Paginated result
     * along with the current query from Input
     */
    public function test_can_call_paginate()
    {
        $m = Mockery::mock(ModelStub::class);

        $m->shouldReceive('paginate')->once()->andReturn($m);
        $m->shouldReceive('appends')->once()->andReturn('foo');
        $m->shouldReceive('get')->never();

        $result = (new RepoStub($m))->paginate(5)->getAll();

        $this->assertEquals('foo',$result);
    }

}