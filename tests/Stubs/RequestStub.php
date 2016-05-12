<?php

namespace Thinktomorrow\Repo\Tests\Stubs;

use Illuminate\Http\Request;

class RequestStub extends Request{

    public function input($key = null, $default = null)
    {
        return isset(PayloadStub::$payload[$key]) ? PayloadStub::$payload[$key] : null;
    }
}