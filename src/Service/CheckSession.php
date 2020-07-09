<?php


namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckSession extends AbstractController
{
    public function getSession($request)
    {
        if ($request->hasSession()) {
            $session = $request->getSession();
        } else {
            $this->redirectToRoute('app_logout', [
                'error' => 'La session a expiré. Veuillez vous reconnecter.'
            ]);
        }
        return $session;
    }
}