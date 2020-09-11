<?php


namespace App\Controller\Utilisateurs\Form;

use App\Controller\Message\Email;
use App\Entity\Admins;
use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
use App\Entity\Users;
use App\Form\Utilisateurs\AdminType;
use App\Form\Utilisateurs\EleveType;
use App\Form\Utilisateurs\PersonnelType;
use App\Form\Utilisateurs\ProfesseurType;
use App\Service\CompleteUser;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AddController extends AbstractController
{
    /**
     * @param Request $request
     * @param CompleteUser $completeUser
     * @param MessageBusInterface $messageBus
     * @return Response
     * @Route("/Utilisateurs/Ajout", name="utilisateurs.add", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_PERSONNEL')")
     */
    public function add(Request $request, CompleteUser $completeUser, MessageBusInterface $messageBus)
    {
        if ($request->request->has("typeUtil")) {
            $user = $request->request->get("typeUtil");
        } elseif ($request->request->has("add")) {
            $user = $request->request->get('add')['type'];
        } else {
            $this->addFlash('danger', "Problème dans la vérification de l'utilisateur à ajouter");
            return $this->redirectToRoute('utilisateurs.index');
        }

        if ($user === 'Eleves') {
            $str = "Ajout d'élève";
            $formType = EleveType::class;
            $obj = new Eleves();
        } elseif ($user === 'Professeurs') {
            $str = "Ajout de professeur";
            $formType = ProfesseurType::class;
            $obj = new Profs();
        } elseif ($user === 'Personnels') {
            $str = "Ajout de personnel";
            $formType = PersonnelType::class;
            $obj = new Personnels();
        } elseif ($user === 'Admins') {
            $str = "Ajout d'administrateur";
            $formType = AdminType::class;
            $obj = new Admins();
        } else {
            return $this->redirectToRoute('utilisateurs.index');
        }
        $form = $this->get('form.factory')->createNamed('add', $formType, $obj, [
            'type' => $user
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $addUser = $completeUser->completeUser($task, $user);

            $query = $this->getDoctrine()->getRepository(Users::class)->findBy(['email' => $addUser->getIdUser()->getEmail()]);
            if ($query) {
                $this->addFlash('danger', "L'email renseigné est déjà utilisé.");
                return $this->render('utilisateurs/add.html.twig', [
                    'form' => $form->createView(),
                    'typeUtil' => $user,
                ]);
            }
            $email = new Email();
            $url = $this->generateUrl('account.change', ['email' => $obj->getIdUser()->getEmail(), 'token' => $obj->getIdUser()->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
            $email->setTask($addUser);
            $email->setUrl($url);
            $messageBus->dispatch($email);

            $entityManager->persist($addUser);
            try {
                $entityManager->flush();
            } catch (Exception $e) {
                $this->addFlash('danger', "Erreur lors de l'ajout de l'utilisateur.");
                return $this->render('utilisateurs/add.html.twig', [
                    'form' => $form->createView(),
                    'typeUtil' => $user,
                ]);
            }
            $this->addFlash('success', 'Utilisateur ajouté!');

            return $this->redirectToRoute('utilisateurs.index');
        }

        return $this->render('utilisateurs/add.html.twig', [
            'form' => $form->createView(),
            'typeUtil' => $user,
            'titre' => $str
        ]);
    }

}