<?php


namespace App\Controller\SousGroupes;

use App\Entity\Eleves;
use App\Entity\Profs;
use App\Entity\Sousgroupes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UpdateSousGroupeController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/Sousgroupe/Update", name="sousgroupe.update", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_PERSONNEL') or is_granted('ROLE_PROF')")
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        /**
         * Récupération de l'id du sous-groupe
         */
        if ($request->request->get("id") && substr($request->request->get("id"), 0, 3) === "sg_") {
            $sgid = explode('_', $request->request->get('id'));

        } else {
            return $this->json([
                'error' => 'Problème dans la détermination du sous-groupe à modifier.'
            ]);
        }

        if (is_numeric($sgid[1])) {
            $id =  (int)$sgid[1];
        } else {
            return $this->json([
                'error' => 'Problème dans la détermination du sous-groupe à modifier.'
            ]);
        }

        if ($request->request->get("nom") && strlen($request->request->get("nom")) > 0) {
            $nom = filter_var($request->request->get('nom'), FILTER_SANITIZE_STRING);
        } else {
            return $this->json([
                'error' => 'Le nom du sous-groupe doit être ajouté.'
            ]);
        }
        $eleve_list = $request->request->get('eleves') ? $request->request->get('eleves') : [];
        $prof_list = $request->request->get('profs') ? $request->request->get('profs') : [];

        /**
         * Vérification si l'ensemble des id des listes sont de type numérique
         */
        if (count($eleve_list) !== count(array_filter($eleve_list, 'is_numeric')) || count($prof_list) !== count(array_filter($prof_list, 'is_numeric'))) {
            return $this->json([
                'error' => "Erreur dans les listes d'éleves et/ou de professeurs"
            ]);
        }

        if ($request->isXmlHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();
            $sousgroupe = $this->getDoctrine()->getRepository(Sousgroupes::class)->find($id);
            $sousgroupe->setNomSousgroupe($nom);
            foreach ($sousgroupe->getEleves() as $eleve){
                $sousgroupe->removeEleve($eleve);
            }
            $eleves = $this->getDoctrine()->getRepository(Eleves::class)->findBy(['id' => $eleve_list]);
            foreach ($eleves as $eleve) {
                $sousgroupe->addEleve($eleve);
            }
            if (($this->getUser()->getRoles()[0] !== "ROLE_PROF")) {
                $profs = $this->getDoctrine()->getRepository(Profs::class)->findBy(['id' => $prof_list]);
                foreach ($profs as $prof) {
                    $sousgroupe->addVisibilite($prof->getIdUser());
                }
            }
            $entityManager->persist($sousgroupe);
            $entityManager->flush();
            return $this->json([
                'success' => "le sous-groupe ".$sousgroupe->getNomSousgroupe()." à été modifié."
            ]);
        } else {
            return $this->json([
                'error' => "Ce n'est pas une requête AJAX."
            ]);
        }
    }
}