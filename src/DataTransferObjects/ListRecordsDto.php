<?php

namespace App\DataTransferObjects;

use App\Entity\Record;

class ListRecordsDto
{
    /**
     * @var RecordDto[]
     */
    private $records;

    /**
     * ListRecordsDto constructor.
     *
     * @param Record[] $records
     */
    public function __construct(array $records)
    {
        $this->records = [];
        foreach ($records as $record) {
            $this->records[] = new RecordDto($record);
        }
    }

    /**
     * @return RecordDto[]
     */
    public function getRecords(): array
    {
        return $this->records;
    }
}
