<?php


namespace App\Controller\SousGroupe;

use App\Entity\Eleves;
use App\Entity\Profs;
use App\Entity\Sousgroupes;
use DateInterval;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddSousGroupe extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/Sousgroupe/Ajouter", name="sousgroupe.add", methods={"POST"})
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        if (strlen($request->request->get("nom")) > 0) {
            $nom = $request->request->get('nom');
        } else {
            return $this->json([
                'error' => 'Le nom du sous-groupe doit être renseigné.'
            ]);
        }
        if (count($request->request->get("eleves")) > 0) {
            $eleve_list = $request->request->get('eleves');
        } else {
            return $this->json([
                'error' => 'Le sous-groupe ajouté est vide.'
            ]);
        }
        if (count($request->request->get("profs")) > 0) {
            $prof_list = $request->request->get('profs');
        } else {
            return $this->json([
                'error' => 'Aucun professeur n\'a été ajouté dans le sous-groupe.'
            ]);
        }
        try {
            $temps_validite = new DateInterval("P".(int)$request->request->get('temps')."D");
        } catch (Exception $e) {
            return $this->json([
                'error' => 'Problème lors de la vérification du temps de validité du sous-groupe.'
            ]);
        }
        if ($request->isXmlHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();
            $createur = $this->getUser();
            $sousgroupe = new Sousgroupes();
            $sousgroupe->setNomSousgroupe($nom)->setIdCreateur($createur);
            foreach ($eleve_list as $eleve) {
                $query = $entityManager->getRepository(Eleves::class)->find((int)$eleve["id"]);
                $sousgroupe->addEleve($query);
            }
            if (($this->getUser()->getRoles()[0] === "ROLE_PROF")) {
                $sousgroupe->addVisibilite($createur);
            } else {
                foreach ($prof_list as $prof) {
                    $query = $entityManager->getRepository(Profs::class)->find($prof["id"])->getIdUser();
                    $sousgroupe->addVisibilite($query);
                }
            }
            $date_creation = new DateTime();
            $date_validite = new DateTime();
            $date_validite->add($temps_validite);
            dump($date_validite, $date_creation);
            $sousgroupe->setDateCreation($date_creation)->setValidite($date_validite);
            $entityManager->persist($sousgroupe);
            $entityManager->flush();
            return $this->json([
                'success' => "le sous-groupe $nom à été ajouté."
            ]);
        }
    }
}
