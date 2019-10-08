<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Tests\Fixtures\includes\HotPath\P1;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getWithRecords($id, $startDate = null, $endDate =  null)
    {
        if (!$endDate) {
            $endDate =  \DateTime::createFromFormat("Y-m-d", '3000-1-1');
        }

        if (!$startDate) {
            $startDate = \DateTime::createFromFormat("Y-m-d", '1900-1-1');
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