<?php

namespace App\Repository;

use App\Entity\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cours|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cours|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cours[]    findAll()
 * @method Cours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cours::class);
    }

    /**
     * @return cours[] Returns an array of cours objects
     */
    public function findCoursByWeek($date1, $date2)
    {
        $column = ['c.heure_debut', 'c.heure_fin', 'm.nom_matiere as matiere', 'p.nom as nom','cl.nom_classe as classe', 'c.commentaire'];
        return $this->createQueryBuilder('c')
            ->select($column)
            ->leftjoin('c.id_classe', 'cl')
            ->leftjoin('c.id_matiere', 'm')
            ->leftjoin('c.id_prof', 'p')
            ->where('c.heure_debut BETWEEN :lundi AND :samedi')
            ->setParameter('lundi', $date1)
            ->setParameter('samedi', $date2)
            ->getQuery()
            ->getResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?cours
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
