<?php

namespace App\Controller;

use App\Form\Settings\ChangeEmailType;
use App\Form\Settings\ChangeIdType;
use App\Form\Settings\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route ("/Reglages")
 */
class SettingsController extends AbstractController
{
    /**
     * @Route ("", name="reglages.index")
     */
    public function index()
    {
        $user = $this->getUser();
        $formId = $this->createForm(ChangeIdType::class, $user);
        $formMdp = $this->createForm(ChangePasswordType::class);
        $formEmail = $this->createForm(ChangeEmailType::class, $user);

        return $this->render('settings/index.html.twig', [
            'current_menu' => 'settings',
            'formId' => $formId->createView(),
            'formMdp' => $formMdp->createView(),
            'formEmail' => $formEmail->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route ("/Identifiant", name="reglages.changeId", methods="POST")
     */
    public function changeId(Request $request): Response
    {
        $form = $this->createForm(ChangeIdType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();
            $this->addFlash('success', 'Identifiant modifié');
            return $this->render("settings/index.html.twig");
        }
        return $this->render('settings/index.html.twig', [
            'change' => 'Identifiant',
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @Route ("/MotDePasse", name="reglages.changePassword", methods="POST")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->all();
            $user = $this->getUser();
            $plainPassword = $task['plainPassword']->getData();
            $newPassword = $task['changedPassword']->getViewData();

            if ($passwordEncoder->isPasswordValid($user, $plainPassword) && $newPassword['first'] === $newPassword['second']) {
                $password = $passwordEncoder->encodePassword($user, $newPassword['first']);
                $user->setMdp($password);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Mot de passe modifié');
                return $this->render("index.html.twig");
            } elseif (!($passwordEncoder->isPasswordValid($user, $plainPassword))) {
                return $this->render('settings/index.html.twig', [
                    'change' => 'Mot de passe',
                    'form' => $form->createView(),
                    'error' => 'Le mot de passe actuel n\'est pas valide'
                ]);
            } else {
                return $this->render('settings/index.html.twig', [
                    'change' => 'Mot de passe',
                    'form' => $form->createView(),
                    'error' => 'Les deux nouveaux mots de passe ne correspondent pas'
                ]);
            }
        }
        return $this->render('settings/index.html.twig', [
            'change' => 'Mot de passe',
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @Route ("/Email", name="reglages.changeEmail", methods="POST")
     */
    public function changeEmail(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(ChangeEmailType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $plainPassword = $form->all()['plainPassword']->getData();
            if ($passwordEncoder->isPasswordValid($task, $plainPassword)){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($task);
                $entityManager->flush();
            }
            $this->addFlash('success', 'Email modifié');
            return $this->render("settings/index.html.twig");
        }
        return $this->render('settings/index.html.twig', [
            'change' => 'Identifiant',
            'form' => $form->createView()
        ]);
    }
}