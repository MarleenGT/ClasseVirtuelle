<?php

namespace App\Repository;

use App\Entity\Personnels;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Personnels|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personnels|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personnels[]    findAll()
 * @method Personnels[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonnelsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personnels::class);
    }
    /**
     * @return Personnels[] Returns an array of Personnels objects
     */

    public function findPersonnelsByPages($limit, $offset)
    {
        $column = ['p.id', 'p.nom', 'p.prenom', 'p.poste'];

        return $this->createQueryBuilder('p')
            ->select($column)
            ->orderBy('p.nom', 'ASC')
            ->groupBy('p.id')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }
    // /**
    //  * @return Personnels[] Returns an array of Personnels objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Personnels
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
