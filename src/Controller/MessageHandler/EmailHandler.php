<?php


namespace App\Controller\MessageHandler;


use App\Controller\Message\Email;
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
        $email = (new TemplatedEmail())
            ->from($this->address)
            ->to($user->getEmail())
            ->subject('Activation de compte Classe Virtuelle')
            ->htmlTemplate('emails/activate.html.twig')
            ->context([
                'nom' => $email->getTask()->getNom(),
                'prenom' => $email->getTask()->getPrenom(),
                'url' => $email->getUrl()
            ]);
            $this->mailer->send($email);
    }
}