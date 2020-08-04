<?php


namespace App\Controller\Classes;


use App\Entity\Classes;
use App\Entity\Eleves;
use App\Entity\Profs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddClasseController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/Classe/Ajouter", name="classe.add", methods={"POST"})
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        /**
         * Récupération et vérification du nom
         */
        if ($request->request->get("nom") && strlen($request->request->get("nom")) > 0) {
            $nom = filter_var($request->request->get('nom'), FILTER_SANITIZE_STRING);
        } else {
            return $this->json([
                'error' => 'Le nom de la classe doit être renseigné.'
            ]);
        }
        /**
         * Récupération et vérification des listes d'id
         */
        $idEleve = $request->request->get('eleves') ? $request->request->get('eleves') : [];
        $idProfs = $request->request->get('profs') ? $request->request->get('profs') : [];

        if (count($idEleve) !== count(array_filter($idEleve, 'is_numeric')) || count($idProfs) !== count(array_filter($idProfs, 'is_numeric'))) {
            return $this->json([
                'error' => "Erreur dans les listes d'éleves et/ou de professeurs"
            ]);
        }
        if ($request->isXmlHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();
            $classe = new Classes();
            $classe->setNomClasse($nom);
            $eleves = $this->getDoctrine()->getRepository(Eleves::class)->findBy(['id' => $idEleve]);
            $profs = $this->getDoctrine()->getRepository(Profs::class)->findBy(['id' => $idProfs]);
            foreach ($eleves as $eleve) {
                $classe->addEleve($eleve);
            }
            foreach ($profs as $prof) {
                $classe->addProf($prof);
            }
            $entityManager->persist($classe);
            $entityManager->flush();
            return $this->json([
                'success' => "La classe $nom à été ajoutée."
            ]);
        } else {
            return $this->json([
                'error' => "Ce n'est pas une requête AJAX."
            ]);
        }

    }
}