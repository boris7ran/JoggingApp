<?php

namespace App\Model\Builders;

use App\Model\UserFilter;

class UserFilterBuilder
{
    /**
     * @var array
     */
    private $roles;

    public function __construct()
    {
    }

    /**
     * @return $this
     */
    public function admins()
    {
        $this->roles = ['ROLE_ADMIN'];

        return $this;
    }

    /**
     * @return $this
     */
    public function managers()
    {
        $this->roles = ['ROLE_MANAGER'];

        return $this;
    }

    /**
     * @return $this
     */
    public function users()
    {
        $this->roles = ['ROLE_USER'];

        return $this;
    }

    /**
     * @return UserFilter
     */
    public function build(): UserFilter
    {
        $userFilter = new UserFilter($this->roles);

        return $userFilter;
    }
}