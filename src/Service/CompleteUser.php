<?php


namespace App\Service;

use App\Entity\Roles;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompleteUser extends AbstractController
{

    public function completeUser($task, $type)
    {
        try {
            $identifiant = bin2hex(random_bytes(20));
            $password = bin2hex(random_bytes(20));
            $token = bin2hex(random_bytes(20));
        } catch (Exception $e) {
            $this->addFlash('danger', "Problème lors de la complétion de l'ajout. Contactez l'administrateur.");
            return $this->redirectToRoute('utilisateurs.index');
        }
        $role = $this->getRoleFromTable($type);
        $task->getIdUser()
            ->setIdentifiant($identifiant)
            ->setMdp($password)
            ->setIdRole($role)
            ->setToken($token);

        return $task;
    }

    public function getRoleFromTable($type)
    {
        $role = $this->getDoctrine()->getRepository(Roles::class);
        if ($type === 'Professeurs') {
            return $role->find(2);
        } elseif ($type === 'Personnels') {
            return $role->find(3);
        } elseif ($type === 'Admins') {
            return $role->find(4);
        } else {
            return $role->find(1);
        }
    }
}