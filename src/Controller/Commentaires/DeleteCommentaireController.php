<?php


namespace App\Controller\Commentaires;


use App\Entity\Commentaires;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DeleteCommentaireController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/Dossier/Delete", name="commentaire.delete", methods="POST")
     */
    public function delete(Request $request)
    {
        $delete_id = array_search("Supprimer", $request->request->all());
        if ($delete_id) {
            $delete_com = $this->getDoctrine()->getRepository(Commentaires::class)->find($delete_id);
            if ($delete_com && ($delete_com->getIdAuteur() === $this->getUser() || $this->getUser()->getRoles()[0] === "ROLE_ADMIN")) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($delete_com);
                $em->flush();
                $this->addFlash('success', 'Commentaire supprimé !');
            } else {
                $this->addFlash('danger', 'Problème dans la suppression du commentaire');
            }
        }
        return $this->redirect($request->headers->get('referer'));
    }
}