<?php


namespace App\Controller;


use App\Entity\Classes;
use App\Entity\Sousgroupes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/Listes")
 */
class ListeController extends AbstractController
{
    /**
     * @Route ("", name="listes.index")
     */
    public function index()
    {
        $querySousgroupes = [];
        $role = $this->getUser()->getRoles()[0];
        if ($role === "ROLE_PROF") {
            $querySousgroupes = $this->getDoctrine()->getRepository(Sousgroupes::class)->getSousgroupesCreesByProf($this->getUser());
        } elseif ($role === "ROLE_PERSONNEL" || $role === "ROLE_ADMIN"){
            $querySousgroupes = $this->getDoctrine()->getRepository(Sousgroupes::class)->findAll();
        }
        $queryClasses = $this->getDoctrine()->getRepository(Classes::class)->findAll();
        return $this->render('listes/index.html.twig', [
            'current_menu' => 'listes',
            'sousgroupes' => $querySousgroupes,
            'classes' => $queryClasses
        ]);
    }
}