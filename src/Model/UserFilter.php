<?php

namespace App\Model;

use DateTime;

class UserFilter
{
    /**
     * @var int
     */
    private $userId;

    /**
     * UserFilter constructor.
     *
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
}