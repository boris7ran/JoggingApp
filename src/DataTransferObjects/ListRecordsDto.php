<?php

namespace App\DataTransferObjects;

use App\Entity\Record;

class ListRecordsDto
{
    /**
     * @var Record[]
     */
    private $records;

    /**
     * ListRecordsDto constructor.
     *
     * @param Record[] $records
     */
    public function __construct(array $records)
    {
        foreach ($records as $record) {
            $this->records[] = new RecordDto($record);
        }
    }

    /**
     * @return Record[]|null
     */
    public function getRecords(): ?array
    {
        return $this->records;
    }
}
