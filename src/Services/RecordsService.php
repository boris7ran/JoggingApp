<?php

namespace App\Services;

use App\Entity\Record;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RecordsService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var object|string
     */
    private $user;

    /**
     * RecordsService constructor.
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @param int|null $id
     * @param string|null $startDate
     * @param string|null $endDate
     *
     * @return User
     *
     * @throws Exception
     */
    public function getUserRecords(int $id = null, string $startDate = null, string $endDate = null): User
    {
        if ($startDate) {
            $startDate = DateTime::createFromFormat("Y-m-d", $startDate);
        }

        if ($endDate) {
            $endDate = DateTime::createFromFormat("Y-m-d", $endDate);
        }

        if (!$id) {
            $user = $this->user;
        } else {
            $user = $this->em->getRepository(User::class)->getWithRecords($id, $startDate, $endDate);
            if (!$user) {
                $user = $this->em->getRepository(User::class)->find($id);
            }
        }

        if (!$user) {
            throw new Exception('No user found with id ' . $id);
        }

        return $user;
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return Record
     */
    public function storeNewRecord(Request $request, int $id): Record
    {
        $record = new Record();

        $this->parseRecordRequest($request, $record, $id);

        return $record;
    }

    /**
     * @param int $id
     *
     * @return Record
     */
    public function editRecord(int $id): Record
    {
        $record = $this->em->getRepository(Record::class)->find($id);

        return $record;
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return Record
     */
    public function putEditedRecord(Request $request, int $id): Record
    {
        $record = $this->em->getRepository(Record::class)->find($id);

        $record  = $this->parseRecordRequest($request, $record);

        return $record;
    }

    /**
     * @param int $id
     * @return Record
     */
    public function deleteRecord(int $id): Record
    {
        $record = $this->em->getRepository(Record::class)->find($id);

        $this->em->remove($record);
        $this->em->flush();

        return $record;
    }

    /**
     * @param Request $request
     * @param Record $record
     * @param int|null $id
     *
     * @return Record
     */
    protected function parseRecordRequest(Request $request, Record $record, int $id=null): Record
    {
        $date = DateTime::createFromFormat("Y-m-d", $request->get('date'));
        $record->setDate($date);
        $record->setDistance($request->get('distance'));
        $record->setTime($request->get('time'));

        if (!$record->getUser() && $id !== null) {
            $user = $this->em->getRepository(User::class)->find($id);
            $record->setUser($user);
        }

        $this->em->persist($record);
        $this->em->flush();

        return $record;
    }

    public function makeReports(int $id)
    {
        $firstJogg = $this->em->getRepository(Record::class)->getFirstJogg($id)[1];
        $lastJogg = $this->em->getRepository(Record::class)->getLastJogg($id)[1];
        $firstJogg = DateTime::createFromFormat("Y-m-d", $firstJogg);
        $lastJogg = DateTime::createFromFormat("Y-m-d", $lastJogg);

        if (!$firstJogg) {
            return [];
        }
        $firstDayWeek = DateService::getStartDate($firstJogg);

        $reports = [];

        for ($i = $firstDayWeek; $i < $lastJogg; $i->modify('+7 days')) {
            $lastDayOfCurrentWeek = DateService::getEndDate($i);

            $records = $this->em->getRepository(Record::class)
                ->getFilteredRecords($id, $i, $lastDayOfCurrentWeek);

            if ($records) {
                $report = $this->calculateAverage($records);
                $report["week"] = $i->format("W");
                $reports[] = $report;
            }
        }

        return $reports;
    }

    public function calculateAverage($records)
    {
        $averageDistance = 0;
        $averageTime = 0;

        foreach ($records as $record) {
            $averageTime += $record['time'];
            $averageDistance += $record['distance'];
        }

        $averageTime = $averageTime/count($records);
        $averageDistance = $averageDistance/count($records);

        return [
            'averageDistance' => $averageDistance,
            "averageTime" => $averageTime
        ];
    }
}