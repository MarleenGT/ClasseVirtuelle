<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/errors", name="error.display")
     */
    public function display(Request $request)
    {
        return $this->render('errors/exception.html.twig');
    }
}