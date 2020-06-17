<?php


namespace App\Controller\Utilisateurs\Form;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingFormController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/Utilisateurs/{user}/{id}", name="utilisateurs.choixModif")
     */
    public function choixModif(Request $request)
    {
        $req = $request->request->all();
        $choix = array_key_first($req);
        $sujet = explode("_", $req[$choix]);

        if($choix === "delete"){
            return $this->forward('App\Controller\Form\DeleteController::deleteUser', [
                'user'  => $sujet[0],
                'id' => $sujet[1]
            ]);
        } elseif ($choix === "update"){
            return $this->forward('App\Controller\Form\ModifController::modifUser', [
                'user'  => $sujet[0],
                'id' => $sujet[1]
                ]);
        } else {
            return $this->render('utilisateurs/index.html.twig');
        }
    }
}