<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface RepositoryInterface
{
    /**
     * Find a specific model by Id.
     *
     * @param int   $id
     * @param array $columns
     * @return Model|null
     */
    public function find(int $id, array $columns = ['*']);

    /**
     * Find a specific model by attribute name.
     *
     * @param string $attribute
     * @param string $value
     * @param array  $columns
     *
     * @return Model|null
     */
    public function findBy(
        string $attribute,
        string $value,
        array $columns = ['*']
    );

    /**
     * Search for a model or more.
     *
     * @param string $keyword
     * @param string $attribute
     * @param int    $limit
     * @param int    $offset
     * @param array  $columns
     *
     * @return Collection
     */
    public function search(
        string $keyword,
        string $attribute,
        int $limit = 15,
        int $offset = 0,
        array $columns = ['*']
    ) : Collection;

    /**
     * Create a new model.
     *
     * @param array $attributes
     * @return Model|false
     */
    public function create(array $attributes);

    /**
     * Get all existing models.
     *
     * @param array $columns
     *
     * @return Collection
     */
    public function all(array $columns = ['*']) : Collection;

    /**
     * Paginate the existing models.
     *
     * @param int   $limit
     * @param int   $offset
     * @param array $columns
     *
     * @return Collection
     */
    public function paginate(
        int $limit = 15,
        int $offset = 0,
        array $columns = ['*']
    ) : Collection;

    /**
     * Update a specific model.
     *
     * @param array $attributes
     * @param int   $id
     *
     * @return bool
     */
    public function update(array $attributes, int $id) : bool;

    /**
     * Delete a specific model.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id) : bool;

    /**
     * Count all models.
     *
     * @return int
     */
    public function count() : int;
}