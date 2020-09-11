<?php


namespace App\Controller\DownloadDoc;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DownloadDoc extends AbstractController
{

    /**
     * @return Response
     * @Route("/Documentation", name="doc.download")
     */
    public function download(): Response
    {
        if ($this->getUser()){
            $role = $this->getUser()->getRoles()[0];
        }
        switch ($role){
            case "ROLE_ELEVE":
                $path = "C:\UwAmp\www\ClasseVirtuelle\documentation\Documentation Classe Virtuelle Eleves.pdf";
                break;
            case "ROLE_PROF":
                $path = "C:\UwAmp\www\ClasseVirtuelle\documentation\Documentation Classe Virtuelle Professeurs.pdf";
                break;
            case "ROLE_PERSONNEL":
                $path = "C:\UwAmp\www\ClasseVirtuelle\documentation\Documentation Classe Virtuelle Personnels.pdf";
                break;
            case "ROLE_ADMIN":
                $path = "C:\UwAmp\www\ClasseVirtuelle\documentation\Documentation Classe Virtuelle Admins.pdf";
                break;
        }
        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

        return $response;
    }
}