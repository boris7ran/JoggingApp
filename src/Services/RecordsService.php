<?php

namespace App\Services;

use App\DataTransferObjects\ListRecordsDto;
use App\DataTransferObjects\RecordDto;
use App\Entity\Record;
use App\Model\Builders\RecordFilterBuilder;
use App\Repository\RecordRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Exception;

class RecordsService
{
    /**
     * @var RecordRepository $recordRepo
     */
    private $recordRepo;
    /**
     * @var UserRepository $userRepo
     */
    private $userRepo;

    /**
     * RecordsService constructor.
     *
     * @param RecordRepository $recordRepo
     * @param UserRepository $userRepo
     */
    public function __construct(
        RecordRepository $recordRepo,
        UserRepository $userRepo
    ) {
        $this->recordRepo = $recordRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * @param int $userId
     * @param string|null $startDate
     * @param string|null $endDate
     *
     * @return ListRecordsDto
     */
    public function getUserRecords(
        int $userId,
        string $startDate = null,
        string $endDate = null
    ): ListRecordsDto {
        $filter = RecordFilterBuilder::valueOf()
            ->startDateText($startDate)
            ->endDateText($endDate)
            ->userId($userId)
            ->build();
        $records = $this->recordRepo->filter($filter);

        return new ListRecordsDto($records);
    }

    /**
     * @param int $id
     *
     * @return RecordDto
     */
    public function getUserRecord(int $id): RecordDto
    {
        $record = $this->recordRepo->ofId($id);

        return new RecordDto($record);
    }

    /**
     * @param DateTime $date
     * @param int $time
     * @param int $distance
     * @param int $userId
     *
     * @return RecordDto
     *
     * @throws ORMException
     */
    public function storeNewRecord(
        DateTime $date,
        int $time,
        int $distance,
        int $userId
    ): RecordDto {
        $user = $this->userRepo->find($userId);
        $record = $user->addRecord($date, $time, $distance);
        $record = $this->recordRepo->add($record);

        return new RecordDto($record);
    }

    /**
     * @param int $recordId
     *
     * @return RecordDto|null
     *
     * @throws NonUniqueResultException
     */
    public function getRecord(int $recordId): ?RecordDto
    {
        $recordDto = new RecordDto($this->recordRepo->ofId($recordId));

        return $recordDto;
    }

    /**
     * @param DateTime $date
     * @param int $time
     * @param int $distance
     * @param int $recordId
     *
     * @return RecordDto
     *
     * @throws ORMException
     */
    public function editRecord(DateTime $date, int $time, int $distance, int $recordId): RecordDto
    {
        $record = $this->recordRepo->find($recordId);
        $record->setDate($date);
        $record->setDistance($distance);
        $record->setTime($time);

        $this->recordRepo->add($record);

        return new RecordDto($record);
    }

    /**
     * @param int $recordId
     *
     * @return RecordDto
     */
    public function deleteRecord(int $recordId): RecordDto
    {
        $record = $this->recordRepo->ofId($recordId);
        $recordDto = new RecordDto($record);
        $this->recordRepo->remove($record);

        return $recordDto;
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws Exception
     */
    public function makeReports(int $id): array
    {
        $builder = new RecordFilterBuilder();
        $filter = $builder->userId($id)->firstJogg()->build();

        $firstJogg = $this->recordRepo->filter($filter)[0]->getDate();

        $filter = $builder->lastJogg()->build();
        $lastJogg = $this->recordRepo->filter($filter)[0]->getDate();

        if (!$firstJogg) {
            return [];
        }

        $firstDayWeek = DateService::getStartDate($firstJogg);
        $reports = [];
        $weeklyBuilder = new RecordFilterBuilder();

        for ($i = $firstDayWeek; $i < $lastJogg; $i->modify('+7 days')) {
            $lastDayOfCurrentWeek = DateService::getEndDate($i);
            $filter = $weeklyBuilder->userId($id)->startDate($i)->endDate($lastDayOfCurrentWeek)->build();
            $records = $this->recordRepo->filter($filter);

            if ($records) {
                $report = $this->calculateAverage($records);
                $report["week"] = $i->format("W");
                $reports[] = $report;
            }
        }

        return $reports;
    }

    /**
     * @param Record[] $records
     *
     * @return array
     */
    public function calculateAverage(array $records): array
    {
        $averageDistance = 0;
        $averageTime = 0;

        foreach ($records as $record) {
            $averageTime += $record->getTime();
            $averageDistance += $record->getDistance();
        }

        $averageTime = $averageTime/count($records);
        $averageDistance = $averageDistance/count($records);

        return [
            'averageDistance' => $averageDistance,
            "averageTime" => $averageTime
        ];
    }
}
