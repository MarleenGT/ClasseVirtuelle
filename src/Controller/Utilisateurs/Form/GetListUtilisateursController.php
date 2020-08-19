<?php


namespace App\Controller\Utilisateurs\Form;

use App\Entity\Eleves;
use App\Entity\Profs;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GetListUtilisateursController extends AbstractController
{
    /**
     * @Route("/List", name="getlistutilisateurs.getList", methods="POST")
     * @param Request $request
     * @return JsonResponse
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_PERSONNEL') or is_granted('ROLE_PROF')")
     */
    public function getList(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            /**
             * Récupération du type et id de la liste à récupérer
             */
            $value = explode('_', $request->request->get("value"));
            if (($value[0] === "cl" || $value[0] === "sg") && is_numeric($value[1])) {
                $type = $value[0];
                $id = (int)$value[1];
            } else {
                return $this->json([
                    'error' => "Problème dans la requête."
                ]);
            }

            if ($type === "cl" && ($this->getUser()->getRoles()[0] === "ROLE_PERSONNEL" || $this->getUser()->getRoles()[0] === "ROLE_ADMIN")) {
                $eleveListe = $this->getDoctrine()->getRepository(Eleves::class)->findElevesByClasseId($id);
                $profListe = $this->getDoctrine()->getRepository(Profs::class)->findProfsByClasseId($id);
            } else {
                $eleveListe = $this->getDoctrine()->getRepository(Eleves::class)->findElevesBySousgroupeId($id);
                if ($this->getUser()->getRoles()[0] !== "ROLE_PROF") {
                    $profListe = $this->getDoctrine()->getRepository(Profs::class)->findProfsBySousgroupeId($id);
                } else {
                    $profListe = [];
                }

            }
        }
        return $this->json([
            'eleves' => $eleveListe,
            'profs' => $profListe
        ]);
    }
}