<?php


namespace App\Controller\Cours;


use App\Entity\Cours;
use App\Entity\Profs;
use App\Form\Cours\CoursType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddCoursController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/Cours/Ajouter", name="cours.add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $cours = new Cours();
        dump($cours);
        $form = $this->createForm(CoursType::class, $cours);

        $form->handleRequest($request);
        dump($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $obj = $form->getData();
            $obj->setDate(new DateTime());
            dump($obj);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($obj);
            $entityManager->flush();

            return $this->render("cours/index.html.twig");
        }
        return $this->render("cours/add.html.twig",[
            "form" => $form->createView()
            ]);
    }
}