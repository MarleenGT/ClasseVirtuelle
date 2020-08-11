<?php


namespace App\Controller\Cours;


use App\Controller\CheckRepository\CheckCoursRepo;
use App\Entity\Profs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteCoursController extends AbstractController
{
    /**
     * @param Request $request
     * @param CheckCoursRepo $checkCoursRepo
     * @Route("/Cours/Delete", name="cours.delete", methods={"POST"})
     * @return Response
     */
    public function delete(Request $request, CheckCoursRepo $checkCoursRepo)
    {
        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('delete-cours', $submittedToken)) {
            $session = $request->getSession();
            /**
             * Récupération de répertoire contenant le cours sur lequel agir.
             */
            $repo = $checkCoursRepo->check($request->request->get('date'));
            $id = $request->request->get('id');
            $cours = $this->getDoctrine()->getRepository($repo)->find($id);
            $prof = $this->getDoctrine()->getRepository(Profs::class)->find($session->get('user')->getId());
            /**
             * Vérification de l'auteur du cours et si l'utilisateur peut le modifier
             */
            if (!($this->getUser()->getRoles()[0] === "ROLE_ADMIN" || ($this->getUser()->getRoles()[0] === "ROLE_PROF" && $cours->getIdProf()->getId() === $prof->getId()))) {
                $this->addFlash('danger', "Problème pour la récupérartion du cours.");
                return $this->render('cours/index.html.twig', [
                    'current_menu' => 'cours'
                ]);
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($cours);
            $em->flush();
            $this->addFlash('success', 'Cours supprimé !');
        } else {
            $this->addFlash('danger', 'Token CSRF incorrect.');
        }
        return $this->redirectToRoute('cours.index');
    }

}