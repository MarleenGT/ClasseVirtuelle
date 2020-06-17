<?php


namespace App\Controller\Cours;


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
            $date = $request->query->get('date');


        } else {
            return $this->render('cours/index.html.twig', [
                'error' => 'Ceci n\'est pas une requête AJAX'
            ]);
        }
        return $this->render('cours/content.html.twig', [
            'date' => $date
        ]);
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