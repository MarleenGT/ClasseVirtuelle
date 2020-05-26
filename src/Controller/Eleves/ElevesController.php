<?php


namespace App\Controller\Eleves;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ElevesController extends AbstractController
{
    /**
     * @return Response
     * @Route("/eleves", name="eleve.index")
     */
    public function index(): Response
    {

        return $this->render('eleve/index.html.twig', [
            'current_menu' => 'eleves'
        ]);
    }
}