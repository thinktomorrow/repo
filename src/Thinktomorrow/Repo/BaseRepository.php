<?php

namespace Thinktomorrow\Repo;

use \BadMethodCallException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;

abstract class BaseRepository {

    /**
     * Model instance
     *
     * @var Model
     */
    protected $model;

    /**
     * Original Model instance
     *
     * @var Model
     */
    protected $originalModel;

    /**
     * Allowed Model Methods
     *
     * Whitelist of methods that can be invoked publicly by the repository on the model
     * These should be methods that won't break the model's integration in the repository.
     * e.g. fetching the latest 5 rows: $repo->limit(5)->orderBy('created_at','DESC')->getAll();
     *
     * @var array
     */
    protected $allowedModelMethods = [
        'orderBy', 'limit', 'offset', 'take', 'skip', 'with'
    ];

    /**
     * Paginate our result set
     *
     * @var int
     */
    protected $paginated = null;

    public function __construct(Model $model)
    {
        $this->setModel($model);
    }

    /**
     * Get All records
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->fetch();
    }

    /**
     * Get record by ID
     *
     * @param    int $id
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getById($id)
    {
        return $this->model->where($this->originalModel->getTable() . '.id', $id)->first();
    }

    /**
     * Base fetch for all repository returns
     * This should be used when returning multiple records
     *
     * @param  Model $model - update model instance
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function fetch($model = null)
    {
        if ( !is_null($model) )
        {
            $this->model = $model;
        }

        $result = ($this->paginated && is_int($this->paginated)) ? $this->model->paginate($this->paginated) : $this->model->get();

        if ( $this->paginated )
        {
            // Append custom query string to page links to maintain correct url filter and sorting
            $result = $result->appends($this->buildUrlQuery());
        }

        $this->reset();

        return $result;
    }

    /**
     * Paginate the results
     *
     * @param int $paginated
     * @return $this
     */
    public function paginate($paginated = 25)
    {
        $this->paginated = (int)$paginated;

        return $this;
    }

    /**
     * Rebuild the current url query string for the pagination links
     *
     * @return    array
     */
    protected function buildUrlQuery()
    {
        $query = array_map(function($value){ return is_string($value) ? urlencode($value) : $value; },Input::query());

        // Remove 'own' page query element
        if ( isset($query['page']) ) unset($query['page']);

        return $query;
    }

    /**
     * Reset the Model to its original form
     *
     * @return void
     */
    protected function reset()
    {
        $this->paginated = null;
        if ( !is_null($this->originalModel) ) $this->setModel($this->originalModel);
    }

    /**
     * Assign a model to our Repository
     * This method must be called by the child class constructor at instantiating of the class
     *
     * @param    Model $model
     * @return    void
     */
    protected function setModel(Model $model)
    {
        $this->model = $this->originalModel = $model;
    }

    /**
     * Call to Eloquent model method
     *
     * @param $method
     * @param $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if ( in_array($method, $this->allowedModelMethods) )
        {
            $this->model = call_user_func_array([$this->model, $method], $parameters);

            return $this;
        }

        throw new BadMethodCallException('Method [' . $method . '] does not exist on the [' . get_class($this) . '] repository or its model instance: [' . get_class($this->model) . ']');
    }

}
