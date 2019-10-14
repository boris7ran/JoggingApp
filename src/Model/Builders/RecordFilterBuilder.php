<?php

namespace App\Model\Builders;

use App\Model\RecordFilter;
use DateTime;

class RecordFilterBuilder
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

    /**
     * RecordFilterBuilder constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return RecordFilterBuilder
     */
    public static function valueOf(): RecordFilterBuilder
    {
        return new static();
    }

    /**
     * @param int $userId
     *
     * @return RecordFilterBuilder
     */
    public function userId(int $userId): RecordFilterBuilder
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @param DateTime $startDate
     *
     * @return RecordFilterBuilder
     */
    public function startDate(DateTime $startDate): RecordFilterBuilder
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @param string|null $startDate
     *
     * @return RecordFilterBuilder
     */
    public function startDateText(?string $startDate): RecordFilterBuilder
    {
        if (empty($startDate)) {
            return $this;
        }

        $this->startDate = DateTime::createFromFormat('Y-m-d', $startDate);

        return $this;
    }

    /**
     * @param DateTime $endDate
     *
     * @return RecordFilterBuilder
     */
    public function endDate(DateTime $endDate): RecordFilterBuilder
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @param string|null $endDate
     *
     * @return $this
     */
    public function endDateText(?string $endDate): RecordFilterBuilder
    {
        if (empty($endDate)) {
            return $this;
        }

        $this->endDate = DateTime::createFromFormat('Y-m-d', $endDate);

        return $this;
    }

    /**
     * @return RecordFilterBuilder
     */
    public function firstJogg(): RecordFilterBuilder
    {
        $this->firstJogg = true;
        $this->lastJogg = false;

        return $this;
    }

    /**
     * @return RecordFilterBuilder
     */
    public function lastJogg(): RecordFilterBuilder
    {
        $this->lastJogg = true;
        $this->firstJogg = false;

        return $this;
    }

    /**
     * @return RecordFilter
     */
    public function build()
    {
        $recordFilter = new RecordFilter(
            $this->userId,
            $this->startDate,
            $this->endDate,
            $this->firstJogg,
            $this->lastJogg
        );

        return $recordFilter;
    }
}
