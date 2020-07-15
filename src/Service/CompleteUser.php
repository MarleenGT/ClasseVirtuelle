<?php


namespace App\Service;


use App\Controller\ErrorController;
use App\Entity\Roles;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompleteUser extends AbstractController
{

    public function completeUser($task, $type)
    {
        try {
            $identifiant = bin2hex(random_bytes(20));

        } catch (Exception $e) {
            return $this->forward(ErrorController::class, [
                'error' => $e->getMessage()
            ]);
        }
        try {
            $password = bin2hex(random_bytes(20));
        } catch (Exception $e) {
            return $this->forward(ErrorController::class, [
                'error' => $e->getMessage()
            ]);
        }
        try {
            $token = bin2hex(random_bytes(20));
        } catch (Exception $e) {
            return $this->forward(ErrorController::class, [
                'error' => $e->getMessage()
            ]);
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