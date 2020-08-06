<?php

namespace App\Controller;

use App\Entity\DateArchive;
use App\Form\Settings\ChangeEmailType;
use App\Form\Settings\ChangeIdType;
use App\Form\Settings\ChangePasswordType;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
        $form = $this->usersForm();

        return $this->render('settings/index.html.twig', [
            'current_menu' => 'settings',
            'formId' => $form["formId"]->createView(),
            'formMdp' => $form["formMdp"]->createView(),
            'formEmail' => $form["formEmail"]->createView(),
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
                return $this->render("settings/index.html.twig");
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
            if ($passwordEncoder->isPasswordValid($task, $plainPassword)) {
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

    /**
     * @Route ("/Purge", name="reglages.purgeTables")
     * @IsGranted("ROLE_ADMIN")
     */
    public function purgeTables()
    {
        $tables = [
            'archives',
            'classes',
            'commentaires',
            'cours',
            'date_archive',
            'eleves',
            'eleves_sousgroupes',
            'matieres',
            'messenger_messages',
            'personnels',
            'profs',
            'profs_classes',
            'profs_matieres',
            'reset_password_request',
            'sousgroupes',
            'sousgroupes_users',
            'users'
        ];
        $error = [];
        $str = "";
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $connection->prepare('SET FOREIGN_KEY_CHECKS=0')->execute();
        foreach ($tables as $table) {
            $query = 'delete from ' . $table;
            if ($table === 'users') {
                $query .= ' where id_role_id != 4';
            }
            try {
                $connection->prepare($query)->execute();
                $connection->query('ALTER TABLE ' . $table . ' AUTO_INCREMENT = 1')->execute();
            } catch (Exception $e) {
                $connection->rollback();
                $error[] = $table;
            }
        }
        if (!in_array('date_archive', $error)) {
            $monday = new DateTime();
            $monday->modify('monday this week');
            $date = new DateArchive();
            $date->setDateDerniereArchive($monday);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($date);
            $entityManager->flush();
        }
        $connection->prepare('SET FOREIGN_KEY_CHECKS=1')->execute();

        if (count($error) > 0) {
            $str = "Les tables ";
            $strTable = "";
            foreach ($error as $item) {
                $strTable .= $item . ", ";
            }
            $str = $str . substr($strTable, 0, -2) . " n'ont pas pu être vidées.";
        }

        $form = $this->usersForm();
        $this->addFlash('success', 'Base de données purgée !');

        return $this->render('settings/index.html.twig', [
            'current_menu' => 'settings',
            'formId' => $form["formId"]->createView(),
            'formMdp' => $form["formMdp"]->createView(),
            'formEmail' => $form["formEmail"]->createView(),
            'purgeError' => $str
        ]);
    }

    public function usersForm()
    {

        $formId = $this->createForm(ChangeIdType::class, [
            'action' => $this->generateUrl('reglages.changeId'),
            'method' => 'POST',
        ]);
        $formMdp = $this->createForm(ChangePasswordType::class, [
            'action' => $this->generateUrl('reglages.changePassword'),
            'method' => 'POST',
        ]);
        $formEmail = $this->createForm(ChangeEmailType::class, [
            'action' => $this->generateUrl('reglages.changeEmail'),
            'method' => 'POST',
        ]);

        return [
            "formId" => $formId,
            "formMdp" => $formMdp,
            "formEmail" => $formEmail
        ];
    }
}