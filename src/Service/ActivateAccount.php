<?php


namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ActivateAccount
{
    private $address;
    private $passwordEncoder;
    private $mailer;

    public function __construct($address, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->address = $address;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function sendEmail($task)
    {
        $id = $task->getIdUser()->getIdentifiant();
        $email = (new TemplatedEmail())
            ->from($this->address)
            ->to($task->getIdUser()->getEmail())
            ->subject('Activation de compte Classe Virtuelle')
            ->htmlTemplate('emails/activate.html.twig')
            ->context([
                'address' => $task->getIdUser()->getEmail(),
                'nom' => $task->getNom(),
                'prenom' => $task->getPrenom(),
                'token' => $id
            ]);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return $e;
        }
        return false;
    }
}