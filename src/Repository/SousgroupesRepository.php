<?php

namespace App\Repository;

use App\Entity\Sousgroupes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sousgroupes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sousgroupes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sousgroupes[]    findAll()
 * @method Sousgroupes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SousgroupesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sousgroupes::class);
    }

    // /**
    //  * @return Sousgroupes[] Returns an array of Sousgroupes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sousgroupes
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
