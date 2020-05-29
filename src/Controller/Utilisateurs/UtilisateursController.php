<?php


namespace App\Controller\Utilisateurs;


use App\Entity\Eleves;
use App\Form\EleveType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateursController extends AbstractController
{
    /**
     * @return Response
     * @Route("/Utilisateurs", name="utilisateurs.index")
     */
    public function index(): Response
    {
        return $this->render('utilisateurs/index.html.twig', [
            'current_menu' => 'utilisateurs'
        ]);
    }


    /**
     * @param Request $request
     * @Route("/Utilisateurs", name="utilisateurs.choice", methods={"GET"})
     * @return Response
     */
    public function choice(Request $request): Response
    {

        if ($request->isXmlHttpRequest()) {

        }

        $task = new Eleves();
        $form = $this->createForm(EleveType::class, $task);

        return $this->render('utilisateurs/add/add.html.twig', ['form' => $form->createView()]);
    }

}