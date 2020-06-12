<?php


namespace App\Controller\Form;


use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
use App\Entity\Roles;
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
        if ($request->request->has("typeUtil")) {
            $util = $_POST["typeUtil"];
        } elseif ($request->request->has("add_eleve")) {
            $util = "Eleves";
        } elseif ($request->request->has("add_professeur")) {
            $util = "Professeurs";
        } elseif ($request->request->has("add_personnel")) {
            $util = "Personnels";
        } else {
            return $this->render('utilisateurs/index.html.twig');
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

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $task = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $user = $this->completeUser($task, $util);
                $entityManager->persist($user);
                $entityManager->flush();

                if ($util === 'Eleves') {
                    $user2 = $this->completeEleve($task);
                } elseif ($util === 'Professeurs') {
                    $user2 = $this->completeProf($task);
                } elseif ($util === 'Personnels') {
                    $user2 = $this->completePersonnel($task);
                }

                $entityManager->persist($user2);
                $entityManager->flush();

                return $this->redirectToRoute('utilisateurs.index');
            }
            dump($form);
        return $this->render('utilisateurs/add/add.html.twig', [
            'form' => $form->createView(),
            'typeUtil' => $util
        ]);
    }

    public function completeUser($task, $type)
    {
        $user = new Users();
        $identifiant = bin2hex(random_bytes(6));
        $password = bin2hex(random_bytes(6));
        $email = $task['users']->getEmail();
        $role = $this->getRoleFromTable($type);

        $user->setIdentifiant($identifiant)->setMdp($password)->setEmail($email)->setIdRole($role);

        return $user;
    }

    public function completeEleve($task)
    {
        $nom = $task['eleve']->getNom();
        $prenom = $task['eleve']->getPrenom();
        $idClasse = $task['eleve']->getIdClasse();
        $email = $task['users']->getEmail();
        $idUser = $this->getUserIdFromTable($email);

        $eleve = new Eleves();

        $eleve->setNom($nom)->setPrenom($prenom)->setIdClasse($idClasse)->setIdUser($idUser);

        return $eleve;
    }

    public function completePersonnel($task)
    {
        $nom = $task['personnel']->getNom();
        $prenom = $task['personnel']->getPrenom();
        $poste = $task['personnel']->getPoste();
        $email = $task['users']->getEmail();
        $idUser = $this->getUserIdFromTable($email);

        $personnel = new Personnels();

        $personnel->setNom($nom)->setPrenom($prenom)->setIdUser($idUser)->setPoste($poste);

        return $personnel;
    }

    public function completeProf($task)
    {
        $nom = $task['profs']->getNom();
        $prenom = $task['profs']->getPrenom();
        $idClasse = $task['profs']->getIdClasse();
        $idMatiere = $task['profs']->getIdMatiere();
        $email = $task['users']->getEmail();
        $idUser = $this->getUserIdFromTable($email);

        $prof = new Profs();

        $prof->setNom($nom)->setPrenom($prenom)->setIdUser($idUser);

        foreach ($idClasse as $id => $classe){
            $prof->addIdClasse($classe);
        }
        foreach ($idMatiere as $id => $matiere){
            $prof->addIdMatiere($matiere);
        }

        return $prof;
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

    public function getUserIdFromTable($email)
    {
        $user = $this->getDoctrine()->getRepository(Users::class);
        return $user->findOneBy([
           'email' => $email
        ]);
    }
}