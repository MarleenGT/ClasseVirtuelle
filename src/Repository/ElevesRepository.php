<?php

namespace App\Repository;

use App\Entity\Classes;
use App\Entity\Eleves;
use App\Entity\Sousgroupes;
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
     * @param $search
     * @param $id
     * @return Eleves[] Returns an array of Eleves objects
     */

    public function findElevesByPages($limit, $offset, $search)
    {
        $column = ['e.id', 'e.nom', 'e.prenom', 'c.nom_classe as classe', 'GROUP_CONCAT(s.nom_sousgroupe) as sousgroupe'];
        return $this->createQueryBuilder('e')
            ->select($column)
            ->leftjoin('e.id_classe', 'c')
            ->leftjoin('e.id_sousgroupe', 's')
            ->where("lower(e.nom) LIKE :search OR lower(e.prenom) LIKE :search OR lower(c.nom_classe) LIKE :search OR lower(s.nom_sousgroupe) LIKE :search")
            ->setParameter('search', '%'.addcslashes($search, '%_').'%')
            ->orderBy('e.nom', 'ASC')
            ->groupBy('e.id')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $classe
     * @return array
     */
    public function findElevesByClasse(Classes $classe): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.id_classe = :classe')
            ->setParameter('classe', $classe->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $groupe
     * @return array
     */
    public function findElevesBySousgroupe(Sousgroupes $groupe): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.id_sousgroupe', 's')
            ->where('s.id = :groupe')
            ->setParameter('groupe', $groupe->getId())
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $id
     * @return array
     */
    public function findElevesByClasseId(int $id): array
    {
        return $this->createQueryBuilder('e')
            ->select('e.id, e.nom, e.prenom, c.nom_classe')
            ->leftJoin('e.id_classe', 'c')
            ->where('c.id = :classe')
            ->setParameter('classe', $id)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $id
     * @return array
     */
    public function findElevesBySousgroupeId(int $id): array
    {
        return $this->createQueryBuilder('e')
            ->select('e.id, e.nom, e.prenom, c.nom_classe')
            ->leftJoin('e.id_sousgroupe', 's')
            ->leftJoin('e.id_classe', 'c')
            ->where('s.id = :groupe')
            ->setParameter('groupe', $id)
            ->getQuery()
            ->getResult()
            ;
    }
}
