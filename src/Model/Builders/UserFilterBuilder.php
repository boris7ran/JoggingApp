<?php

namespace App\Model\Builders;

use App\Model\UserFilter;
use DateTime;

class UserFilterBuilder
{
    /**
     * @var int
     */
    private $userId;

    /**
     * @param int $userId
     *
     * @return UserFilterBuilder
     */
    public function setUserId(int $userId): UserFilterBuilder
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return UserFilter
     */
    public function build(): UserFilter
    {
        $user = new UserFilter(
            $this->userId
        );

        return $user;
    }
}
