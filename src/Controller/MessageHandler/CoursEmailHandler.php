<?php


namespace App\Controller\MessageHandler;


use App\Controller\Message\CoursEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CoursEmailHandler implements MessageHandlerInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    public function __invoke(CoursEmail $email)
    {
        $email = (new TemplatedEmail())
            ->from($email->getAddress())
            ->to($email->getEmail())
            ->subject('Ajout de cours')
            ->htmlTemplate('emails/addCours.html.twig')
            ->context([
                'nom' => $email->getTask()->getNom(),
                'prenom' => $email->getTask()->getPrenom(),
                'cours' => $email->getCours()
            ]);
        $this->mailer->send($email);
    }
}