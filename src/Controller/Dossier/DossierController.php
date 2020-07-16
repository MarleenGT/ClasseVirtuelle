<?php


namespace App\Controller\Dossier;


use App\Entity\Commentaires;
use App\Entity\Eleves;
use App\Form\Commentaire\CommentaireType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/Dossier")
 */
class DossierController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/{id}/{nom}/{prenom}", name="dossier.index", methods="POST")
     * @return Response
     */
    public function check(Request $request)
    {
        $id = $request->get('id');
        $user_id = $this->getUser()->getId();
        $eleve = $this->getDoctrine()->getRepository(Eleves::class)->find($id);
        $commentairesGlobal = $this->getDoctrine()->getRepository(Commentaires::class)->findBy(['id_concerne' => $eleve->getIdUser()->getId(), 'global' => true]);
        $commentairesUser = $this->getDoctrine()->getRepository(Commentaires::class)->findBy(['id_concerne' => $eleve->getIdUser()->getId(), 'global' => false, 'id_auteur' => $user_id]);
        dump($commentairesUser, $commentairesGlobal);
        $commentaires = array_merge($commentairesGlobal, $commentairesUser);
        $comm = new Commentaires();
        $form = $this->createForm(CommentaireType::class, $comm);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $obj = $form->getData();
            $date = new DateTime();
            $auteur = $this->getUser();
            $obj->setIdConcerne($eleve->getIdUser())->setIdAuteur($auteur)->setDate($date);
            $entity = $this->getDoctrine()->getManager();
            $entity->persist($obj);
            $entity->flush();

            return $this->render('utilisateurs/index.html.twig', [
                'select' => 'Eleves',

            ]);
        }

        return $this->render('dossier/index.html.twig', [
            'eleve' => $eleve,
            'commentaires' => $commentaires,
            'form' => $form->createView()
        ]);
    }
}