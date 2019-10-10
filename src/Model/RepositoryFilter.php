<?php

namespace App\Model;

use DateTime;

class RepositoryFilter
{
    /**
     * @var int
     */
    private $userId;
    /**
     * @var DateTime
     */
    private $startDate;
    /**
     * @var DateTime
     */
    private $endDate;
    /**
     * @var bool
     */
    private $firstJogg;
    /**
     * @var bool
     */
    private $lastJogg;

    public function __construct(int $userId, DateTime $startDate = null, DateTime $endDate = null, bool $firstJogg = null, bool $lastJogg = null)
    {
        $this->userId = $userId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->firstJogg = $firstJogg;
        $this->lastJogg = $lastJogg;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return DateTime|null
     */
    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @return bool|null
     */
    public function isFirstJogg(): ?bool
    {
        return $this->firstJogg;
    }

    /**
     * @return bool|null
     */
    public function isLastJogg(): ?bool
    {
        return $this->lastJogg;
    }

}