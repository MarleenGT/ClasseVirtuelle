<?php


namespace App\Controller\Cours;


use App\Controller\CheckCoursConflit\CheckCoursConflit;
use App\Entity\Cours;
use App\Entity\Profs;
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
     * @param CheckCoursConflit $checkCoursConflit
     * @return Response
     * @IsGranted("ROLE_PROF")
     * @Route("/Cours/Ajouter", name="cours.add", methods={"POST"})
     */
    public function add(Request $request, CheckSession $checkSession, CheckCoursConflit $checkCoursConflit): Response
    {
        $session = $checkSession->getSession($request);
        /**
         * Préparation de tableau de matières pour le formulaire
         */
        $matieres = [];
        foreach ($session->get('user')->getIdMatiere() as $matiere) {
            $matieres[] = $matiere;
        }
        $matieres[] = 'Autre';

        $cours = new Cours();
        $form = $this->createForm(CoursType::class, $cours, [
            'matieres' => $matieres,
            'classes' => $session->get('user')->getIdClasse(),
            'sousgroupes' => $session->get('user')->getIdUser()->getSousgroupesVisibles(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prof = $this->getDoctrine()->getRepository(Profs::class)->find($session->get('user')->getId());
            $obj = $form->getData();

            /**
             * Ajout de la matière si la selection de la matière dans le formulaire est Autre.
             */

            if ($form->get('matiere')->getData() === "Autre") {
                $obj->setMatiere($form->get('autre')->getData());
            }
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
                $this->addFlash('danger', 'Rentrez une classe ou un sous-groupe.');
                return $this->render("cours/add.html.twig", [
                    "form" => $form->createView(),
                ]);
            }
            /**
             * Vérification que le cours ajouté n'est pas déjà passé
             */
            if ($heure_debut < $now) {
                $this->addFlash('danger', 'Problème au niveau des horaires : le cours ajouté est déjà passé.');
                return $this->render("cours/add.html.twig", [
                    "form" => $form->createView(),
                ]);
            }
            /**
             * Vérification que le début du cours est avant la fin du cours
             */
            if ($heure_debut > $heure_fin) {
                $this->addFlash('danger', 'Problème au niveau des horaires : la fin du cours se passe avant la fin du cours.');
                return $this->render("cours/add.html.twig", [
                    "form" => $form->createView(),
                ]);
            }
            /**
             * Vérification que les cours ajoutés sont bien dans la plage horaire d'affichage de l'emploi du temps
             */
            if ((int)$heure_debut->format('H') < $debut_affichage || (int)$heure_fin->format('H') > $fin_affichage) {
                $this->addFlash('danger', 'Problème au niveau des horaires : Vérifiez que le cours ajouté est bien dans les horaires définis par l\'administrateur (de $debut_affichage h à $fin_affichage h).');
                return $this->render("cours/add.html.twig", [
                    "form" => $form->createView(),
                ]);
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
                        dump($sousgroupe);
                        $obj->setIdSousgroupe($sousgroupe);
                        break;
                    }
                }
            }


            try {
                $verif = $checkCoursConflit->verifCours($obj);
            } catch (Exception $e) {
                $this->addFlash('danger', 'Problème dans la vérification des cours');
                return $this->render("cours/add.html.twig", [
                    "form" => $form->createView(),
                ]);
            }

            if (count($verif) > 0) {
                $msg = "Conflit avec d'autres cours : <br>";
                foreach ($verif as $item) {
                    $msg .= $item->getMatiere()." avec ".$item->getIdProf()->getCivilite().$item->getIdProf()->getNom()." le ".$item->getHeureDebut()->format("d/m/Y")." de ".$item->getHeureDebut()->format("H:i")." à ".$item->getHeureFin()->format("H:i");
                    if ($item->getIdClasse()){
                        $msg .= " (".$item->getIdClasse()->getNomClasse().")";
                    }
                    $msg .= "<br>";
                }
                $this->addFlash('danger', $msg);
                return $this->render("cours/add.html.twig", [
                    "form" => $form->createView(),
                    'conflit' => $verif
                ]);
            }
            /**
             * Vérification du lien de connection au cours
             * et ajout de http si besoin pour permettre à Twig de détecter que le lien est un lien externe
             */
            $lien = $obj->getLien();
            if (substr($lien, 0, 8) !== "https://" && substr($lien, 0, 7) !== "http://") {
                $obj->setLien("http://".$lien);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cours);
            $entityManager->flush();
            $this->addFlash('success', 'Cours ajouté !');
            return $this->redirectToRoute('cours.index');
        }
        return $this->render("cours/add.html.twig", [
            "form" => $form->createView()
        ]);
    }
}