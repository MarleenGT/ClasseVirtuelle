<?php


namespace App\Controller\Cours;


use App\Controller\CheckRepository\CheckCoursRepo;
use App\Form\Cours\CoursType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateCoursController extends AbstractController
{
    /**
     * @param Request $request
     * @param CheckCoursRepo $checkCoursRepo
     * @Route("/Cours/Update", name="cours.update", methods={"POST"})
     * @return Response
     */
    public function update(Request $request, CheckCoursRepo $checkCoursRepo)
    {
        $session = $request->getSession();
        /**
         * Récupération de répertoire contenant le cours sur lequel agir.
         */
        $repo = $checkCoursRepo->check($request->request->get('date'));
        $id = $request->request->get('id');
        $cours = $this->getDoctrine()->getRepository($repo)->find($id);
        $prof = $session->get('user');
        /**
         * Vérification de l'auteur du cours et si l'utilisateur peut le modifier
         */
        if (!($this->getUser()->getRoles()[0] === "ROLE_ADMIN" || ($this->getUser()->getRoles()[0] === "ROLE_PROF" && $cours->getIdProf()->getId() === $prof->getId()))) {
            $this->addFlash('danger', "Problème pour la récupérartion du cours.");
            return $this->render('cours/index.html.twig', [
                'current_menu' => 'cours'
            ]);
        }
        /**
         * Préparation de tableau de matières pour le formulaire
         */
        $matieres = [];
        foreach ($session->get('user')->getIdMatiere() as $matiere) {
            $matieres[] = $matiere;
        }
        $matieres[] = 'Autre';

        $form = $this->createForm(CoursType::class, $cours, [
            'matieres' => $matieres,
            'classes' => $prof->getIdClasse(),
            'sousgroupes' => $prof->getIdUser()->getSousgroupesVisibles(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cours = $form->getData();
            dump($cours);
            die();
        }

        return $this->render('cours/modif.html.twig', [
            'current_menu' => 'Cours',
            'form' => $form->createView()
        ]);
    }
}