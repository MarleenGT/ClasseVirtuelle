<?php


namespace App\Controller\CheckCoursConflit;


use App\Entity\Cours;
use App\Entity\Eleves;
use App\Entity\Sousgroupes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;

class CheckCoursConflit extends AbstractController
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function verifCours(Cours $cours)
    {
        $heure_debut = $cours->getHeureDebut();
        $heure_fin = $cours->getHeureFin();
        $coursListe = [];
        $sousgroupeListe = [];
        $classeListe = [];

        $coursProfListe = $this->getDoctrine()->getRepository(Cours::class)->findCoursByProf($cours->getIdProf(), $heure_debut, $heure_fin);
        foreach ($coursProfListe as $coursProf){
            if (!isset($coursListe['prof'][$coursProf->getId()])){
                $coursListe[$coursProf->getId()] = $coursProf;
            }
        }

        /**
         * On récupère la liste des élèves concernés par le cours
         */
        if ($cours->getIdClasse()) {
            $classe = $cours->getIdClasse();
            $eleves = $this->getDoctrine()->getRepository(Eleves::class)->findElevesByClasse($classe);
        } elseif ($cours->getIdSousgroupe()) {
            $groupe = $cours->getIdSousgroupe();
            $eleves = $this->getDoctrine()->getRepository(Eleves::class)->findElevesBySousgroupe($groupe);
        } else {
            throw new Exception("Classe et sous-groupe nuls pour le cours à ajouter.");
        }

        /**
         * Si le cours concerne une classe, on récupère l'ensemble des cours en conflit pour la classe concernée
         */
        if ($cours->getIdClasse()) {
            $coursClasseListe = $this->getDoctrine()->getRepository(Cours::class)->findCoursByClasse($cours->getIdClasse(), $heure_debut, $heure_fin);
            foreach ($coursClasseListe as $coursClasse) {
                if (!isset($coursListe[$coursClasse->getId()])) {
                    $coursListe[$coursClasse->getId()] = $coursClasse;
                }

            }
        }
        /**
         * Pour chaque élève, on récupère les cours le concernant en conflit avec le cours à ajouter
         */
        foreach ($eleves as $eleve) {
            /**
             * Si le cours concerne un sous-groupe, on récupère l'ensemble des cours en conflit
             * dans la classe de chaque élève (en évitant de répéter les requêtes déjà effectuées)
             */
            if ($cours->getIdSousgroupe()) {
                $classe = $eleve->getIdClasse();
                if (!isset($classeListe[$classe->getId()])) {
                    $classeListe[$classe->getId()] = $classe;
                    $coursClasseListe = $this->getDoctrine()->getRepository(Cours::class)->findCoursByClasse($classe, $heure_debut, $heure_fin);
                    foreach ($coursClasseListe as $coursClasse) {
                        if (!isset($coursListe[$coursClasse->getId()])) {
                            $coursListe[$coursClasse->getId()] = $coursClasse;
                        }
                    }
                }
            }

            /**
             * On récupère l'ensemble des sous-groupes de chaque élève, puis les cours en conflit
             * pour chaque sous-groupe (en évitant de répéter les requêtes déjà effectuées)
             */
            $sousgroupes = $this->getDoctrine()->getRepository(Sousgroupes::class)->findSousgroupesByEleve($eleve);
            if (count($sousgroupes) > 0) {
                foreach ($sousgroupes as $sousgroupe) {
                    if (!isset($sousgroupeListe[$sousgroupe->getId()])) {
                        $sousgroupeListe[$sousgroupe->getId()] = $sousgroupe;
                        $coursSousGroupeListe = $this->getDoctrine()->getRepository(Cours::class)->findCoursBySousgroupe($sousgroupe, $heure_debut, $heure_fin);
                        foreach ($coursSousGroupeListe as $coursSousgroupe) {
                            if (!isset($coursListe[$coursSousgroupe->getId()])) {
                                $coursListe[$coursSousgroupe->getId()] = $coursSousgroupe;
                            }
                        }
                    }
                }
            }
        }
        unset($coursListe[$cours->getId()]);
        return $coursListe;
    }
//    public function verifCours(Cours $cours)
//    {
//        $heure_debut = $cours->getHeureDebut();
//        $heure_fin = $cours->getHeureFin();
//        $coursListe = ['classe' => [], 'sousgroupe' => [], 'prof' => []];
//        $sousgroupeListe = [];
//        $classeListe = [];
//
//        $coursProfListe = $this->getDoctrine()->getRepository(Cours::class)->findCoursByProf($cours->getIdProf(), $heure_debut, $heure_fin);
//        foreach ($coursProfListe as $coursProf){
//            if (!isset($coursListe['prof'][$coursProf->getId()])){
//                $coursListe['prof'][$coursProf->getId()] = $coursProf;
//            }
//        }
//
//        /**
//         * On récupère la liste des élèves concernés par le cours
//         */
//        if ($cours->getIdClasse()) {
//            $classe = $cours->getIdClasse();
//            $eleves = $this->getDoctrine()->getRepository(Eleves::class)->findElevesByClasse($classe);
//        } elseif ($cours->getIdSousgroupe()) {
//            $groupe = $cours->getIdSousgroupe();
//            $eleves = $this->getDoctrine()->getRepository(Eleves::class)->findElevesBySousgroupe($groupe);
//        } else {
//            throw new Exception("Classe et sous-groupe nuls pour le cours à ajouter.");
//        }
//
//        /**
//         * Si le cours concerne une classe, on récupère l'ensemble des cours en conflit pour la classe concernée
//         */
//        if ($cours->getIdClasse()) {
//            $coursClasseListe = $this->getDoctrine()->getRepository(Cours::class)->findCoursByClasse($cours->getIdClasse(), $heure_debut, $heure_fin);
//            foreach ($coursClasseListe as $coursClasse) {
//                $coursListe['classe'][] = $coursClasse;
//            }
//        }
//        /**
//         * Pour chaque élève, on récupère les cours le concernant en conflit avec le cours à ajouter
//         */
//        foreach ($eleves as $eleve) {
//            /**
//             * Si le cours concerne un sous-groupe, on récupère l'ensemble des cours en conflit
//             * dans la classe de chaque élève (en évitant de répéter les requêtes déjà effectuées)
//             */
//            if ($cours->getIdSousgroupe()) {
//                $classe = $eleve->getIdClasse();
//                if (!isset($classeListe[$classe->getId()])) {
//                    $classeListe[$classe->getId()] = $classe;
//                    $coursClasseListe = $this->getDoctrine()->getRepository(Cours::class)->findCoursByClasse($classe, $heure_debut, $heure_fin);
//                    foreach ($coursClasseListe as $coursClasse) {
//                        $coursListe['classe'][] = $coursClasse;
//                    }
//                }
//            }
//
//            /**
//             * On récupère l'ensemble des sous-groupes de chaque élève, puis les cours en conflit
//             * pour chaque sous-groupe (en évitant de répéter les requêtes déjà effectuées)
//             */
//            $sousgroupes = $this->getDoctrine()->getRepository(Sousgroupes::class)->findSousgroupesByEleve($eleve);
//            if (count($sousgroupes) > 0) {
//                foreach ($sousgroupes as $sousgroupe) {
//                    if (!isset($sousgroupeListe[$sousgroupe->getId()])) {
//                        $sousgroupeListe[$sousgroupe->getId()] = $sousgroupe;
//                        $coursSousGroupeListe = $this->getDoctrine()->getRepository(Cours::class)->findCoursBySousgroupe($sousgroupe, $heure_debut, $heure_fin);
//                        foreach ($coursSousGroupeListe as $coursSousgroupe) {
//                            if (!isset($coursListe['sousgroupe'][$coursSousgroupe->getId()])) {
//                                $coursListe['sousgroupe'][$coursSousgroupe->getId()] = $coursSousgroupe;
//                            }
//                        }
//                    }
//                }
//            }
//        }
//        return $coursListe;
//    }
}