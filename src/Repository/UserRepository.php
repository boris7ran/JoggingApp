<?php

namespace App\Repository;

use App\Entity\User;
use App\Model\RepositoryFilter;
use App\Repository\Interfaces\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
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
     *
     * @return User|null
     *
     * @throws NonUniqueResultException
     */
    public function ofId(int $id): ?User
    {
        return $this->createQueryBuilder('u')
            ->where("u.id = :id")
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param User $user
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(User $user)
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @param User $user
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $user)
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @param RepositoryFilter $filter
     * @return User|null
     *
     * @throws NonUniqueResultException
     */
    public function filter(RepositoryFilter $filter): ?User
    {
        $query = $this->createQueryBuilder('u')
            ->leftJoin('u.records', 'r')
            ->addSelect('r');

        if (!$filter->getStartDate() && !$filter->getEndDate()) {
            return $query->andWhere('u.id = :id')
                ->setParameter('id', $filter->getUserId())
                ->getQuery()
                ->getOneOrNullResult();
        }

        if (!$filter->getStartDate()) {
            return $query->andWhere('u.id = :id AND r.date < :endDate')
                ->setParameters(['id' => $filter->getUserId(),
                    'endDate' => $filter->getEndDate()])
                ->getQuery()
                ->getOneOrNullResult();
        }

        if (!$filter->getEndDate()) {
            return $query->andWhere('u.id = :id AND r.date > :startDate')
                ->setParameters(['id' => $filter->getUserId(),
                    'startDate' => $filter->getStartDate()])
                ->getQuery()
                ->getOneOrNullResult();
        }

        return $query->andWhere('u.id = :id AND r.date >= :startDate AND r.date < :endDate')
            ->setParameters(['id' => $filter->getUserId(),
                'startDate' => $filter->getStartDate(),
                'endDate' => $filter->getEndDate()])
            ->getQuery()
            ->getOneOrNullResult();
    }
}