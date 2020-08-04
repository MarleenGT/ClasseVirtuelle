<?php

namespace App\Repository;

use App\Entity\Profs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Profs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profs[]    findAll()
 * @method Profs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profs::class);
    }

//    /**
//     * @return Profs[] Returns an array of Profs objects
//     */

    public function findProfHydrated($id)
    {
        return $this->createQueryBuilder('p')
            ->where("p.id_user = $id")
            ->addSelect('c')
            ->leftjoin('p.id_classe', 'c')
            ->addSelect('m')
            ->leftjoin('p.id_matiere', 'm')
            ->getQuery()
            ->getSingleResult()
            ;
    }

    public function findProfsByPages($limit, $offset, $search)
    {
        $column = ['p.id', 'p.nom', 'p.prenom', 'p.civilite', 'GROUP_CONCAT(DISTINCT c.nom_classe) as classe', 'GROUP_CONCAT(DISTINCT m.nom_matiere) as matiere'];

        return $this->createQueryBuilder('p')
            ->select($column)
            ->leftjoin('p.id_classe', 'c')
            ->leftjoin('p.id_matiere', 'm')
            ->where("lower(p.nom) LIKE '%".$search."%' OR lower(p.prenom) LIKE '%".$search."%' OR lower(c.nom_classe) LIKE '%".$search."%' OR lower(m.nom_matiere) LIKE '%".$search."%'")
            ->orderBy('p.nom', 'ASC')
            ->groupBy('p.id')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findProfsByClasseId(int $id)
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.nom, p.prenom, p.civilite')
            ->leftjoin('p.id_classe', 'c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findProfsBySousgroupeId(int $id)
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.nom, p.prenom, p.civilite')
            ->leftJoin('p.id_user', 'u')
            ->where(':id MEMBER OF u.sousgroupes_visibles')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?Profs
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
