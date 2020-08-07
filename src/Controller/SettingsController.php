<?php

namespace App\Controller;

use App\Entity\DateArchive;
use App\Entity\Users;
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
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @Route ("/Identifiant", name="reglages.changeId", methods="POST")
     */
    public function changeId(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $param = $request->request->get('change_id');
        $plainPassword = $param['plainPassword']['plainPassword'];
        $identifiant = filter_var($param['identifiant'], FILTER_SANITIZE_STRING);
        $user = $this->getDoctrine()->getRepository(Users::class)->find($this->getUser()->getId());
        if ($passwordEncoder->isPasswordValid($user, $plainPassword)) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setIdentifiant($identifiant);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Identifiant modifié');
        } else {
            $this->addFlash('danger', 'Le mot de passe est incorrect');
        }

        return $this->redirectToRoute('reglages.index');
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @Route ("/MotDePasse", name="reglages.changePassword", methods="POST")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $param = $request->request->get('change_password');
        $plainPassword = $param['plainPassword']['plainPassword'];
        $newPassword = $param['changedPassword'];
        $user = $this->getDoctrine()->getRepository(Users::class)->find($this->getUser()->getId());

        if ($passwordEncoder->isPasswordValid($user, $plainPassword) && $newPassword['first'] === $newPassword['second']) {
            $password = $passwordEncoder->encodePassword($user, $newPassword['first']);
            $user->setMdp($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Mot de passe modifié');
        } elseif (!($passwordEncoder->isPasswordValid($user, $plainPassword))) {
            $this->addFlash('danger', "Le mot de passe actuel n'est pas valide");
        } else {
            $this->addFlash('danger', "Les deux nouveaux mots de passe ne correspondent pas");
        }

        return $this->redirectToRoute('reglages.index');
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @Route ("/Email", name="reglages.changeEmail", methods="POST")
     */
    public function changeEmail(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $param = $request->request->get('change_email');
        $plainPassword = $param['plainPassword']['plainPassword'];
        $email = filter_var($param['email'], FILTER_SANITIZE_STRING);
        $user = $this->getDoctrine()->getRepository(Users::class)->find($this->getUser()->getId());
        if ($passwordEncoder->isPasswordValid($user, $plainPassword)) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setEmail($email);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Email modifié');
        } else {
            $this->addFlash('danger', 'Le mot de passe est incorrect');
        }

        return $this->redirectToRoute('reglages.index');
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
            $str = "les tables ";
            $strTable = "";
            foreach ($error as $item) {
                $strTable .= $item . ", ";
            }
            $str = $str . substr($strTable, 0, -2) . " n'ont pas pu être vidées.";
        }
        if (strlen($str) > 0) {
            $this->addFlash('danger', 'Base de données purgée mais ' . $str);
        } else {
            $this->addFlash('success', 'Base de données purgée !');
        }
        return $this->redirectToRoute('reglages.index');
    }

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

    public function usersForm()
    {
        $formId = $this->createForm(ChangeIdType::class);
        $formMdp = $this->createForm(ChangePasswordType::class);
        $formEmail = $this->createForm(ChangeEmailType::class);

        return [
            "formId" => $formId,
            "formMdp" => $formMdp,
            "formEmail" => $formEmail
        ];
    }
}