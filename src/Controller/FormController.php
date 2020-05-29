<?php


namespace App\Controller;


use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
use App\Entity\Users;
use App\Form\EleveType;
use App\Form\PersonnelType;
use App\Form\ProfesseurType;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/Utilisateurs/Ajout", name="utilisateurs.add", methods={"POST"})
     */
    public function add(Request $request)
    {
        $util = $request->request->get('typeUtil');

        if ($util === 'Eleves'){
            $user = new Eleves();
            $form = $this->createForm(EleveType::class, $user);
        } elseif ($util === "Professeurs"){
            $user = new Profs();
            $form = $this->createForm(ProfesseurType::class, $user);
        } elseif ($util === "Personnels"){
            $user = new Personnels();
            $form = $this->createForm(PersonnelType::class, $user);
        } else {
            $form = [];
        }

        $user2 = new Users();
        $form2 = $this->createForm(UserType::class, $user2);

        dump($form->getData(), $form2);
        return $this->render('utilisateurs/add/add.html.twig', [
            'form' => $form->createView(),
            'form2' => $form2->createView(),
        ]);
    }
}