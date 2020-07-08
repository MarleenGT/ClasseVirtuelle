<?php

namespace App\Repository;

use App\Entity\Archives;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Archives|null find($id, $lockMode = null, $lockVersion = null)
 * @method Archives|null findOneBy(array $criteria, array $orderBy = null)
 * @method Archives[]    findAll()
 * @method Archives[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArchivesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Archives::class);
    }

    /**
     * @param $date1
     * @param $date2
     * @param $id
     * @return archives[] Returns an array of archives objects
     */
    public function findCoursByWeekAndByProf(DateTimeInterface $date1, DateTimeInterface $date2, int $id)
    {
        $column = ['c.heure_debut', 'c.heure_fin', 'm.nom_matiere as matiere', 'cl.nom_classe as classe', 'c.commentaire'];
        return $this->createQueryBuilder('c')
            ->select($column)
            ->leftjoin('c.id_classe', 'cl')
            ->leftjoin('c.id_matiere', 'm')
            ->where('c.heure_debut BETWEEN :lundi AND :samedi')
            ->setParameter('lundi', $date1)
            ->setParameter('samedi', $date2)
            ->andWhere('c.id_prof = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Archives
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
