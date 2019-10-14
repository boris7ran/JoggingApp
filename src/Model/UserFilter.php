<?php

namespace App\Model;

class UserFilter
{
    /**
     * @var array
     */
    private $roles;

    /**
     * UserFilter constructor.
     *
     * @param array|null $roles
     */
    public function __construct(array $roles = null)
    {
        $this->roles = $roles;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }
}