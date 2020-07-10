<?php


namespace App\Controller;


use App\Entity\Users;
use App\Form\ActivateAccount\ChangeIdentifiantType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route ("/Activate")
 */
class ActivateAccountController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param Request $request
     * @param string|null $email
     * @param string|null $token
     * @return Response
     * @Route ("/{email}/{token}", name="account.change", requirements={"token"=".+"})
     */
    public function change(Request $request, string $email = null, string $token = null)
    {
        $session = $request->getSession();

        if ($token && $email) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $session->set('ActivateAccountToken', $token);
            $session->set('ActivateAccountEmail', $email);

            return $this->redirectToRoute('account.change');
        }

        $token = $session->get('ActivateAccountToken');
        $email = $session->get('ActivateAccountEmail');
        if (null === $token || null === $email) {
            return $this->render('errors/exception.html.twig', [
                'exception' => 'Pas d\'email ou de token trouvé pour l\'activation du compte.'
            ]);
        }
        if ($email && $token) {
            $query = $this->getDoctrine()->getRepository(Users::class)->findOneBy(['email' => $email]);
            $identifiant = $query->getIdentifiant();

            if (hash_equals($token, $identifiant)) {
                $form = $this->createForm(ChangeIdentifiantType::class, $query);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $password = $request->request->get('change_identifiant')['plainPassword'];

                    if ($password['first'] === $password['second']) {
                        $user = $form->getData();
                        $user->setMdp($this->passwordEncoder->encodePassword($user, $password['first']))
                            ->setActif(true);

                    } else {
                        return $this->render("activate_account/activate.html.twig", [
                            "form" => $form->createView(),
                            "error" => "Les 2 mots de passe ne correspondent pas"
                        ]);
                    }
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $session->clear();
                    return $this->redirectToRoute('app_logout');
                }
                return $this->render("activate_account/activate.html.twig", [
                    "form" => $form->createView()
                ]);
            }
        }
        return $this->redirectToRoute('app_login');
    }
}