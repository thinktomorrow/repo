<?php

namespace Thinktomorrow\Repo;

use Illuminate\Support\Facades\Input;

trait Filterable{

	/**
	 * Contains the allowed filters
	 *
	 * @var array
	 */
	private $filterWhitelist = [];

    /**
     * Active filters in effect of current query request
     *
     * @var array
     */
    private $filterPayload;

    /**
     * Enable filter
     *
     * @param array $filterWhitelist
     * @param array $forcedPayload
     * @return $this
     */
	public function filter( array $filterWhitelist = null, $forcedPayload = [] )
	{
		if(!is_null($filterWhitelist)) $this->filterWhitelist = $filterWhitelist;

        $this->filterPayload = $this->determineFilterPayload($forcedPayload);

		$this->handleFilterPayload();

		return $this;
	}

    /**
     * @param array $directPayload
     * @return array
     */
    private function determineFilterPayload($directPayload = [])
    {
        $filterPayload = [];

        foreach($this->filterWhitelist as $filterable)
        {
            // Custom filters take priority
            if(array_key_exists($filterable,$directPayload))
            {
                $filterPayload[$filterable] = $directPayload[$filterable];
                continue;
            }

            if( false != ($value = Input::get($filterable)) )
            {
                $filterPayload[$filterable] = $value;
            }
        }

        return $filterPayload;
    }

    /**
     * Process the filter payload.
     *
     * The input is scanned for a viable filter payload.
     * If so, these filters are taken into account for the constructed query
     * FilterBy       The Repository can contain FilterBy... methods by which it can deliver a specific querybuild to the model
     *                These methods should not return anything but instead chain their custom query to the model property.
     * Text search    By default the filter will assume a tablefield is searched upon
     *                A non-strict text search is applied
     *
     */
	private function handleFilterPayload()
	{
		foreach($this->filterPayload as $filter => $value)
		{
			$this->addFilterQuery($filter, $value);
		}
	}

    /**
     * @param $filter
     * @param $value
     */
    private function addFilterQuery($filter, $value)
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
