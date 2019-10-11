<?php

namespace App\Services;

use App\DataTransferObjects\ListRecordsDto;
use App\DataTransferObjects\RecordDto;
use App\DataTransferObjects\UserDto;
use App\Entity\Record;
use App\Model\Builders\RecordFilterBuilder;
use App\Repository\RecordRepository;
use App\Repository\UserRepository;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
     * @param int|null $userId
     * @param string|null $startDate
     * @param string|null $endDate
     *
     * @return ListRecordsDto
     *
     * @throws Exception
     */
    public function getUserRecords(
        int $userId,
        string $startDate = null,
        string $endDate = null
    ): ListRecordsDto
    {
        $filter = RecordFilterBuilder::valueOf()
            ->startDateText($startDate)
            ->endDateText($endDate)
            ->userId($userId)
            ->build();
        $records = $this->recordRepo->filter($filter);

        return new ListRecordsDto($records);
    }

    /**
     * @param Request $request
     * @param int $id
     */
    public function storeNewRecord(Request $request, int $id)
    {
        $this->parseRecordRequest($request, null, $id);
    }

    /**
     * @param int $id
     *
     * @return RecordDto
     */
    public function editRecord(int $id): RecordDto
    {
        $record = $this->recordRepo->find($id);

        return new RecordDto($record);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return RecordDto
     */
    public function putEditedRecord(Request $request, int $id): RecordDto
    {
        $record = $this->recordRepo->find($id);

        $record  = $this->parseRecordRequest($request, $record);
        dump($record);

        return new RecordDto($record);
    }

    /**
     * @param int $id
     *
     * @return RecordDto
     */
    public function deleteRecord(int $id): RecordDto
    {
        $record = $this->recordRepo->ofId($id);
        $this->recordRepo->remove($record);

        return new RecordDto($record);
    }

    /**
     * @param Request $request
     * @param Record|null $record
     * @param int|null $id
     *
     * @return Record
     */
    protected function parseRecordRequest(Request $request, Record $record = null, int $id=null): Record
    {
        $date = DateTime::createFromFormat("Y-m-d", $request->get('date'));
        $distance = $request->get('distance');
        $time = $request->get('time');

        if (!$record && $id !== null) {
            $user = $this->userRepo->find($id);
            $record = new Record($date, $time, $distance, $user);
        } else {
            $record->setDate($date);
            $record->setDistance($distance);
            $record->setTime($time);
        }

        $this->recordRepo->add($record);

        return $record;
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