<?php

namespace Thinktomorrow\Repo\Traits;

use Illuminate\Support\Facades\Input;

trait Filterable{

	/**
	 * Contains the allowed filters
	 *
	 * @var array
	 */
	public $filterables = [];

    /**
     * List of actual used filters
     *
     * @var array
     */
    public $filters;

    /**
     * Enable filter
     *
     * @param array $filterables
     * @param array $filters
     * @return $this
     */
	public function filter( array $filterables = null,$filters = [] )
	{
		if(!is_null($filterables)) $this->filterables = $filterables;

        $this->filters = $this->getFilters($filters);

		$this->handleFilterablePayload();

		return $this;
	}

    /**
     * @param array $filters
     * @return array
     */
    protected function getFilters($filters = [])
    {
        $payload = [];

        foreach($this->filterables as $filterable)
        {
            // Custom filters take priority
            if(array_key_exists($filterable,$filters))
            {
                $payload[$filterable] = $filters[$filterable];
                continue;
            }

            if( false != ($value = Input::get($filterable)) )
            {
                $payload[$filterable] = $value;
            }
        }

        return $payload;
    }

    /**
     * Process the filters
     * The input is scanned for a viable filter payload.
     * If so, these filters are taken into account for the constructed query
     * FilterBy    The Repository can contain FilterBy... methods by which it can deliver a specific querybuild to the model
     *                These methods should not return anything but instead chain their custom query to the model property.
     * Text search    By default the filter will assume a tablefield is searched upon
     *                A non-strict text search is applied
     *
     */
	protected function handleFilterablePayload()
	{
		foreach($this->filters as $filter=>$value)
		{
			$this->addFilterQuery($filter, $value);
		}
	}

    /**
     * @param $filter
     * @param $value
     */
    protected function addFilterQuery($filter, $value)
    {
        if ( method_exists($this, 'filterBy' . ucfirst($filter)) )
        {
            call_user_func_array(array($this, 'filterBy' . ucfirst($filter)), array($value));
        }

        else
        {
            $this->model = $this->model->where($filter, 'LIKE', '%' . $value . '%');
        }
    }

}
