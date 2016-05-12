<?php

namespace Thinktomorrow\Repo;

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
    private $sortDelimiter = '|';

    /**
     * Collection of allowed keys to sort on
     *
     * @var array
     */
    private $sortableWhitelist;

    /**
     * Key that should be used to sort the current Query
     *
     * @var array
     */
    private $sortablePayload;

    /**
     * Enable sort
     *
     * @param array $sortableWhitelist - whitelist of sortable parameters
     * @param array $forcedPayload - enforce these sorters
     * @param array $defaultPayload - default sorting if no sorting is passed
     * @return $this
     */
    public function sort($sortableWhitelist = null, $forcedPayload = [], $defaultPayload = [])
    {
        $this->sortableWhitelist = $this->convertToCleanArray($sortableWhitelist);

        $this->sortablePayload = $this->setSortablePayload($forcedPayload, $defaultPayload);

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
     * @param  array $forcedPayload
     * @param array $defaultPayload
     * @return array
     */
    protected function setSortablePayload($forcedPayload = [], $defaultPayload = [])
    {
        if (count($forced = $this->convertToCleanArray($forcedPayload)) > 0) {
            return $forced;
        }

        if (count($uri_sorters = $this->convertToCleanArray(Input::get($this->sortableKey))) > 0) {
            return $uri_sorters;
        }

        return $this->convertToCleanArray($defaultPayload);
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
        if (empty($this->sortableWhitelist) or empty($this->sortablePayload)) {
            return;
        }

        foreach ($this->sortablePayload as $sorter) {
            list($sorter, $order) = $this->getSorterAndOrderFromQueryString($sorter);

            if (false !== ($key = array_search($sorter, $this->sortableWhitelist))) {
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
        $sortable = $this->sortableWhitelist[$key];

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
            return array(substr($sorter, 1), 'DESC');
        }

        return array($sorter, 'ASC');
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
            if (false !== strpos($v, $this->sortDelimiter)) {
                $split_values = explode($this->sortDelimiter, $v);

                // Inject the delimited values into the main array
                array_splice($values, $k, 1, $split_values);
            }
        }

        return array_values($values);
    }
}
