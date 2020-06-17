<?php


namespace App\Controller\Utilisateurs\Form;


use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
use App\Form\EleveType;
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
     * @Route("/Utilisateurs/Modif", name="utilisateurs.modif")
     */
    public function modifUser(Request $request)
    {
        dump($request);
        if (isset($request->request->all()['modif'])) {
            $modif = $request->request->all()['modif'];
            $user = $modif['type'];
            $id = $modif['id'];
        } else {
            $user = $request->request->get('user');
            $id = $request->request->get('id');
        }
        if ($user === 'Eleves') {
            $obj = $this->getDoctrine()->getRepository(Eleves::class)->find($id);
            $formType = EleveType::class;
        } elseif ($user === 'Professeurs') {
            $obj = $this->getDoctrine()->getRepository(Profs::class)->find($id);
            $formType = ProfesseurType::class;
        } elseif ($user === 'Personnels') {
            $obj = $this->getDoctrine()->getRepository(Personnels::class)->find($id);
            $formType = PersonnelType::class;
        } else {
//            return $this->render('utilisateurs/index.html.twig');
        }
        $form = $this->get('form.factory')->createNamed('modif',$formType, $obj, [
            'id' => $id,
            'type' => $user
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $obj = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($obj);
            $entityManager->flush();

            return $this->render("utilisateurs/index.html.twig", [
                "select" => $user,
                'user' => $user,
            ]);
        }
        $nom = $obj->getNom();
        $prenom = $obj->getPrenom();
        $substrUser = substr($user, 0, strlen($user) - 1);

        return $this->render("utilisateurs/modif.html.twig",[
            "titre" => "Modification de $nom $prenom ($substrUser)",
            "form" => $form->createView()
        ]);
    }
}