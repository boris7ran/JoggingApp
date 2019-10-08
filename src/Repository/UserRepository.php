<?php

namespace App\Repository;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\DependencyInjection\Tests\Fixtures\includes\HotPath\P1;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param int $id
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @return User|null
     *
     * @throws NonUniqueResultException
     */
    public function getWithRecords(int $id, $startDate = null, $endDate =  null): ?User
    {
        if (!$endDate) {
            $endDate =  DateTime::createFromFormat("Y-m-d", '3000-1-1');
        }

        if (!$startDate) {
            $startDate = DateTime::createFromFormat("Y-m-d", '1900-1-1');
        }

        if ($startDate) {
            return $this->createQueryBuilder('u')
                ->leftJoin('u.records', 'r')
                ->addSelect('r')
                ->andWhere('u.id = :id AND r.date >= :startdate AND r.date < :enddate')
                ->setParameters(['id' => $id, 'startdate' => $startDate, 'enddate' => $endDate])
                ->getQuery()
                ->getOneOrNullResult();
        } else {
            $this->createQueryBuilder('u')
                ->leftJoin('u.records', 'r')
                ->addSelect('r')
                ->andWhere('u.id = :id')
                ->setParameters(['id' => $id])
                ->getQuery()
                ->getOneOrNullResult();
        }
    }
}