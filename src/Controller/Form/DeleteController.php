<?php


namespace App\Controller\Form;


use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
use App\Form\EleveType;
use App\Form\PersonnelType;
use App\Form\ProfesseurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeleteController extends AbstractController
{
    public function deleteUser($user, $id)
    {
        if ($user === 'Eleves') {
            $obj = $this->getDoctrine()->getRepository(Eleves::class)->find($id);
        } elseif ($user === 'Professeurs') {
            $obj = $this->getDoctrine()->getRepository(Profs::class)->find($id);
        } elseif ($user === 'Personnels') {
            $obj = $this->getDoctrine()->getRepository(Personnels::class)->find($id);
        } else {
            return $this->render('utilisateurs/index.html.twig');
        }
        $nom = $obj->getNom();
        $prenom = $obj->getPrenom();
        $substrUser = substr($user, 0, strlen($user) - 1);

        return $this->render("utilisateurs/delete.html.twig",[
            "modal" => "Voulez-vous vraiment supprimer $nom $prenom ($substrUser) ?"
        ]);
    }
}