<?php


namespace App\Controller\Dossier;


use App\Entity\Admins;
use App\Entity\Commentaires;
use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
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
     * @Route("/{id}/{nom}/{prenom}", name="dossier.index")
     * @return Response
     */
    public function index(Request $request)
    {
        $id = $request->get('id');
        $user_id = $this->getUser()->getId();
        $eleve = $this->getDoctrine()->getRepository(Eleves::class)->find($id);

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
        }
        $commentaires = [];
        $commentairesGlobal = $this->getDoctrine()->getRepository(Commentaires::class)->findBy(['id_concerne' => $eleve->getIdUser()->getId(), 'global' => true]);
        $commentairesUser = $this->getDoctrine()->getRepository(Commentaires::class)->findBy(['id_concerne' => $eleve->getIdUser()->getId(), 'global' => false, 'id_auteur' => $user_id]);
        $temp = array_merge($commentairesGlobal, $commentairesUser);
        foreach ($temp as $com) {
            $role = $com->getIdAuteur()->getIdRole()->getNomRole();
            if ($role === "ROLE_PROF") {
                $repo = Profs::class;
            } elseif ($role === "ROLE_PERSONNEL") {
                $repo = Personnels::class;
            } elseif ($role === "ROLE_ADMIN") {
                $repo = Admins::class;
            }
            $auteur = $this->getDoctrine()->getRepository($repo)->findOneBy(['id_user' => $com->getIdAuteur()->getId()]);
            $commentaires[] = [$com, $auteur];
        }

        return $this->render('dossier/index.html.twig', [
            'eleve' => $eleve,
            'commentaires' => array_reverse($commentaires),
            'form' => $form->createView()
        ]);
    }
}