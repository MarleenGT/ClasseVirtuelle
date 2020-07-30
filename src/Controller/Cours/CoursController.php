<?php


namespace App\Controller\Cours;


use App\Controller\CheckRepository\CheckCoursRepo;
use App\Entity\Classes;
use App\Entity\Eleves;
use App\Entity\Profs;
use App\Entity\Sousgroupes;
use App\Service\CheckSession;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoursController extends AbstractController
{
    /**
     * @param Request $request
     * @param CheckCoursRepo $checkCoursRepo
     * @return Response
     * @Route("/Cours/ajax", name="cours.ajax", methods={"GET"})
     */
    public function ajax(Request $request, CheckCoursRepo $checkCoursRepo): Response
    {
        if ($request->isXmlHttpRequest()) {

            /**
             * Récupération et nettoyage du choix de l'emploi du temps
             */
            $select = explode("_", filter_var($request->query->get('select'), FILTER_SANITIZE_STRING));
            $debut = $this->getParameter('startTimeTable');
            $fin = $this->getParameter('endTimeTable');

            /**
             * Récupération et vérification de l'heure du début de l'affichage de l'emploi du temps
             */
            if (is_numeric($request->query->get('date'))) {
                $timeLundi = (int)$request->query->get('date');
            } else {
                $this->addFlash('danger', "Problème pour la récupération du jour. Vérifiez si javascript est activé.");
                return $this->render('cours/index.html.twig');
            }
            $timeSamedi = $timeLundi + 5 * 24 * 60 * 60 + 60 * 60 * ($fin - $debut);
            $date_lundi = (new DateTime)->setTimestamp($timeLundi);
            $date_samedi = (new DateTime)->setTimestamp($timeSamedi);

            $role = $this->getUser()->getRoles()[0];

            /**
             * Vérification de la date d'archivage pour savoir si les cours à afficher sont dans la table Archives ou Cours
             */
            $repository = $checkCoursRepo->check($timeLundi);
            if ($role === "ROLE_ELEVE"){
                $eleve = $this->getDoctrine()->getRepository(Eleves::class)->findOneBy(['id_user' => $this->getUser()->getId()]);
                if (!$eleve) {
                    $this->addFlash('danger', 'Problème pour la récupération de l\'emploi de temps. Contacter l\'administrateur' );
                    return $this->render('cours/index.html.twig');
                }
                $query = $this->findCoursForEleve($eleve, $repository, $date_lundi, $date_samedi);
            } else {

                /**
                 * Récupération de l'emploi du temps correspondant à l'option choisie
                 */
                if ($select[0] === 'pr'){
                    if ($role === "ROLE_PROF"){
                        $profId = $this->getDoctrine()->getRepository(Profs::class)->findOneBy(['id_user' => $this->getUser()->getId()])->getId();
                    } else {
                        $profId = $select[1];
                    }
                    $query = $this->getDoctrine()->getRepository($repository)->findCoursByWeekAndByProf($date_lundi, $date_samedi, $profId);
                } elseif ($select[0] === "cl"){
                    $query = $this->getDoctrine()->getRepository($repository)->findCoursByWeekAndByClasse($date_lundi, $date_samedi, $select[1]);
                } elseif ($select[0] === "sg"){
                    $query = $this->getDoctrine()->getRepository($repository)->findCoursByWeekAndBySousgroupe($date_lundi, $date_samedi, $select[1]);
                }
            }
            $mon = [];
            $tue = [];
            $wed = [];
            $thu = [];
            $fri = [];
            $sat = [];

            /**
             * Stockage de chaque cours récupéré dans le tableau correspondant au jour du cours.
             * Création des variables nécessaires pour le positionnement des cours dans l'emploi du temps
             */
            foreach ($query as $cours) {
                $coursArray = [];
                $coursArray['cours'] = $cours;
                $date = strtolower($cours->getHeureDebut()->format('D'));
                $coursArray['float_debut'] = ($this->hours_tofloat($cours->getHeureDebut()->format('H:i')) - $debut) * ($fin - $debut);
                $coursArray['float_fin'] = ($this->hours_tofloat($cours->getHeureFin()->format('H:i')) - $debut) * ($fin - $debut);
                ${$date}[] = $coursArray;
            }
            $hours = [];

            /**
             * Vérification des valeurs d'environnement de l'emploi du temps.
             */
            if ($debut > $fin){
                $this->addFlash('danger', "Les valeurs correspondant aux heures de début et de la fin de la journée sont incorrectes. Contactez l'administrateur.");
                return $this->render('cours/index.html.twig');
            }
            for ($i = $debut; $i < $fin; $i++) {
                $hours[] = $i.'h';
            }
            return $this->render('cours/content.html.twig', [
                'debut_cours' => $debut,
                'fin_cours' => $fin,
                'hours' => $hours,
                'date_lundi' => $date_lundi,
                'date_samedi' => $date_samedi,
                'lundi' => $mon,
                'mardi' => $tue,
                'mercredi' => $wed,
                'jeudi' => $thu,
                'vendredi' => $fri,
                'samedi' => $sat
            ]);
        } else {
            $this->addFlash('danger', "La requête AJAX est incorrecte. Contactez l'administrateur.");
            return $this->render('cours/index.html.twig');
        }
    }


    /**
     * @param Request $request
     * @param CheckSession $checkSession
     * @return Response
     * @Route("/Cours", name="cours.index")
     */
    public function index(Request $request, CheckSession $checkSession): Response
    {
        $session = $checkSession->getSession($request);
        /**
         * Récupération des listes de profs/classes/sous-groupes en fonction de l'utilisateur
         */
        $role = $this->getUser()->getRoles()[0];
        if ($role === 'ROLE_PERSONNEL' || $role === 'ROLE_ADMIN'){
            $liste_classe = $this->getDoctrine()->getRepository(Classes::class)->findAll();
            $liste_sousgroupe = $this->getDoctrine()->getRepository(Sousgroupes::class)->findAll();
            $liste_prof = $this->getDoctrine()->getRepository(Profs::class)->findAll();
        } else if ($role === 'ROLE_PROF') {
            $liste_classe = $session->get('user')->getIdClasse();
            $liste_sousgroupe = $this->getUser()->getSousgroupesvisibles();
            $liste_prof = $session->get('user');
        }

        return $this->render('cours/index.html.twig', [
            'current_menu' => 'cours',
            'start_hour' => $this->getParameter('startTimeTable'),
            'liste_classe' => $liste_classe,
            'liste_sousgroupe' => $liste_sousgroupe,
            'liste_prof' => $liste_prof,
        ]);
    }

    /**
     * @param $val
     * @return int
     * Transformation des horaires du cours en nombres pour le positionnement dans l'emploi du temps.
     */
    private function hours_tofloat($val): int
    {
        if (empty($val)) {
            return 0;
        }
        $parts = explode(':', $val);
        return $parts[0] + floor(($parts[1] / 60) * 100) / 100;
    }

    /**
     * @param Eleves $eleve
     * @param $repository
     * @param $heure_debut
     * @param $heure_fin
     * @return array
     * Récupération des cours de l'élève.
     */
    private function findCoursForEleve(Eleves $eleve, $repository, $heure_debut, $heure_fin): array
    {
        $query = [];
        $classe = $eleve->getIdClasse();
        $sousgroupeArray = $eleve->getIdSousgroupe();

        $repo = $this->getDoctrine()->getRepository($repository);
        $queryClasse = $repo->findCoursByClasse($classe, $heure_debut, $heure_fin);
        foreach ($queryClasse as $cours){
            $query[] = $cours;
        }
        foreach ($sousgroupeArray as $sousgroupe){
            $querySousgroupe = $repo->findCoursBySousgroupe($sousgroupe, $heure_debut, $heure_fin);
            foreach ($querySousgroupe as $cours){
                $query[] = $cours;
            }
        }
        return $query;
    }
}