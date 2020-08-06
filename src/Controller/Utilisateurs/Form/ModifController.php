<?php


namespace App\Controller\Utilisateurs\Form;


use App\Controller\Message\Email;
use App\Entity\Admins;
use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
use App\Form\Utilisateurs\AdminType;
use App\Form\Utilisateurs\EleveType;
use App\Form\Utilisateurs\PersonnelType;
use App\Form\Utilisateurs\ProfesseurType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ModifController extends AbstractController
{
    /**
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return Response
     * @Route("/Utilisateurs/Modif", name="utilisateurs.modif")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_PERSONNEL')")
     */
    public function modif(Request $request, MessageBusInterface $messageBus)
    {
        if (isset($request->request->all()['modif'])) {
            $modif = $request->request->all()['modif'];
            $user = $modif['type'];
            $id = $modif['id'];
        } else {
            $user = $request->request->get('user');
            $id = $request->request->get('id');
        }
        if ($user === 'Eleves') {
            $obj = $this->getDoctrine()->getRepository(Eleves::class)->find($id);
            $formType = EleveType::class;
        } elseif ($user === 'Professeurs') {
            $obj = $this->getDoctrine()->getRepository(Profs::class)->find($id);
            $formType = ProfesseurType::class;
        } elseif ($user === 'Personnels') {
            $obj = $this->getDoctrine()->getRepository(Personnels::class)->find($id);
            $formType = PersonnelType::class;
         }elseif ($user === 'Admins') {
            $obj = $this->getDoctrine()->getRepository(Admins::class)->find($id);
            $formType = AdminType::class;
        } else {
            return $this->json([
                'error' => 'Le type d\'utilisateur (Eleves, Professeurs ou Personnels) est incorrect'
            ]);
        }
        $form = $this->get('form.factory')->createNamed('modif',$formType, $obj, [
            'id' => $id,
            'type' => $user,
            'action' => $this->generateUrl('utilisateurs.modif')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $obj = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($obj);
            $entityManager->flush();

            if ($form->getClickedButton() && 'sendEmail' === $form->getClickedButton()->getName()){
                $email = new Email();
                $url = $this->generateUrl('account.change', ['email' => $obj->getIdUser()->getEmail(), 'token' => $obj->getIdUser()->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
                $email->setTask($obj);
                $email->setUrl($url);
                $messageBus->dispatch($email);
            }
            return $this->redirectToRoute('utilisateurs.index');
        }
        $nom = $obj->getNom();
        $prenom = $obj->getPrenom();
        $substrUser = substr($user, 0, strlen($user) - 1);

        return $this->json([
            "titre" => "Modification des informations de $nom $prenom ($substrUser)",
            "form" => $this->render("utilisateurs/modif.html.twig", [
                "form" => $form->createView()
            ])
        ]);
    }
}