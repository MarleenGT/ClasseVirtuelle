<?php


namespace App\Controller\SousGroupes;

use App\Entity\Eleves;
use App\Entity\Profs;
use App\Entity\Sousgroupes;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddSousGroupeController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/Sousgroupe/Ajouter", name="sousgroupe.add", methods={"POST"})
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        if ($request->request->get("nom") && strlen($request->request->get("nom")) > 0) {
            $nom = $request->request->get('nom');
        } else {
            return $this->json([
                'error' => 'Le nom du sous-groupe doit être renseigné.'
            ]);
        }
        $eleve_list = $request->request->get('eleves') ? $request->request->get('eleves') : [];
        $prof_list = $request->request->get('profs') ? $request->request->get('profs') : [];

        if (count($eleve_list) !== count(array_filter($eleve_list, 'is_numeric')) || count($prof_list) !== count(array_filter($prof_list, 'is_numeric'))) {
            return $this->json([
                'error' => "Erreur dans les listes d'éleves et/ou de professeurs"
            ]);
        }

        if ($request->isXmlHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();
            $createur = $this->getUser();
            $sousgroupe = new Sousgroupes();
            $sousgroupe->setNomSousgroupe($nom)->setIdCreateur($createur);
            $eleves = $this->getDoctrine()->getRepository(Eleves::class)->findBy(['id' => $eleve_list]);
            foreach ($eleves as $eleve) {
                $sousgroupe->addEleve($eleve);
            }
            if (($this->getUser()->getRoles()[0] === "ROLE_PROF")) {
                $sousgroupe->addVisibilite($createur);
            } else {
                $profs = $this->getDoctrine()->getRepository(Profs::class)->findBy(['id' => $prof_list]);
                foreach ($profs as $prof) {
                    $sousgroupe->addVisibilite($prof);
                }
            }
            $date_creation = new DateTime();
            $sousgroupe->setDateCreation($date_creation);
            $entityManager->persist($sousgroupe);
            $entityManager->flush();
            return $this->json([
                'success' => "le sous-groupe $nom à été ajouté."
            ]);
        } else {
            return $this->json([
                'error' => "Ce n'est pas une requête AJAX."
            ]);
        }

    }
}
