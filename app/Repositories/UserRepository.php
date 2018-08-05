<?php

namespace App\Repositories;

use App\User;

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