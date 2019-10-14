<?php

namespace App\Repository\Interfaces;

use App\Entity\User;

interface UserRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return User
     */
    public function ofId(int $id): ?User;

    /**
     * @param User $user
     *
     * @return User
     */
    public function add(User $user): User;

    /**
     * @param User $user
     */
    public function remove(User $user): void;
}
