<?php


namespace App\Controller\Cours;


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
        if ($request->isXmlHttpRequest()) {
            $timeLundi = (int) $request->query->get('date');
            $timeSamedi = (int) ($timeLundi + 5*24*60*60 + 60*60*10);
            $date_lundi = (new DateTime)->setTimestamp($timeLundi);
            $date_samedi = (new DateTime)->setTimestamp($timeSamedi);
            $query = $this->getDoctrine()->getRepository(Cours::class)->findCoursByWeek($date_lundi, $date_samedi);

            $mon = [];
            $tue = [];
            $wed = [];
            $thu = [];
            $fri = [];
            $sat = [];
            dump($query);
            foreach ($query as $cours){
                $date = strtolower($cours['heure_debut']->format('D'));
                $cours['heure_debut'] = $this->hours_tofloat($cours['heure_debut']->format('H:i'));
                $cours['heure_fin'] = $this->hours_tofloat($cours['heure_fin']->format('H:i'));
                ${$date}[] = $cours;
            }
            return $this->render('cours/content.html.twig', [
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
    private function hours_tofloat($val){
        if (empty($val)) {
            return 0;
        }
        $parts = explode(':', $val);
        return $parts[0] + floor(($parts[1]/60)*100) / 100;
    }
}