<?php

namespace App\DataTransferObjects;

use App\Entity\Record;
use DateTime;

class RecordDto
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var int
     */
    private $time;

    /**
     * @var int
     */
    private $distance;

    /**
     * @var UserDto
     */
    private $user;

    /**
     * RecordDto constructor.
     *
     * @param Record $record
     */
    public function __construct(Record $record)
    {
        $this->id = $record->getId();
        $this->date = $record->getDate();
        $this->time = $record->getTime();
        $this->distance = $record->getDistance();
        $this->user = new UserDto($record->getUser());
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @return int
     */
    public function getDistance(): int
    {
        return $this->distance;
    }

    /**
     * @return UserDto
     */
    public function getUser(): UserDto
    {
        return $this->user;
    }
}
