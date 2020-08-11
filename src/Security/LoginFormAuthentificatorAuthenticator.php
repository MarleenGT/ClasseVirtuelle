<?php

namespace App\Security;

use App\Entity\Admins;
use App\Entity\Eleves;
use App\Entity\Personnels;
use App\Entity\Profs;
use App\Entity\Users;
use App\Service\CheckArchive;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthentificatorAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $checkArchive;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder, CheckArchive $checkArchive)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->checkArchive = $checkArchive;
    }

    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'identifiant' => $request->request->get('identifiant'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['identifiant']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(Users::class)->findOneBy(['identifiant' => $credentials['identifiant']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Les identifiants sont incorrects');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if ($user->getActif()) {
            return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
        }
        return false;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $role = $token->getRoleNames()[0];
        $id = $token->getUser()->getId();

        $this->checkArchive->check();

        $request->getSession()->set('role', $role);

        switch ($role) {
            case 'ROLE_ELEVE':
                $query = $this->entityManager->getRepository(Eleves::class)->findOneBy(['id_user' => $id]);
                $request->getSession()->set('user', $query);
                break;
            case 'ROLE_PROF':
                $query = $this->entityManager->getRepository(Profs::class)->findProfHydrated($id);
                $request->getSession()->set('user', $query);
                break;
            case 'ROLE_PERSONNEL':
                $query = $this->entityManager->getRepository(Personnels::class)->findOneBy(['id_user' => $id]);
                $request->getSession()->set('user', $query);
                break;
            case 'ROLE_ADMIN':
                $query = $this->entityManager->getRepository(Admins::class)->findOneBy(['id_user' => $id]);
                $request->getSession()->set('user', $query);
                break;
        }
        return new RedirectResponse('/ClasseVirtuelle/public/Cours');
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

}