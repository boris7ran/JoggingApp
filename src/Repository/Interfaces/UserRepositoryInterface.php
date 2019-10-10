<?php

namespace App\Repository\Interfaces;

use App\Entity\User;
use App\Model\RepositoryFilter;

interface UserRepositoryInterface
{
    public function ofId(int $id);
    public function add(User $user);
    public function remove(User $user);
    public function filter(RepositoryFilter $filter);
}