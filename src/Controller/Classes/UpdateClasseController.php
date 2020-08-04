<?php


namespace App\Controller\Classes;

use App\Entity\Classes;
use App\Entity\Eleves;
use App\Entity\Profs;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UpdateClasseController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/Classe/Update", name="classe.update", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_PERSONNEL')")
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        /**
         * Récupération de l'id du sous-groupe
         */
        if ($request->request->get("id") && substr($request->request->get("id"), 0, 3) === "cl_") {
            $clid = explode('_', $request->request->get('id'));
            $id = is_numeric($clid[1]) ? (int)$clid[1] : -1;
        } else {
            return $this->json([
                'error' => 'Problème dans la détermination de la classe à modifier.'
            ]);
        }
        $eleve_list = $request->request->get('eleves') ? $request->request->get('eleves') : [];
        $prof_list = $request->request->get('profs') ? $request->request->get('profs') : [];

        if ($request->isXmlHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();
            $classe = $this->getDoctrine()->getRepository(Classes::class)->find($id);
            $eleves = $this->getDoctrine()->getRepository(Eleves::class)->findBy(['id' => $eleve_list]);
            foreach ($eleves as $eleve) {
                $classe->addEleve($eleve);
            }

            $profs = $this->getDoctrine()->getRepository(Profs::class)->findBy(['id' => $prof_list]);
            foreach ($profs as $prof) {
                $classe->addProf($prof);
            }

            $entityManager->persist($classe);
            $entityManager->flush();
            return $this->json([
                'success' => "La classe " . $classe->getNomClasse() . " à été modifiée."
            ]);
        } else {
            return $this->json([
                'error' => "Ce n'est pas une requête AJAX."
            ]);
        }
    }
}