<?php

namespace App\Repository;

use App\Entity\Record;
use App\Model\RepositoryFilter;
use App\Repository\Interfaces\RecordRepositoryInterface;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

class RecordRepository extends ServiceEntityRepository implements RecordRepositoryInterface
{
    /**
     * RecordRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Record::class);
    }

    /**
     * @param int $id
     *
     * @return Record|null
     *
     * @throws NonUniqueResultException
     */
    public function ofId(int $id): ?Record
    {
        return $this->createQueryBuilder('r')
            ->where("r.id = :id")
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function remove(Record $record)
    {
        $this->getEntityManager()->remove($record);
        $this->getEntityManager()->flush();
    }

    public function add(Record $record)
    {
        $this->getEntityManager()->persist($record);
        $this->getEntityManager()->flush();
    }

    /**
     * @param RepositoryFilter $filter
     *
     * @return Record[]|null
     *
     * @throws NonUniqueResultException
     */
    public function filter(RepositoryFilter $filter): ?array
    {
        $query = $this->createQueryBuilder('r');

        if ($filter->isFirstJogg()) {
            return $query->select('min(r.date)')
                ->where('r.user = :id')
                ->setParameter('id', $filter->getUserId())
                ->getQuery()
                ->getOneOrNullResult();
        }
        if ($filter->isLastJogg()) {
            return $query->select('max(r.date)')
                ->where('r.user = :id')
                ->setParameter('id', $filter->getUserId())
                ->getQuery()
                ->getOneOrNullResult();
        }

        if (!$filter->getEndDate() && !$filter->getStartDate()) {
            return $query->where('r.user = :id')
                ->setParameters(['id' => $filter->getUserId()])
                ->getQuery()
                ->getArrayResult();

        } elseif (!$filter->getStartDate()) {
            return $query->andWhere('r.user = :id AND r.date < endDate')
                ->setParameters(['id' => $filter->getUserId(), 'endDate' => $filter->getEndDate()])
                ->getQuery()
                ->getArrayResult();
        } elseif (!$filter->getEndDate()) {
            $partDate = DateTime::createFromFormat('Y-m-d', $filter->getStartDate()->format('Y-m-d'));
            $partDate->modify('-1 days');

            return $query->where('r.user = :id AND r.date >= :startDate')
                ->setParameters(['id' => $filter->getUserId(), 'startDate' => $partDate])
                ->getQuery()
                ->getArrayResult();

        }

        $partDate = DateTime::createFromFormat('Y-m-d', $filter->getStartDate()->format('Y-m-d'));
        $partDate->modify('-1 days');

        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :id AND :endDate >= r.date AND :startDate < r.date')
            ->setParameters(['id' => $filter->getUserId(), 'startDate' => $partDate, 'endDate' => $filter->getEndDate()])
            ->getQuery()
            ->getArrayResult();
    }
}
