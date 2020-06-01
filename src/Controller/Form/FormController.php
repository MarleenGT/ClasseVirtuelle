<?php


namespace App\Controller\Form;


use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
use App\Entity\Users;
use App\Form\AddEleveType;
use App\Form\AddPersonnelType;
use App\Form\AddProfesseurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/Utilisateurs/Ajout", name="utilisateurs.add", methods={"POST"})
     */
    public function add(Request $request)
    {
        dump($request);
        if ($request->request->has("typeUtil")) {
            $util = $_POST["typeUtil"];
        } elseif ($request->request->has("add_eleve")){
            $util = "Eleves";
        } elseif ($request->request->has("add_professeur")){
            $util = "Professeurs";
        } elseif ($request->request->has("add_personnel")){
            $util = "Personnels";
        }
        $class = new Users();

        if ($util === 'Eleves') {
            $class2 = new Eleves();
            $util2 = AddEleveType::class;
        } elseif ($util === 'Professeurs') {
            $class2 = new Profs();
            $util2 = AddProfesseurType::class;
        } elseif ($util === 'Personnels') {
            $class2 = new Personnels();
            $util2 = AddPersonnelType::class;
        }
        $mergedData = [
            "$util" => $class2,
            "Users" => $class
        ];
        $form = $this->createForm($util2, $mergedData);
        dump($form);
        $form->handleRequest($request);
        dump($form);
        if ($form->isSubmitted() && $form->isValid()) {

            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $task = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($task);
            // $entityManager->flush();

            return $this->redirectToRoute('task_success');
        }

        return $this->render('utilisateurs/add/add.html.twig', [
            'form' => $form->createView(),
            'typeUtil' => $util
        ]);


    }
}