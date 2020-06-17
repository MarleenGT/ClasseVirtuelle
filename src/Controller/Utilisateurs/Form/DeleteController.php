<?php


namespace App\Controller\Utilisateurs\Form;


use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
use App\Form\DeleteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/Utilisateurs/Delete", name="utilisateurs.delete", methods={"POST"})
     */
    public function deleteUser(Request $request)
    {
        if (isset($request->request->all()['delete'])) {
            $name = explode('-', array_key_first($request->request->all()['delete']));
            $user = $name[0];
            $id = $name[1];
        } else {
            $user = $request->request->get('user');
            $id = $request->request->get('id');
        }

        if ($user === 'Eleves') {
            $obj = $this->getDoctrine()->getRepository(Eleves::class)->find($id);
        } elseif ($user === 'Professeurs') {
            $obj = $this->getDoctrine()->getRepository(Profs::class)->find($id);
        } elseif ($user === 'Personnels') {
            $obj = $this->getDoctrine()->getRepository(Personnels::class)->find($id);
        } else {
            return $this->render('utilisateurs/index.html.twig');
        }
        $form = $this->createForm(DeleteType::class, $obj, [
            'user' => $user,
            'id' => $id
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $obj = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($obj);
            $entityManager->flush();

            return $this->render("utilisateurs/index.html.twig", [
                "select" => $user
            ]);
        }

        $nom = $obj->getNom();
        $prenom = $obj->getPrenom();
        $substrUser = substr($user, 0, strlen($user) - 1);

        return $this->render("utilisateurs/delete.html.twig", [
            "modal" => "Voulez-vous vraiment supprimer $nom $prenom ($substrUser) ?",
            "submit" => $form->createView()
        ]);
    }
}