<?php

namespace Thinktomorrow\Repo\Tests;

use Thinktomorrow\Repo\Tests\Stubs\ModelStub;
use Thinktomorrow\Repo\Tests\Stubs\PayloadStub;
use Thinktomorrow\Repo\Tests\Stubs\RepoStub;
use Thinktomorrow\Repo\Tests\Stubs\RequestStub;

class FilterableTest extends TestCase
{
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
    function it_can_set_whitelist_of_filters()
    {
        $this->repo->filter(['foo','bar']);

        $this->assertInternalType('array',$this->repo->getFilterWhitelist());
        $this->assertCount(2,$this->repo->getFilterWhitelist());
        $this->assertEquals(['foo','bar'],$this->repo->getFilterWhitelist());
    }

    /** @test */
    function no_payload_without_active_filters()
    {
        $this->repo->filter(['foo','bar']);

        $this->assertInternalType('array',$this->repo->getFilterPayload());
        $this->assertCount(0,$this->repo->getFilterPayload());
    }

    /** @test */
    function a_whitelisted_filter_shall_take_effect_if_passed_via_request()
    {
        PayloadStub::$payload = ['foo' => 'foz'];

        $this->repo->filter(['foo','bar']);

        $this->assertInternalType('array',$this->repo->getFilterPayload());
        $this->assertCount(1,$this->repo->getFilterPayload());
        $this->assertEquals(['foo' => 'foz'],$this->repo->getFilterPayload());
    }

    /** @test */
    function a_whitelisted_filter_shall_take_effect_if_forced()
    {
        $this->repo->filter(['foo','bar'],['bar' => 'zar']);

        $this->assertInternalType('array',$this->repo->getFilterPayload());
        $this->assertEquals(['bar' => 'zar'],$this->repo->getFilterPayload());
        $this->assertCount(1,$this->repo->getFilterPayload());
    }

    /** @test */
    function forced_filter_has_priority_over_request()
    {
        PayloadStub::$payload = ['bar' => 'foz'];

        $this->repo->filter(['foo','bar'],['bar' => 'zar']);

        $this->assertInternalType('array',$this->repo->getFilterPayload());
        $this->assertEquals(['bar' => 'zar'],$this->repo->getFilterPayload());
        $this->assertCount(1,$this->repo->getFilterPayload());
    }

    /** @test */
    function only_whitelisted_filters_can_take_effect()
    {
        PayloadStub::$payload = ['laz' => 'foz'];

        $this->repo->filter(['foo','bar'],['zap' => 'zar']);

        $this->assertInternalType('array',$this->repo->getFilterPayload());
        $this->assertCount(0,$this->repo->getFilterPayload());
    }

    /** @test */
    function filter_repo_with_filterby_method()
    {
        PayloadStub::$payload = ['foo' => 'foz'];

        $this->repo->filter(['foo','bar'],['zap' => 'zar']);

        // Flag to assert the repo->filterByFoo() is called
        $this->assertTrue($this->repo->filteredByFoo);
    }
}