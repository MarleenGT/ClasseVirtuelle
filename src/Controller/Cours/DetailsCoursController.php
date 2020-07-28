<?php


namespace App\Controller\Cours;


use App\Entity\Cours;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DetailsCoursController extends AbstractController
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/Cours/Details", name="cours.details", methods={"POST"})
     */
    public function details(Request $request): JsonResponse
    {
        $session = $request->getSession();

        /**
         * Vérification de l'id du cours et redirection vers la page de cours si problème
         */
        if (is_numeric($request->request->get('id')) && is_numeric($request->request->get('date'))) {
            $id = (int)$request->request->get('id');
            $date = (int)$request->request->get('date');
        } else {
            return $this->json([
                'titre' => "Problème dans la récupération du cours."
            ]);
        }
        /**
         * Récupération du cours concerné
         */
        $cours = $this->getDoctrine()->getRepository(Cours::class)->find($id);

        /**
         * Création des boutons de modification et suppression du cours
         * si l'utilisateur est le prof créateur ou un administrateur
         */
        if ($this->getUser()->getRoles()[0] === "ROLE_ADMIN" || ($this->getUser()->getRoles()[0] === "ROLE_PROF" && $cours->getIdProf()->getId() === $session->get('user')->getId())) {
            $footer = $this->render("cours/footer.modal.html.twig", [
                'cours' => $cours,
                'date' => $date
            ]);
        } else {
            $footer = "";
        }

        return $this->json([
            "titre" => "Détails du cours",
            "body" => $this->render('cours/details.html.twig', [
                'auteur' => $cours->getIdProf()->getCivilite().' '.$cours->getIdProf()->getNom(),
                'matiere' => $cours->getMatiere(),
                'date' => $cours->getDate()->format("d/m/Y"),
                'debut' => $cours->getHeureDebut()->format("H:i"),
                'fin' => $cours->getHeureFin()->format("H:i"),
                'lien' => $cours->getLien(),
                'commentaire' => $cours->getCommentaire()
            ]),
            "footer" => $footer
        ]);
    }
}