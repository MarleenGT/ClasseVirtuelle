<?php


namespace App\Controller\SousGroupes;

use App\Entity\Sousgroupes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DeleteSousgroupeController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/Sousgroupe/Delete", name="sousgroupe.delete", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_PERSONNEL') or is_granted('ROLE_PROF')")
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        /**
         * Récupération de l'id du sous-groupe
         */
        if ($request->request->get("id") && substr($request->request->get("id"), 0, 3) === "sg_") {
            $sgid = explode('_', $request->request->get('id'));
            $id = is_numeric($sgid[1]) ? (int)$sgid[1] : -1;
        } else {
            return $this->json([
                'error' => 'Problème dans la détermination du sous-groupe à modifier.'
            ]);
        }
        if ($request->isXmlHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();
            $sousgroupe = $this->getDoctrine()->getRepository(Sousgroupes::class)->find($id);
            if (($this->getUser()->getRoles()[0] === "ROLE_PROF" && $sousgroupe->getIdCreateur()->getId() === $this->getUser()->getId())
                ||  $this->getUser()->getRoles()[0] === "ROLE_PERSONNEL" || $this->getUser()->getRoles()[0] === "ROLE_ADMIN"){
                $entityManager->remove($sousgroupe);
                $entityManager->flush();
                return $this->json([
                    'success' => "Le sous-groupe " . $sousgroupe->getNomSousgroupe() . " à été supprimé."
                ]);
            } else {
                return $this->json([
                    'error' => "Problème dans la détermination du sous-groupe à modifier."
                ]);
            }

        } else {
            return $this->json([
                'error' => "Ce n'est pas une requête AJAX."
            ]);
        }
    }
}