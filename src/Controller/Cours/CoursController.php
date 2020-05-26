<?php


namespace App\Controller\Cours;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoursController extends AbstractController
{

    /**
     * @return Response
     * @Route("/cours", name="cours.index")
     */
    public function index(): Response
    {

        // $date = $this->session->get('date')->format('Y-m-d');
        return $this->render('cours/index.html.twig',
            ['date' => '', 'current_menu' => 'cours']
        );
    }

    /**
     * @return Response
     * @Route("/cours", name="cours.previous")
     */
    public function previous(): Response
    {

        // $date = $this->session->get('date');
        // var_dump($date);
        // $this->session->set('date', $date);
        return $this->index();
    }

    /**
     * @return Response
     * @Route("/cours", name="cours.next")
     */
    public function next(): Response
    {
        // $date = $this->session->get('date')->modify('+1 week');
        // $this->session->set('date', $date);
        return $this->index();
    }

    public function setSession()
    {

    }
    public function ajaxAction(Request $request)
    {

    }
}