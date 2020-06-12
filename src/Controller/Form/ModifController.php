<?php


namespace App\Controller\Form;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModifController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/Utilisateurs/Modif", name="utilisateurs.modifUser", methods={"POST"})
     */
    public function modifUser(Request $request)
    {
        dump($request);
        return $this->render("utilisateurs/modalModif.html.twig",[
            "request" => $request
        ]);
    }
}