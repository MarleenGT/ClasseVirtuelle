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

    /**
     * @return Sousgroupes[] Returns an array of Sousgroupes objects
     */

    public function findSousgroupesByEleve($eleve): array
    {
        return $this->createQueryBuilder('s')
            ->Where(':eleve MEMBER OF s.eleves')
            ->setParameter('eleve', $eleve)
            ->getQuery()
            ->getResult();
    }

    public function getSGNomByCreateur($id)
    {
        return $this->createQueryBuilder('s')
            ->select('s.nom_sousgroupe')
            ->where('s.id_createur = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function getSousgroupesCreesByProf($prof)
    {
        return $this->createQueryBuilder('s')
            ->where('s.id_createur = :id')
            ->setParameter('id', $prof->getId())
            ->getQuery()
            ->getResult();
    }

}
