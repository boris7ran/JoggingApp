<?php

namespace App\Repository;

use App\Entity\Record;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Record|null find($id, $lockMode = null, $lockVersion = null)
 * @method Record|null findOneBy(array $criteria, array $orderBy = null)
 * @method Record[]    findAll()
 * @method Record[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecordRepository extends ServiceEntityRepository
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
     * @param DateTime $startDate
     *
     * @return Record[]
     */
    public function filterByDate(int $id, $startDate): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.user', 'u')
            ->addSelect('u')
            ->andWhere('r.date > :startdate AND r.user = :id')
            ->setParameters(['startdate' => $startDate, 'id' => $id])
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param int $id
     *
     * @return mixed
     *
     * @throws NonUniqueResultException
     */
    public function getFirstJogg(int $id): ?Record
    {
        return $this->createQueryBuilder('r')
            ->select('min(r.date)')
            ->where('r.user = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getLastJogg($id)
    {
        return $this->createQueryBuilder('r')
            ->select('max(r.date)')
            ->where('r.user = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $id
     * @param DateTime $startDate
     * @param DateTime $endDate
     *
     * @return Record[]
     */
    public function getFilteredRecords(int $id, DateTime $startDate, DateTime $endDate)
    {
        $partDate = DateTime::createFromFormat('Y-m-d', $startDate->format('Y-m-d'));
        $partDate->modify('-1 days');

        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :id AND :endDate >= r.date AND :startDate < r.date')
            ->setParameters(['id' => $id, 'startDate' => $partDate, 'endDate' => $endDate])
            ->getQuery()
            ->getArrayResult();
    }
}
