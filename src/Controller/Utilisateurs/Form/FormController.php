<?php


namespace App\Controller\Utilisateurs\Form;


use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
use App\Entity\Roles;
use App\Form\Utilisateurs\EleveType;
use App\Form\Utilisateurs\PersonnelType;
use App\Form\Utilisateurs\ProfesseurType;
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
        if ($request->request->has("typeUtil")) {
            $util = $_POST["typeUtil"];
        } elseif ($request->request->has("eleve") || $request->request->has("professeur") || $request->request->has("personnel")) {
            $util = ucwords(array_key_first($request->request->all()).'s');
        } else {
            return $this->render('utilisateurs/index.html.twig');
        }

            if ($util === 'Eleves') {
                $util2 = EleveType::class;
                $class = new Eleves();
            } elseif ($util === 'Professeurs') {
                $util2 = ProfesseurType::class;
                $class = new Profs();
            } elseif ($util === 'Personnels') {
                $util2 = PersonnelType::class;
                $class = new Personnels();
            }
            $form = $this->createForm($util2, $class);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $task = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $user = $this->completeUser($task, $util);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Utilisateur ajouté!');
                return $this->redirectToRoute('utilisateurs.index');
            }
        return $this->render('utilisateurs/add/add.html.twig', [
            'form' => $form->createView(),
            'typeUtil' => $util
        ]);
    }

    public function completeUser($task, $type)
    {
        $identifiant = bin2hex(random_bytes(6));
        $password = bin2hex(random_bytes(6));
        $role = $this->getRoleFromTable($type);

        $task->getIdUser()->setIdentifiant($identifiant)->setMdp($password)->setIdRole($role);

        return $task;
    }

    public function getRoleFromTable($type)
    {
        $role = $this->getDoctrine()->getRepository(Roles::class);
        if ($type === 'Professeurs') {
            return $role->find(2);
        } elseif ($type === 'Personnels') {
            return $role->find(3);
        } else {
            return $role->find(1);
        }
    }
}