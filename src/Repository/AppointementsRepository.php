<?php

// src/Repository/AppointementsRepository.php

namespace App\Repository;

use App\Entity\Appointements;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class AppointementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointements::class);
    }

    public function findUpcomingByUser(Users $user): array
    {
        return $this->getUserAppointmentsQueryBuilder($user)
            ->andWhere('a.DateTime >= :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    public function findPastByUser(Users $user): array
    {
        return $this->getUserAppointmentsQueryBuilder($user)
            ->andWhere('a.DateTime < :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    private function getUserAppointmentsQueryBuilder(Users $user): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.services', 's')
            ->addSelect('s')
            ->where('a.users = :user')
            ->setParameter('user', $user);
    }
}

