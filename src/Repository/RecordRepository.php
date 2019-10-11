<?php

namespace App\Repository;

use App\Entity\Record;
use App\Model\RecordFilter;
use App\Repository\Interfaces\RecordRepositoryInterface;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;

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
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param RecordFilter $filter
     *
     * @return Record[]
     */
    public function filter(RecordFilter $filter): array
    {
        $query = $this->createQueryBuilder('r');

        if ($filter->isFirstJogg()) {
            $query->orderBy('r.date', 'ASC')
                ->setMaxResults(1);
        }

        if ($filter->isLastJogg()) {
            $query->orderBy('r.date', 'DESC')
                ->setMaxResults(1);
        }

        if ($filter->getEndDate()) {
            $query->andWhere('r.date < :endDate')
                ->setParameter('endDate', $filter->getEndDate());
        }

        if ($filter->getStartDate()) {
            $partDate = DateTime::createFromFormat('Y-m-d', $filter->getStartDate()->format('Y-m-d'));
            $partDate->modify('-1 days');

            $query->andWhere('r.date >= :startDate')
                ->setParameter('startDate', $partDate);
        }

        if ($filter->getUserId()) {
            $query->andWhere('r.user = :id')
                ->setParameter('id', $filter->getUserId());
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param Record $record
     *
     * @return Record
     *
     * @throws ORMException
     */
    public function add(Record $record): Record
    {
        $this->getEntityManager()->persist($record);
        $this->getEntityManager()->flush();

        return $record;
    }

    /**
     * @param Record $record
     *
     * @throws ORMException
     */
    public function remove(Record $record): void
    {
        $this->getEntityManager()->remove($record);
        $this->getEntityManager()->flush();
    }
}
