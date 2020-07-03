<?php

namespace App\Repository;

use App\Entity\Eleves;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Eleves|null find($id, $lockMode = null, $lockVersion = null)
 * @method Eleves|null findOneBy(array $criteria, array $orderBy = null)
 * @method Eleves[]    findAll()
 * @method Eleves[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElevesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eleves::class);
    }

    /**
     * @param $limit
     * @param $offset
     * @return Eleves[] Returns an array of Eleves objects
     */

    public function findElevesByPages($limit, $offset)
    {
        $column = ['e.id', 'e.nom', 'e.prenom', 'c.nom_classe as classe', 'GROUP_CONCAT(s.nom_sousgroupe) as sousgroupe'];
        return $this->createQueryBuilder('e')
            ->select($column)
            ->leftjoin('e.id_classe', 'c')
            ->leftjoin('e.id_sousgroupe', 's')
            ->orderBy('e.nom', 'ASC')
            ->groupBy('e.id')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }



    public function findElevesByClasse($classe): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.id_classe = :classe')
            ->setParameter('classe', $classe->getId())
            ->getQuery()
            ->getResult()
        ;
    }

}
