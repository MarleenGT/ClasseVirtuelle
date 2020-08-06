<?php


namespace App\Controller\Utilisateurs;


use App\Entity\Admins;
use App\Entity\Classes;
use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
use App\Entity\Sousgroupes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateursController extends AbstractController
{

    /**
     * @param Request $request
     * @Route("/Utilisateurs/ajax", name="utilisateurs.ajax", methods={"POST"})
     * @return Response
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_PERSONNEL') or is_granted('ROLE_PROF')")
     */
    public function choice(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $user = $request->request->get('user');
            $limit = $request->request->get('limit');
            $offset = $request->request->get('offset') * $limit;
            $search = $request->request->get('search');
            if ($offset < 0) {
                $offset = 0;
            }
            if ($user === 'Eleves') {
                $query = $this->getDoctrine()->getRepository(Eleves::class)->findElevesByPages($limit, $offset, $search);
                foreach ($query as $key => $eleve) {
                    $query[$key]['sousgroupe'] = explode(',', $eleve['sousgroupe']);
                }
            } elseif ($user === 'Professeurs') {
                $query = $this->getDoctrine()->getRepository(Profs::class)->findProfsByPages($limit, $offset, $search);
            } elseif ($user === 'Personnels') {
                $query = $this->getDoctrine()->getRepository(Personnels::class)->findPersonnelsByPages($limit, $offset, $search);
            } elseif ($user === 'Admins') {
                $query = $this->getDoctrine()->getRepository(Admins::class)->findAdminsByPages($limit, $offset, $search);
            } else {
                return $this->render('utilisateurs/listing.html.twig', [
                    'error' => 'Problème dans la requête'
                ]);
            }
            return $this->render('utilisateurs/listing.html.twig', [
                'response' => $query,
                'user' => $user
            ]);
        } else {
            return $this->render('utilisateurs/listing.html.twig', [
                'error' => 'Ceci n\'est pas une requête AJAX'
            ]);
        }

    }

    /**
     * @return Response
     * @Route("/Utilisateurs", name="utilisateurs.index")
     */
    public function index(): Response
    {
        $role = $this->getUser()->getRoles()[0];
        $options = [
            'current_menu' => 'utilisateurs'
        ];

        /**
         * Création des listes de modification de classe/sous-groupe
         */
        $classes = [];
        if ($role === 'ROLE_PROF') {
            $sousgroupes = $this->getDoctrine()->getRepository(Sousgroupes::class)->findBy(['id_createur' => $this->getUser()->getId()]);
        } elseif ($role === 'ROLE_PERSONNEL') {
            $classes = $this->getDoctrine()->getRepository(Classes::class)->findAll();
            $sousgroupes = $this->getDoctrine()->getRepository(Sousgroupes::class)->findBy(['id_createur' => $this->getUser()->getId()]);
        } elseif ($role === 'ROLE_ADMIN') {
            $classes = $this->getDoctrine()->getRepository(Classes::class)->findAll();
            $sousgroupes = $this->getDoctrine()->getRepository(Sousgroupes::class)->findAll();
        } else {
            return $this->forward('App\Controller\LoginController::logout');
        }
        $options['classes'] = $classes;
        $options['sousgroupes'] = $sousgroupes;

        return $this->render('utilisateurs/index.html.twig', $options);
    }
}