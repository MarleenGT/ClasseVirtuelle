<?php


namespace App\Controller\Cours;


use App\Controller\CheckCoursConflit\CheckCoursConflit;
use App\Controller\CheckRepository\CheckCoursRepo;
use App\Entity\Profs;
use App\Form\Cours\CoursType;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateCoursController extends AbstractController
{
    /**
     * @param Request $request
     * @param CheckCoursRepo $checkCoursRepo
     * @param CheckCoursConflit $checkCoursConflit
     * @return Response
     * @throws Exception
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_PERSONNEL') or is_granted('ROLE_PROF')")
     * @Route("/Cours/Update", name="cours.update", methods={"POST"})
     */
    public function update(Request $request, CheckCoursRepo $checkCoursRepo, CheckCoursConflit $checkCoursConflit)
    {
        $session = $request->getSession();
        $prof = $this->getDoctrine()->getRepository(Profs::class)->find($session->get('user')->getId());
        if ($request->request->get('date') && $request->request->get('id')) {
            $id = $request->request->get('id');
            $date = $request->request->get('date');
        } else if ($request->request->get('cours')) {
            $id = $request->request->get('cours')['id'];
            $cours_date = new DateTime($request->request->get('cours')['date']);
            $timestamp_date = $cours_date->getTimestamp();
            $date = strtotime( 'monday this week', $timestamp_date );
        } else {
            return $this->render('cours/index.html.twig', [
                'current_menu' => 'cours'
            ]);
        }
        /**
         * Récupération de répertoire contenant le cours à modifier.
         */
        $repo = $checkCoursRepo->check($date);
        $cours = $this->getDoctrine()->getRepository($repo)->find($id);
        /**
         * Vérification de l'auteur du cours et si l'utilisateur peut le modifier
         */
        if (!($this->getUser()->getRoles()[0] === "ROLE_ADMIN" || ($this->getUser()->getRoles()[0] === "ROLE_PROF" && $cours->getIdProf()->getId() === $prof->getId()))) {
            $this->addFlash('danger', "Problème pour la récupération du cours.");
            return $this->render('cours/index.html.twig', [
                'current_menu' => 'cours'
            ]);
        }
        /**
         * Préparation de tableau de matières pour le formulaire
         */
        $matieres = [];
        foreach ($session->get('user')->getIdMatiere() as $matiere) {
            $matieres[] = $matiere;
        }
        $matieres[] = 'Autre';

        $form = $this->createForm(CoursType::class, $cours, [
            'matieres' => $matieres,
            'classes' => $prof->getIdClasse(),
            'sousgroupes' => $prof->getIdUser()->getSousgroupesVisibles(),
            'id' => $cours->getId()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cours = $form->getData();
            $type = $form['typeChoice']->getData();
            $date = $form['date']->getData();
            $heure_debut = $form['heure_debut']->getData()->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
            $heure_fin = $form['heure_fin']->getData()->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
            $now = new DateTime();
            $debut_affichage = $this->getParameter('startTimeTable');
            $fin_affichage = $this->getParameter('endTimeTable');

            /**
             * Ajout de la matière si la selection de la matière dans le formulaire est Autre.
             */
            if ($form->get('matiere')->getData() === "Autre") {
                $cours->setMatiere($form->get('autre')->getData());
            }

            /**
             * Changement de la classe ou du sousgroupe
             */
            if ($type === 'sousgroupe') {
                $cours->setIdClasse(null);
            } elseif ($type === 'classe') {
                $cours->setIdSousgroupe(null);
            } else {
                $this->addFlash('danger', 'Rentrez une classe ou un sous-groupe.');
                return $this->render("cours/modif.html.twig", [
                    'current_menu' => 'Cours',
                    "form" => $form->createView(),
                ]);
            }
            /**
             * Vérification que le cours ajouté n'est pas déjà passé
             */
            if ($heure_debut < $now) {
                $this->addFlash('danger', 'Problème au niveau des horaires : le cours à modifier est déjà passé.');
                return $this->render("cours/modif.html.twig", [
                    "form" => $form->createView(),
                ]);
            }
            /**
             * Vérification que le début du cours est avant la fin du cours
             */
            if ($heure_debut > $heure_fin) {
                $this->addFlash('danger', 'Problème au niveau des horaires : la fin du cours se passe avant la fin du cours.');
                return $this->render("cours/modif.html.twig", [
                    "form" => $form->createView(),
                ]);
            }
            /**
             * Vérification que les cours ajoutés sont bien dans la plage horaire d'affichage de l'emploi du temps
             */
            if ((int)$heure_debut->format('H') < $debut_affichage || (int)$heure_fin->format('H') > $fin_affichage) {
                $this->addFlash('danger', 'Problème au niveau des horaires : Vérifiez que le cours ajouté est bien dans les horaires définis par l\'administrateur (de .'.$debut_affichage.'.h à .'.$fin_affichage.'.h).');
                return $this->render("cours/modif.html.twig", [
                    "form" => $form->createView(),
                ]);
            }
            $cours->setHeureDebut($heure_debut)->setHeureFin($heure_fin);
            if ($type === 'classe') {
                $id_classe = $cours->getIdClasse()->getId();
                foreach ($prof->getIdClasse()->getValues() as $classe) {
                    if ($classe->getId() === $id_classe) {
                        $cours->setIdClasse($classe);
                        break;
                    }
                }
            } else {
                $id_sousgroupe = $cours->getIdSousgroupe()->getId();
                foreach ($prof->getIdUser()->getSousgroupesvisibles() as $sousgroupe) {
                    if ($sousgroupe->getId() === $id_sousgroupe) {
                        $cours->setIdSousgroupe($sousgroupe);
                        break;
                    }
                }
            }
            try {
                $verif = $checkCoursConflit->verifCours($cours);
            } catch (Exception $e) {
                $this->addFlash('danger', 'Problème dans la vérification des cours');
                return $this->render("cours/modif.html.twig", [
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
                return $this->render("cours/modif.html.twig", [
                    "form" => $form->createView()
                ]);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cours);
            $entityManager->flush();
            $this->addFlash('success', 'Cours modifié !');
            return $this->redirectToRoute('cours.index');
        }

        return $this->render('cours/modif.html.twig', [
            'current_menu' => 'Cours',
            'form' => $form->createView()
        ]);
    }
}