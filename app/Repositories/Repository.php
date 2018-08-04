<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\RepositoryInterface;

abstract class Repository implements RepositoryInterface
{
    protected $model;
    protected $relations = [];
    protected $countRelations = [];

    /**
     * Set the model of the repository.
     *
     * @param Model $model
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get the instance of the Model.
     *
     * @return Model
     */
    public function model() : Model
    {
        return $this->model;
    }

    /**
     * Set the entire relations array on the model.
     *
     * @param array|string $relations
     */
    public function setRelations($relations) : void
    {
        $this->relations = $relations;
    }

    /**
     * Set the withCount property.
     *
     * @param array|string $countRelations
     */
    public function setWithCount($countRelations) : void
    {
        $this->countRelations = $countRelations;
    }

    /**
     * Find a specific model by Id.
     *
     * @param int $id
     * @param array $columns
     *
     * @return Model|Collection|static[]|static|null
     */
    public function find(int $id, array $columns = ['*'])
    {
        $order = $this->model()
            ->with($this->relations)
            ->withCount($this->countRelations)
            ->find($id, $columns);

        if ($order) {
            return $order;
        }

        return null;
    }

    /**
     * Find a specific model by attribute name.
     *
     * @param string $attribute
     * @param string $value
     * @param array $columns
     *
     * @return Model|null
     */
    public function findBy(
        string $attribute,
        string $value,
        array $columns = ['*']
    )
    {
        return $this->model()
            ->with($this->relations)
            ->withCount($this->countRelations)
            ->where($attribute, $value)
            ->first($columns);
    }

    /**
     * Search for a model or more.
     *
     * @param string $keyword
     * @param string $attribute
     * @param int $limit
     * @param int $offset
     * @param array $columns
     *
     * @return Collection|static[]
     */
    public function search(
        string $keyword,
        string $attribute,
        int $limit = 15,
        int $offset = 0,
        array $columns = ['*']
    ) : Collection
    {
        return $this
            ->model()
            ->with($this->relations)
            ->withCount($this->countRelations)
            ->where($attribute, 'like', '%' . $keyword . '%')
            ->limit($limit)
            ->offset($offset)
            ->get($columns);
    }

    /**
     * Create a new model.
     *
     * @param array $attributes
     *
     * @return Model
     */
    public function create(array $attributes)
    {
        return (new $this->model())->create($attributes);
    }

    /**
     * Get all existing models.
     *
     * @param array $columns
     *
     * @return Collection|static[]
     */
    public function all(array $columns = ['*']) : Collection
    {
        return $this->model()
            ->with($this->relations)
            ->withCount($this->countRelations)
            ->get($columns);
    }

    /**
     * Paginate the existing models.
     *
     * @param int $limit
     * @param int $offset
     * @param array $columns
     *
     * @return Collection|static[]
     */
    public function paginate(
        int $limit = 15,
        int $offset = 0,
        array $columns = ['*']
    ) : Collection
    {
        return $this
            ->model()
            ->with($this->relations)
            ->withCount($this->countRelations)
            ->limit($limit)
            ->offset($offset)
            ->get($columns);
    }

    /**
     * Update a specific model.
     *
     * @param array $attributes
     * @param int $id
     *
     * @return bool
     */
    public function update(array $attributes, int $id) : bool
    {
        $model = $this->find($id);

        if ($model) {
            return $model->update($attributes);
        }

        return false;
    }

    /**
     * Delete a specific model.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id) : bool
    {
        $model = $this->find($id);

        if ($model) {
            return $model->delete();
        }

        return false;
    }
    
    /**
     * Count all models.
     *
     * @return int
     */
    public function count() : int
    {
        return $this->model()->count();
    }
}