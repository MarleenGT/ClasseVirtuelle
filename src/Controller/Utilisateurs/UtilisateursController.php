<?php


namespace App\Controller\Utilisateurs;


use App\Entity\Admins;
use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
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
     */
    public function choice(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $user = $request->request->get('user');
            $limit = $request->request->get('limit');
            $offset = $request->request->get('offset')*$limit;
            $search = $request->request->get('search');
            if ($offset < 0){
                $offset = 0;
            }
            if ($user === 'Eleves') {
                $query = $this->getDoctrine()->getRepository(Eleves::class)->findElevesByPages($limit, $offset, $search);
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
            dump($this->render('utilisateurs/listing.html.twig', [
                'response' => $query,
                'user' => $user
            ]));
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
        return $this->render('utilisateurs/index.html.twig', [
            'current_menu' => 'utilisateurs'
        ]);
    }
}