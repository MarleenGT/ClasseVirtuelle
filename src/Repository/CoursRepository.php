<?php

namespace App\Repository;

use App\Entity\Classes;
use App\Entity\Cours;
use App\Entity\Profs;
use App\Entity\Sousgroupes;
use DateTimeInterface;
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
    public function findCoursByWeekAndByProf(DateTimeInterface $date1, DateTimeInterface $date2, int $id): array
    {
        return $this->createQueryBuilder('c')
            ->leftjoin('c.id_classe', 'cl')
            ->where('c.heure_debut BETWEEN :lundi AND :samedi')
            ->setParameter('lundi', $date1)
            ->setParameter('samedi', $date2)
            ->andWhere('c.id_prof = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
    public function findCoursByWeekAndBySousgroupe(DateTimeInterface $date1, DateTimeInterface $date2, int $id): array
    {
        return $this->createQueryBuilder('c')
            ->leftjoin('c.id_sousgroupe', 'sg')
            ->where('c.heure_debut BETWEEN :lundi AND :samedi')
            ->setParameter('lundi', $date1)
            ->setParameter('samedi', $date2)
            ->andWhere('c.id_sousgroupe = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
    public function findCoursByWeekAndByClasse(DateTimeInterface $date1, DateTimeInterface $date2, int $id): array
    {
        return $this->createQueryBuilder('c')
            ->leftjoin('c.id_classe', 'cl')
            ->where('c.heure_debut BETWEEN :lundi AND :samedi')
            ->setParameter('lundi', $date1)
            ->setParameter('samedi', $date2)
            ->andWhere('c.id_classe = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
    /**
     * Les méthodes ci-dessous servent lors de l'ajout d'un cours pour détecter les collisions possibles et pour
     * déterminer quels cours concernent l'élève au moment de l'affichage de son emploi du temps
     */

    /**
     * @param Profs $prof
     * @param DateTimeInterface $heure_debut
     * @param DateTimeInterface $heure_fin
     * @return array
     */
    public function findCoursByProf(Profs $prof, DateTimeInterface $heure_debut, DateTimeInterface $heure_fin): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.heure_debut < :fin AND c.heure_fin > :debut')
            ->setParameter('debut', $heure_debut)
            ->setParameter('fin', $heure_fin)
            ->andWhere('c.id_prof = :prof')
            ->setParameter('prof', $prof)
            ->getQuery()
            ->getResult();
    }
    /**
     * @param $sousgroupe
     * @param $heure_debut
     * @param $heure_fin
     * @return array
     */
    public function findCoursBySousgroupe(Sousgroupes $sousgroupe, DateTimeInterface $heure_debut, DateTimeInterface $heure_fin): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.id_prof', 'p')
            ->where('c.heure_debut < :fin AND c.heure_fin > :debut')
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
    public function findCoursByClasse(Classes $classe, DateTimeInterface $heure_debut, DateTimeInterface $heure_fin): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.id_prof', 'p')
            ->where('c.heure_debut < :fin AND c.heure_fin > :debut')
            ->setParameter('debut', $heure_debut)
            ->setParameter('fin', $heure_fin)
            ->andWhere('c.id_classe = :classe')
            ->setParameter('classe', $classe)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $date
     * @return array
     */
    public function findCoursToArchive($date): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.heure_fin < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
}
