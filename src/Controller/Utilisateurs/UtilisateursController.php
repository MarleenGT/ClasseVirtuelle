<?php


namespace App\Controller\Utilisateurs;


use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
use App\Form\AddEleve;
use App\Form\AddPersonnel;
use App\Form\AddProfesseur;
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
     * @return Response
     * @Route("/Utilisateurs/Ajout", name="utilisateurs.add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $util = $request->request->get('typeUtil');
        if ($util === 'Eleves'){
            $task = new Eleves();
            $form = $this->createForm(AddEleve::class, $task);
        } elseif ($util === "Professeurs"){
            $task = new Profs();
            $form = $this->createForm(AddProfesseur::class, $task);
        } elseif ($util === "Personnels"){
            $task = new Personnels();
            $form = $this->createForm(AddPersonnel::class, $task);
        }
    dump($form);
        return $this->render('utilisateurs/add/add.html.twig', ['form' => $form->createView()]);
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
        $form = $this->createForm(AddEleve::class, $task);

        return $this->render('utilisateurs/add/add.html.twig', ['form' => $form->createView()]);
    }

}