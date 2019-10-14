<?php

namespace App\DataTransferObjects;

class ListUsersDto
{
    /**
     * @var UserDto[] $users
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
     * @return UserDto[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }
}
