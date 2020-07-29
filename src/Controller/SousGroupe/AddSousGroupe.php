<?php


namespace App\Controller\SousGroupe;

use App\Entity\Eleves;
use App\Entity\Profs;
use App\Entity\Sousgroupes;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddSousGroupe extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/Sousgroupe/Ajouter", name="sousgroupe.add", methods={"POST"})
     */
    public function add(Request $request)
    {
        dump($request);
        if ($request->request->get("nom") && strlen($request->request->get("nom")) > 0) {
            $nom = $request->request->get('nom');
        } else {
            return $this->json([
                'error' => 'Le nom du sous-groupe doit être renseigné.'
            ]);
        }
        if ($request->request->get("eleves") && count($request->request->get("eleves")) > 0) {
            $eleve_list = $request->request->get('eleves');
        } else {
            return $this->json([
                'error' => 'Le sous-groupe ajouté est vide.'
            ]);
        }
        if ($request->request->get("profs") && count($request->request->get("profs")) > 0) {
            $prof_list = $request->request->get('profs');
        } else {
            return $this->json([
                'error' => 'Aucun professeur n\'a été ajouté dans le sous-groupe.'
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
            $sousgroupe->setDateCreation($date_creation);
            $entityManager->persist($sousgroupe);
            $entityManager->flush();
            return $this->json([
                'success' => "le sous-groupe $nom à été ajouté."
            ]);
        } else {
            return $this->redirectToRoute('utilisateurs.index');
        }

    }
}
