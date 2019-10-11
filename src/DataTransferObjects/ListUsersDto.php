<?php

namespace App\DataTransferObjects;

use App\Entity\User;

class ListUsersDto
{
    /**
     * @var User[] $users
     */
    private $users;

    /**
     * ListUsersDto constructor.
     *
     * @param array $users
     */
    public function __construct(array $users)
    {
        foreach ($users as $user) {
            $this->users[] = new UserDto($user);
        }
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }


}
