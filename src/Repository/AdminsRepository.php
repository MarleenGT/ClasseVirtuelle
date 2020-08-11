<?php

namespace App\Repository;

use App\Entity\Admins;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Admins|null find($id, $lockMode = null, $lockVersion = null)
 * @method Admins|null findOneBy(array $criteria, array $orderBy = null)
 * @method Admins[]    findAll()
 * @method Admins[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Admins::class);
    }

    public function findAdminsByPages($limit, $offset, $search)
    {
        $column = ['a.id', 'a.nom', 'a.prenom'];

        return $this->createQueryBuilder('a')
            ->select($column)
            ->where("lower(a.nom) LIKE '%".$search."%' OR lower(a.prenom) LIKE '%".$search."%'")
            ->orderBy('a.nom', 'ASC')
            ->groupBy('a.id')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }


    public function countRowsFromTable(): int
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

}
