<?php


namespace App\Controller\Cours;


use App\Entity\Cours;
use DateTime;
use DateTimeZone;
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
            $lundi = (new \DateTime)->setTimestamp($timeLundi);
            $samedi = (new \DateTime)->setTimestamp($timeSamedi);
            $query = $this->getDoctrine()->getRepository(Cours::class)->findCoursByWeek($lundi, $samedi);
            dump($query);
            return $this->render('cours/content.html.twig', [
                'lundi' => $lundi,
                'samedi' => $samedi,
                'query' => $query
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
}