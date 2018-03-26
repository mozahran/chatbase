<?php

namespace App\Repositories;

use App\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends Repository
{
    /**
     * UserRepository constructor.
     */
    public function __construct()
    {
        $this->setModel(app(User::class));
    }
}