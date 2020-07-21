<?php


namespace App\Controller\Utilisateurs\Form;


use App\Entity\Admins;
use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
use App\Form\Utilisateurs\DeleteType;
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
    public function delete(Request $request)
    {
        if (isset($request->request->all()['delete'])) {
            $delete = $request->request->all()['delete'];
            $user = $delete['type'];
            $id = $delete['id'];
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
        } elseif ($user === 'Admins') {
            $obj = $this->getDoctrine()->getRepository(Admins::class)->find($id);
        } else {
            return $this->render('utilisateurs/index.html.twig');
        }

        $form = $this->createForm(DeleteType::class, $obj, [
            'id' => $id,
            'type' => $user
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

        return $this->json([
            "titre" => "Voulez-vous vraiment supprimer $nom $prenom ($substrUser) ?",
            "form" => $this->render("utilisateurs/delete.html.twig", [
                "form" => $form->createView()
            ])
        ]);
    }
}