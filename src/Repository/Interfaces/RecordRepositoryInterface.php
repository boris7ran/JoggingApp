<?php

namespace App\Repository\Interfaces;

use App\Entity\Record;
use App\Model\RecordFilter;

interface RecordRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return Record|null
     */
    public function ofId(int $id): ?Record;

    /**
     * @param RecordFilter $filter
     *
     * @return Record[]
     */
    public function filter(RecordFilter $filter): array;

    /**
     * @param Record $record
     *
     * @return Record
     */
    public function add(Record $record): Record;

    /**
     * @param Record $record
     */
    public function remove(Record $record): void;
}
