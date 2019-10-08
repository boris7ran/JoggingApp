<?php

namespace App\Services;

use App\Entity\Record;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RecordsService
{
    private $em;
    private $user;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    public function getUserRecords($id = null)
    {
        if (!$id) {
            $user = $this->user;
        } else {
            $user = $this->em->getRepository(User::class)->find($id);
        }

        if (!$user) {
            throw new Exception('No record found with id ' . $id);
        }

        return $user;
    }

    public function storeNewRecord(Request $request, int $id)
    {
        $record = new Record();

        $this->parseRecordRequest($request, $record, $id);

        return $record;
    }

    public function editRecord($id)
    {
        $record = $this->em->getRepository(Record::class)->find($id);

        return $record;
    }

    public function putEditedRecord(Request $request, int $id)
    {
        $record = $this->em->getRepository(Record::class)->find($id);

        $record  = $this->parseRecordRequest($request, $record);

        return $record;
    }

    public function deleteRecord(int $id)
    {
        $record = $this->em->getRepository(Record::class)->find($id);

        $this->em->remove($record);
        $this->em->flush();

        return $record;
    }

    protected function parseRecordRequest(Request $request, Record $record, $id=null)
    {
        $date = \DateTime::createFromFormat("Y-m-d", $request->get('date'));
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
}