<?php


namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class ActivateAccount
{
    private $address;
    private $mailer;

    public function __construct($address, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->address = $address;
    }

    public function sendEmail($task)
    {
        $token = $task->getIdUser()->getToken();
        $email = (new TemplatedEmail())
            ->from($this->address)
            ->to($task->getIdUser()->getEmail())
            ->subject('Activation de compte Classe Virtuelle')
            ->htmlTemplate('emails/activate.html.twig')
            ->context([
                'address' => $task->getIdUser()->getEmail(),
                'nom' => $task->getNom(),
                'prenom' => $task->getPrenom(),
                'token' => $token
            ]);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return $e;
        }
    }
}