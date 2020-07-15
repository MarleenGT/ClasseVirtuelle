<?php


namespace App\Controller\MessageHandler;


use App\Controller\Message\Email;
use App\Repository\UsersRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EmailHandler implements MessageHandlerInterface
{
    private $address;
    private $mailer;

    public function __construct($address, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->address = $address;
    }
    public function __invoke(Email $email)
    {
        $user =  $email->getTask()->getIdUser();
        $token = $user->getToken();
        $email = (new TemplatedEmail())
            ->from($this->address)
            ->to($user->getEmail())
            ->subject('Activation de compte Classe Virtuelle')
            ->htmlTemplate('emails/activate.html.twig')
            ->context([
                'address' => $user->getEmail(),
                'nom' => $email->getTask()->getNom(),
                'prenom' => $email->getTask()->getPrenom(),
                'token' => $token
            ]);
            $this->mailer->send($email);
    }
}