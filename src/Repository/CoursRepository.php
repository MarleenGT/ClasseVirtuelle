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
     * @param $date1
     * @param $date2
     * @param $id
     * @return cours[] Returns an array of cours objects
     */
    public function findCoursByWeekAndByProf($date1, $date2, $id)
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

    /**
     * @param $sousgroupe
     * @param $heure_debut
     * @param $heure_fin
     * @return array
     */
    public function findCoursBySousgroupe($sousgroupe, $heure_debut, $heure_fin): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.id_prof', 'p')
            ->where('c.heure_debut BETWEEN :debut AND :fin OR c.heure_fin BETWEEN :debut AND :fin')
            ->setParameter('debut', $heure_debut)
            ->setParameter('fin', $heure_fin)
            ->andWhere('c.id_sousgroupe = :sousgroupe')
            ->setParameter('sousgroupe', $sousgroupe)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $classe
     * @param $heure_debut
     * @param $heure_fin
     * @return array
     */
    public function findCoursByClasse($classe, $heure_debut, $heure_fin): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.id_prof', 'p')
            ->where('c.heure_debut BETWEEN :debut AND :fin OR c.heure_fin BETWEEN :debut AND :fin')
            ->setParameter('debut', $heure_debut)
            ->setParameter('fin', $heure_fin)
            ->andWhere('c.id_classe = :classe')
            ->setParameter('classe', $classe)
            ->getQuery()
            ->getResult();
    }

    public function findCoursToArchive($date): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.heure_fin < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
}
