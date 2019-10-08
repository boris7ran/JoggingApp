<?php

namespace App\Repository;

use App\Entity\Record;
use Cassandra\Date;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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
}
