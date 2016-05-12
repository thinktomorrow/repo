<?php

namespace Thinktomorrow\Repo\Tests\Stubs;

use Thinktomorrow\Repo\BaseRepository;
use Thinktomorrow\Repo\Filterable;
use Thinktomorrow\Repo\Sortable;

class RepoStub extends BaseRepository{

    use Filterable;
    use Sortable;

    protected $model;
    public $filteredByFoo = false;

    public function __construct(ModelStub $model)
    {
        $this->setModel($model);
    }

    // Access to private properties for our unit tests
    public function getFilterWhitelist(){ return $this->filterWhitelist; }
    public function getFilterPayload(){ return $this->filterPayload; }
    public function getSortableWhitelist(){ return $this->sortableWhitelist; }
    public function getSortablePayload(){ return $this->sortablePayload; }

    public function filterByFoo(){ $this->filteredByFoo = true; }
}