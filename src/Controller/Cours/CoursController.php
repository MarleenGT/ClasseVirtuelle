<?php


namespace App\Controller\Cours;


use App\Entity\Archives;
use App\Entity\Cours;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoursController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/Cours/ajax", name="cours.ajax", methods={"GET"})
     */
    public function ajax(Request $request): Response
    {
        if ($request->hasSession()) {
            $session = $request->getSession();
        } else {
            $this->redirectToRoute('app_logout', [
                'error' => 'La session a expiré. Veuillez vous reconnecter.'
            ]);
        }
        if ($request->isXmlHttpRequest()) {
            $archivage = $session->get('date_archivage');
            $debut = $this->getParameter('startTimeTable');
            $fin = $this->getParameter('endTimeTable');
            $timeLundi = (int)$request->query->get('date');
            $timeSamedi = (int)($timeLundi + 5 * 24 * 60 * 60 + 60 * 60 * ($fin - $debut));
            $date_lundi = (new DateTime)->setTimestamp($timeLundi);
            $date_samedi = (new DateTime)->setTimestamp($timeSamedi);

            /**
             * Vérification de la date d'archivage pour savoir si les cours à afficher sont dans la table Archives ou Cours
             */
            $repository = ($archivage < $date_samedi)? Cours::class : Archives::class;
            $query = $this->getDoctrine()->getRepository($repository)->findCoursByWeekAndByProf($date_lundi, $date_samedi, $session->get('id'));

            $mon = [];
            $tue = [];
            $wed = [];
            $thu = [];
            $fri = [];
            $sat = [];
            $debut = $this->getParameter('startTimeTable');
            $fin = $this->getParameter('endTimeTable');

            foreach ($query as $cours) {
                $date = strtolower($cours['heure_debut']->format('D'));
                $cours['float_debut'] = ($this->hours_tofloat($cours['heure_debut']->format('H:i')) - $debut) * ($fin - $debut);
                $cours['float_fin'] = ($this->hours_tofloat($cours['heure_fin']->format('H:i')) - $debut) * ($fin - $debut);
                ${$date}[] = $cours;
            }
            $hours = [];

            if ($debut > $fin){
                return $this->render('cours/index.html.twig', [
                    'error' => 'Problème dans les paramètres d\'affichage. Contactez l\'administrateur.'
                ]);
            }
            for ($i = $debut; $i <= $fin; $i++) {
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
        return $this->render('cours/index.html.twig', [
            'current_menu' => 'cours'
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
}