<?php

namespace App\Repository;

use App\Entity\Archives;
use App\Entity\Classes;
use App\Entity\Sousgroupes;
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

    /**
     * Les méthodes ci-dessous servent lors de l'ajout d'un cours pour détecter les collisions possibles et pour
     * déterminer quels cours concernent l'élève au moment de l'affichage de son emploi du temps
     */

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
