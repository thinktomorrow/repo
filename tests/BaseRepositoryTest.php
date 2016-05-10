<?php

namespace Thinktomorrow\Repo\Tests;

use Mockery;
use Illuminate\Database\Eloquent\Model;
use Thinktomorrow\Repo\BaseRepository;

class BaseRepositoryTest extends TestCase
{
    /** @test */
    function it_is_an_abstract_class()
    {
        $abstract = new \ReflectionClass(BaseRepository::class);

        $this->assertTrue($abstract->isAbstract());
    }

    /**
     * @expectedException Exception
     */
    public function test_must_set_model_on_instance()
    {
        new ChildRepository();
    }

    public function test_can_call_getAll()
    {
        $m = Mockery::mock(ChildEntity::class);
        $m->shouldReceive('get')->once()->andReturn('foo');

        $result = (new ChildRepository($m))->getAll();

        $this->assertEquals('foo',$result);
    }

    public function test_can_call_getById()
    {
        $m = Mockery::mock(ChildEntity::class);

        $m->shouldReceive('getTable')->once()->andReturn('table');
        $m->shouldReceive('where')->with("table.id",1)->once()->andReturn($m);
        $m->shouldReceive('first')->once()->andReturn('foo');

        $result = (new ChildRepository($m))->getById(1);

        $this->assertEquals('foo',$result);
    }

    public function test_fetch_equals_getAll()
    {
        $m = Mockery::mock(ChildEntity::class);

        $m->shouldReceive('get')->times(1)->andReturn('foo');

        $this->assertEquals('foo',(new ChildRepository($m))->getAll());
    }

    /**
     * The paginate option return the Paginated result
     * along with the current query from Input
     */
    public function test_can_call_paginate()
    {
        $m = Mockery::mock(ChildEntity::class);

        $m->shouldReceive('paginate')->once()->andReturn($m);
        $m->shouldReceive('appends')->once()->andReturn('foo');
        $m->shouldReceive('get')->never();

        $result = (new ChildRepository($m))->paginate(5)->getAll();

        $this->assertEquals('foo',$result);
    }

}

class ChildEntity extends Model{}

class ChildRepository extends BaseRepository{

    public function __construct(ChildEntity $model)
    {
        $this->setModel($model);
    }
}

// Pagination depends on input
class Input{

    public static function query()
    {
        return ['a' => 'foo'];
    }
}