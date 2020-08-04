<?php


namespace App\Controller\Classes;

use App\Entity\Classes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DeleteClasseController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/Classe/Delete", name="classe.delete", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_PERSONNEL')")
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        /**
         * Récupération de l'id du sous-groupe
         */
        if ($request->request->get("id") && substr($request->request->get("id"), 0, 3) === "cl_") {
            $clid = explode('_', $request->request->get('id'));
            $id = is_numeric($clid[1]) ? (int)$clid[1] : -1;
        } else {
            return $this->json([
                'error' => 'Problème dans la détermination du sous-groupe à modifier.'
            ]);
        }
        if ($request->isXmlHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();
            $classe = $this->getDoctrine()->getRepository(Classes::class)->find($id);
            $eleves = $classe->getEleves();
            foreach ($eleves as $eleve){
                $eleve->setIdClasse(null);
                $entityManager->persist($eleve);
            }
            $entityManager->remove($classe);
            $entityManager->flush();
            return $this->json([
                'success' => "La classe " . $classe->getNomClasse() . " à été supprimée."
            ]);
        } else {
            return $this->json([
                'error' => "Ce n'est pas une requête AJAX."
            ]);
        }
    }
}