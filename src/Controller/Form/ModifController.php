<?php


namespace App\Controller\Form;


use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
use App\Form\EleveType;
use App\Form\ModifEleveType;
use App\Form\PersonnelType;
use App\Form\ProfesseurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModifController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/Utilisateurs/Modif/{user}/{id}", name="utilisateurs.modifUser")
     */
    public function modifUser(Request $request)
    {
        $util = $request->get('user');
        $id = $request->get('id');
        if ($util === 'Eleves') {
            $obj = $this->getDoctrine()->getRepository(Eleves::class)->find($id);
            $formType = ModifEleveType::class;
        } elseif ($util === 'Professeurs') {
            $obj = $this->getDoctrine()->getRepository(Profs::class)->find($id);
            $formType = ProfesseurType::class;
        } elseif ($util === 'Personnels') {
            $obj = $this->getDoctrine()->getRepository(Personnels::class)->find($id);
            $formType = PersonnelType::class;
        } else {
            return $this->render('utilisateurs/index.html.twig');
        }
        dump($obj);
        $form = $this->createForm($formType, $obj);

        return $this->render("utilisateurs/modif.html.twig",[
            "form" => $form->createView()
        ]);
    }
}