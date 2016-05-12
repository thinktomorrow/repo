<?php

namespace Thinktomorrow\Repo\Tests;

use Thinktomorrow\Repo\Tests\Stubs\ModelStub;
use Thinktomorrow\Repo\Tests\Stubs\PayloadStub;
use Thinktomorrow\Repo\Tests\Stubs\RepoStub;
use Thinktomorrow\Repo\Tests\Stubs\RequestStub;

class SortableTest extends TestCase {

    private $repo;

    public function setUp()
    {
        parent::setUp();

        $this->repo = new RepoStub(new ModelStub);

        app()->bind('request',RequestStub::class);
    }

    public function tearDown()
    {
        // Reset request payload after each test
        PayloadStub::$payload = [];

        parent::tearDown();
    }

    /** @test */
    public function it_does_set_the_right_sortables()
    {
        $this->repo->sort(['name']);
        $this->assertEquals(['name'],$this->repo->getSortableWhitelist());
    }

    /** @test */
    public function it_does_accept_a_string_sortable()
    {
        $this->repo->sort('name');
        $this->assertEquals(['name'],$this->repo->getSortableWhitelist());
    }

    /** @test */
    public function it_does_ignore_empty_sortable()
    {
        $this->repo->sort(null);
        $this->assertEquals([],$this->repo->getSortableWhitelist());

        $this->repo->sort('');
        $this->assertEquals([],$this->repo->getSortableWhitelist());

        $this->repo->sort([null]);
        $this->assertEquals([],$this->repo->getSortableWhitelist());

        $this->repo->sort(['']);
        $this->assertEquals([],$this->repo->getSortableWhitelist());

        $this->repo->sort(['','foo']);
        $this->assertEquals(['foo'],$this->repo->getSortableWhitelist());

        $this->repo->sort(['','foo','bar',null,'baz']);
        $this->assertEquals(['foo','bar','baz'],$this->repo->getSortableWhitelist());
    }

    /** @test */
    public function it_sets_the_passed_forced_payload()
    {
        $this->repo->sort(null,['foo']);
        $this->assertEquals(['foo'],$this->repo->getSortablePayload());

        $this->repo->sort(null,'foo');
        $this->assertEquals(['foo'],$this->repo->getSortablePayload());
    }

    /** @test */
    public function it_does_ignore_empty_sorter()
    {
        $this->repo->sort(null,null,null);
        $this->assertEquals([],$this->repo->getSortablePayload());

        $this->repo->sort(null,'');
        $this->assertEquals([],$this->repo->getSortablePayload());

        $this->repo->sort(null,[null]);
        $this->assertEquals([],$this->repo->getSortablePayload());

        $this->repo->sort(null,['']);
        $this->assertEquals([],$this->repo->getSortablePayload());

        $this->repo->sort(null,null,['','foo']);
        $this->assertEquals(['foo'],$this->repo->getSortablePayload());
    }

    /** @test */
    public function it_takes_defaults_when_no_sortables_and_sorters_are_set()
    {
        $this->repo->sort(null,null,['foo']);
        $this->assertEquals(['foo'],$this->repo->getSortablePayload());
    }

    /** @test */
    public function it_sets_the_passed_defaults()
    {
        $this->repo->sort(null,null,['foo']);
        $this->assertEquals(['foo'],$this->repo->getSortablePayload());

        $this->repo->sort(null,null,'foo');
        $this->assertEquals(['foo'],$this->repo->getSortablePayload());
    }

    /** @test */
    public function it_does_ignore_empty_default()
    {
        $this->repo->sort(null,null,'');
        $this->assertEquals([],$this->repo->getSortablePayload());

        $this->repo->sort(null,null,[null]);
        $this->assertEquals([],$this->repo->getSortablePayload());

        $this->repo->sort(null,null,['']);
        $this->assertEquals([],$this->repo->getSortablePayload());

        $this->repo->sort(null,null,['','foo']);
        $this->assertEquals(['foo'],$this->repo->getSortablePayload());
    }

    /** @test */
    public function it_can_accept_query_payload()
    {
        PayloadStub::$payload = ['sort' => 'foo'];

        $this->repo->sort('foo');

        $this->assertEquals(['foo'],$this->repo->getSortablePayload());
    }

    /** @test */
    public function it_can_accept_negative_payload()
    {
        PayloadStub::$payload = ['sort' => '-foo'];

        $this->repo->sort('foo');
        $this->assertEquals(['-foo'],$this->repo->getSortablePayload());
    }

    /** @test */
    public function it_can_accept_chained_payload()
    {
        PayloadStub::$payload = ['sort' => 'foo|-bar'];

        $this->repo->sort('foo');
        $this->assertEquals(['foo','-bar'],$this->repo->getSortablePayload());
    }

    /** @test */
    public function it_takes_custom_sorters_over_query_payload()
    {
        PayloadStub::$payload = ['sort' => 'foo'];

        $this->repo->sort('foo',['-bar']);
        $this->assertEquals(['-bar'],$this->repo->getSortablePayload());
    }

}
