<?php


namespace App\Controller\Utilisateurs;


use App\Entity\Admins;
use App\Entity\Classes;
use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
use App\Form\Classes\AddClasseType;
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
     * @param Request $request
     * @return Response
     * @Route("/Utilisateurs", name="utilisateurs.index")
     */
    public function index(Request $request): Response
    {
        $role = $this->getUser()->getRoles()[0];
        $options = [
            'current_menu' => 'utilisateurs'
        ];

        /**
         * Si l'utilisateur est un administrateur, création du formulaire d'ajout d'une classe
         */
        if ($role === 'ROLE_ADMIN') {
            $cla = new Classes();
            $form = $this->createForm(AddClasseType::class, $cla);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $classe = $form->getData();

                /**
                 * Vérification si la classe à ajouter existe déjà
                 */
                $query = $this->getDoctrine()->getRepository(Classes::class)->findOneBy(['nom_classe' => $classe->getNomClasse()]);
                if (!$query) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($classe);
                    $em->flush();
                    $this->addFlash('success', 'Classe ajoutée !');
                } else {
                    $this->addFlash('danger', 'La classe à ajouter existe déjà.');
                }
            }
            $options['form'] = $form->createView();
        }
        return $this->render('utilisateurs/index.html.twig', $options);
    }
}