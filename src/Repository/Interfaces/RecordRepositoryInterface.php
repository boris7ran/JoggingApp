<?php

namespace App\Repository\Interfaces;

use App\Entity\Record;
use App\Model\RepositoryFilter;

interface RecordRepositoryInterface
{
    public function ofId(int $id);
    public function add(Record $record);
    public function remove(Record $record);
    public function filter(RepositoryFilter $filter);
}