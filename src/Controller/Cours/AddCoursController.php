<?php


namespace App\Controller\Cours;


use App\Entity\Cours;
use App\Entity\Eleves;
use App\Entity\Profs;
use App\Entity\Sousgroupes;
use App\Form\Cours\CoursType;
use App\Service\CheckSession;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddCoursController extends AbstractController
{
    /**
     * @param Request $request
     * @param CheckSession $checkSession
     * @return Response
     * @IsGranted("ROLE_PROF")
     * @Route("/Cours/Ajouter", name="cours.add", methods={"POST"})
     */
    public function add(Request $request, CheckSession $checkSession): Response
    {
        $session = $checkSession->getSession($request);
        $cours = new Cours();
        $form = $this->createForm(CoursType::class, $cours, [
            'matieres' => $session->get('matiere')->getValues(),
            'classes' => $session->get('classe')->getValues(),
            'sousgroupes' => $session->get('sousgroupe')->getValues(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prof = $this->getDoctrine()->getRepository(Profs::class)->find($session->get('id'));
            $obj = $form->getData();

            /**
             * Création des objets DateTime pour le début et fin du cours
             */
            $date = $form['date']->getData();
            $type = $form['typeChoice']->getData();
            $heure_debut = $form['heure_debut']->getData()->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
            $heure_fin = $form['heure_fin']->getData()->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
            $obj->setDate(new DateTime())->setIdProf($prof)->setHeureDebut($heure_debut)->setHeureFin($heure_fin);
            $now = new DateTime();
            $debut_affichage = $this->getParameter('startTimeTable');
            $fin_affichage = $this->getParameter('endTimeTable');

            if ($type === 'sousgroupe') {
                $cours->setIdClasse(null);
            } elseif ($type === 'classe') {
                $cours->setIdSousgroupe(null);
            } else {
                return $this->render("cours/add.html.twig", [
                    "form" => $form->createView(),
                    'error' => 'Rentrez une classe ou un sous-groupe.'
                ]);
            }
            /**
             * Vérification que le cours ajouté n'est pas déjà passé
             */
            if ($heure_debut < $now) {
                return $this->render("cours/add.html.twig", [
                    "form" => $form->createView(),
                    'error' => 'Problème au niveau des horaires : le cours ajouté est déjà passé.'
                ]);
            }
            /**
             * Vérification que le début du cours est avant la fin du cours
             */
            if ($heure_debut > $heure_fin) {
                return $this->render("cours/add.html.twig", [
                    "form" => $form->createView(),
                    'error' => "Problème au niveau des horaires : la fin du cours se passe avant la fin du cours."
                ]);
            }
            /**
             * Vérification que les cours ajoutés sont bien dans la plage horaire d'affichage de l'emploi du temps
             */
            if ((int)$heure_debut->format('h') < $debut_affichage || (int)$heure_fin->format('h') > $fin_affichage) {
                return $this->render("cours/add.html.twig", [
                    "form" => $form->createView(),
                    'error' => "Problème au niveau des horaires : Vérifiez que le cours ajouté est bien dans les horaires définis par l'administrateur (de $debut_affichage h à $fin_affichage h)."
                ]);
            }

            /**
             * Partie nécessaire pour que les entités Classes et Matières de l'entité Cours soient les mêmes
             * que celles du prof. Sinon, Doctrine cherche à rajouter des entités supplémentaires dans les tables.
             */
            $id_matiere = $obj->getIdMatiere()->getId();
            foreach ($prof->getIdMatiere()->getValues() as $matiere) {
                if ($matiere->getId() === $id_matiere) {
                    $obj->setIdMatiere($matiere);
                    break;
                }
            }
            if ($type === 'classe') {
                $id_classe = $obj->getIdClasse()->getId();
                foreach ($prof->getIdClasse()->getValues() as $classe) {
                    if ($classe->getId() === $id_classe) {
                        $obj->setIdClasse($classe);
                        break;
                    }
                }
            } else {
                $id_sousgroupe = $obj->getIdSousgroupe()->getId();
                foreach ($prof->getIdUser()->getSousgroupesvisibles() as $sousgroupe) {
                    if ($sousgroupe->getId() === $id_sousgroupe) {
                        $obj->setIdSousgroupe($sousgroupe);
                        break;
                    }
                }
            }


            try {
                $verif = $this->verifCours($obj);
            } catch (Exception $e) {

            }

            if (count($verif['classe']) > 0 || count($verif['sousgroupe']) > 0 || count($verif['prof']) > 0) {
                return $this->render("cours/add.html.twig", [
                    "form" => $form->createView(),
                    'conflit' => $verif
                ]);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cours);
            $entityManager->flush();

            return $this->redirectToRoute('cours.index');
        }
        return $this->render("cours/add.html.twig", [
            "form" => $form->createView()
        ]);
    }

    private function verifCours(Cours $cours)
    {
        $heure_debut = $cours->getHeureDebut();
        $heure_fin = $cours->getHeureFin();
        $coursListe = ['classe' => [], 'sousgroupe' => [], 'prof' => []];
        $sousgroupeListe = [];
        $classeListe = [];

        $coursProfListe = $this->getDoctrine()->getRepository(Cours::class)->findCoursByProf($cours->getIdProf(), $heure_debut, $heure_fin);
        foreach ($coursProfListe as $coursProf){
            if (!isset($coursListe['prof'][$coursProf->getId()])){
                $coursListe['prof'][$coursProf->getId()] = $coursProf;
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
                $coursListe['classe'][] = $coursClasse;
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
                        $coursListe['classe'][] = $coursClasse;
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
                            if (!isset($coursListe['sousgroupe'][$coursSousgroupe->getId()])) {
                                $coursListe['sousgroupe'][$coursSousgroupe->getId()] = $coursSousgroupe;
                            }
                        }
                    }
                }
            }
        }
        return $coursListe;
    }
}