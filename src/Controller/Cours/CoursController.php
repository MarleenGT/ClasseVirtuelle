<?php


namespace App\Controller\Cours;


use App\Entity\Archives;
use App\Entity\Classes;
use App\Entity\Cours;
use App\Entity\DateArchive;
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
     * @param CheckSession $checkSession
     * @return Response
     * @Route("/Cours/ajax", name="cours.ajax", methods={"GET"})
     */
    public function ajax(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $select = explode("_", $request->query->get('select'));
            $archivage = $this->getDoctrine()->getRepository(DateArchive::class)->find(1)->getDateDerniereArchive();
            $debut = $this->getParameter('startTimeTable');
            $fin = $this->getParameter('endTimeTable');
            $timeLundi = (int)$request->query->get('date');
            $timeSamedi = (int)($timeLundi + 5 * 24 * 60 * 60 + 60 * 60 * ($fin - $debut));
            $date_lundi = (new DateTime)->setTimestamp($timeLundi);
            $date_samedi = (new DateTime)->setTimestamp($timeSamedi);

            /**
             * Vérification de la date d'archivage pour savoir si les cours à afficher sont dans la table Archives ou Cours
             */
            $role = $this->getUser()->getRoles()[0];
            $repository = ($archivage < $date_samedi)? Cours::class : Archives::class;
            if ($role === "ROLE_ELEVE"){
                $eleve = $this->getDoctrine()->getRepository(Eleves::class)->findOneBy(['id_user' => $this->getUser()->getId()]);
                $query = $this->findCoursForEleve($eleve, $repository, $date_lundi, $date_samedi);
            } else {
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
            $debut = $this->getParameter('startTimeTable');
            $fin = $this->getParameter('endTimeTable');

            foreach ($query as $cours) {
                $coursArray = [];
                $coursArray['cours'] = $cours;
                $date = strtolower($cours->getHeureDebut()->format('D'));
                $coursArray['float_debut'] = ($this->hours_tofloat($cours->getHeureDebut()->format('H:i')) - $debut) * ($fin - $debut);
                $coursArray['float_fin'] = ($this->hours_tofloat($cours->getHeureFin()->format('H:i')) - $debut) * ($fin - $debut);
                ${$date}[] = $coursArray;
            }
            $hours = [];

            if ($debut > $fin){
                return $this->render('cours/index.html.twig', [
                    'error' => 'Problème dans les paramètres d\'affichage. Contactez l\'administrateur.'
                ]);
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
            return $this->render('cours/index.html.twig', [
                'error' => 'Ceci n\'est pas une requête AJAX'
            ]);
        }
    }


    /**
     * @return Response
     * @Route("/Cours", name="cours.index")
     */
    public function index(): Response
    {
        $role = $this->getUser()->getRoles()[0];
        if ($role === 'ROLE_PERSONNEL' || $role === 'ROLE_ADMIN'){
            $liste_classe = $this->getDoctrine()->getRepository(Classes::class)->findAll();
            $liste_sousgroupe = $this->getDoctrine()->getRepository(Sousgroupes::class)->findAll();
            $liste_prof = $this->getDoctrine()->getRepository(Profs::class)->findAll();
        } else {
            $liste_classe = [];
            $liste_sousgroupe = [];
            $liste_prof = [];
        }

        return $this->render('cours/index.html.twig', [
            'current_menu' => 'cours',
            'start_hour' => $this->getParameter('startTimeTable'),
            'liste_classe' => $liste_classe,
            'liste_sousgroupe' => $liste_sousgroupe,
            'liste_prof' => $liste_prof,
        ]);
    }

    private function hours_tofloat($val)
    {
        if (empty($val)) {
            return 0;
        }
        $parts = explode(':', $val);
        return $parts[0] + floor(($parts[1] / 60) * 100) / 100;
    }

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