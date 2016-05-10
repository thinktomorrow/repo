<?php

namespace Thinktomorrow\Repo\Traits;

use Illuminate\Support\Facades\Input;

trait Sortable
{

    /**
     * Url query parameter key for sorting
     *
     * @var string
     */
    private $sortableKey = 'sort';

    /**
     * Delimiter of chained sort string,
     * e.g. ?sort=foo|bar
     *
     * @var string
     */
    private $delimiter = '|';

    /**
     * Collection of allowed keys to sort on
     *
     * @var array
     */
    private $sortables;

    /**
     * Key that should be used to sort the current Query
     *
     * @var array
     */
    private $sorters;

    /**
     * Enable sort
     *
     * @param array $sortables - set of allowed sortable parameters
     * @param array $defaults  - default sorters - used when no sorters are in effect
     * @param array $sorters   - forced sorters - used when the query payload is empty
     * @return $this
     */
    public function sort($sortables = null, $defaults = [], $sorters = [])
    {
        $this->sortables = $this->convertToCleanArray($sortables);

        $this->sorters = $this->getSorters($defaults, $sorters);

        $this->handleSortablePayload();

        return $this;
    }

    /**
     * Get our current sorters
     *
     * Sorters        custom sorting payload which has priority
     * uriSorters    By default the sorters provided via uri payload are used
     * defaults    if none are given, a default sorter can be activated
     *
     * @param  array $defaults
     * @param  array $sorters
     * @return array
     */
    protected function getSorters($defaults = [], $sorters = [])
    {
        if (count($sorters = $this->convertToCleanArray($sorters)) > 0) {
            return $sorters;
        }

        if (count($uri_sorters = $this->convertToCleanArray(Input::get($this->sortableKey))) > 0) {
            return $uri_sorters;
        }

        return $this->convertToCleanArray($defaults);
    }

    /**
     * Process the sorting payload
     *
     * If the url query contains the sort key, the belonging values are taken into account for sorting.
     * multiple values are separated by a comma.
     * Default is Ascending sort, a minus before a value depicts a descending direction
     *
     * @return    void
     */
    protected function handleSortablePayload()
    {
        if (empty($this->sortables) or empty($this->sorters)) {
            return;
        }

        foreach ($this->sorters as $sorter) {
            list($order, $sorter) = $this->getSorterAndOrderFromQueryString($sorter);

            if (false !== ($key = array_search($sorter, $this->sortables))) {
                $this->addSortQuery($key, $sorter, $order);
            }
        }
    }

    /**
     * @param $key
     * @param $sorter
     * @param $order
     */
    protected function addSortQuery($key, $sorter, $order)
    {
        $sortable = $this->sortables[$key];

        if (method_exists($this, 'sortBy' . ucfirst($sortable))) {
            call_user_func_array(array($this, 'sortBy' . ucfirst($sortable)), array($sorter, $order));
        } else {
            $this->model = $this->model->orderBy($sortable, $order);
        }
    }

    /**
     * @param $sorter
     * @return array
     */
    protected function getSorterAndOrderFromQueryString($sorter)
    {
        if (0 === strpos($sorter, '-')) {
            return array('DESC', substr($sorter, 1));
        }

        return array('ASC', $sorter);
    }

    private function convertToCleanArray($values)
    {
        $values = (array)$values;

        // Remove empty values for our value array
        $values = array_filter($values, function ($v) {
            return ($v);
        });

        // Key pointers must stay nicely indexed so rebase the keys
        $values = array_values($values);

        foreach ($values as $k => $v) {
            if (false !== strpos($v, $this->delimiter)) {
                $split_values = explode($this->delimiter, $v);

                // Inject the delimited values into the main array
                array_splice($values, $k, 1, $split_values);
            }
        }

        return array_values($values);
    }
}
