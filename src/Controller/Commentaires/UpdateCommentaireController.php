<?php


namespace App\Controller\Commentaires;


use App\Entity\Commentaires;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UpdateCommentaireController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/Dossier/Update", name="commentaire.update", methods="POST")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_PERSONNEL') or is_granted('ROLE_PROF')")
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        $modif_id = array_search("Modifier", $request->request->all());
        if ($modif_id) {
            $modif_com = $this->getDoctrine()->getRepository(Commentaires::class)->find($modif_id);
            if ($modif_com && ($modif_com->getIdAuteur() === $this->getUser() || $this->getUser()->getRoles()[0] === "ROLE_ADMIN")) {
                $em = $this->getDoctrine()->getManager();
                $modif_com->setCommentaire($request->request->get('commentaire'));
                if ($request->request->get("global")) {
                    $modif_com->setGlobal(true);
                }
                $em->persist($modif_com);
                $em->flush();
                $this->addFlash('success', 'Commentaire modifié !');
            } else {
                $this->addFlash('danger', 'Problème dans la modification du commentaire');
            }
        }
        return $this->redirect($request->headers->get('referer'));
    }
}