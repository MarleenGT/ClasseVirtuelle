<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/Importer")
 */
class ImportController extends AbstractController
{
    /**
     * @Route ("", name="import.index")
     */
    public function index()
    {
        return $this->render('import/index.html.twig', [
            'current_menu' => 'import',
        ]);
    }
}