<?php

namespace App\Form\Settings;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder
                ->add('plainPassword', ActualPasswordType::class, [
                    'label' => false,
                    'mapped' => false
                ])
                ->add('changedPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Entrez le nouveau mot de passe',
                            ]),
                            new Length([
                                'min' => 6,
                                'minMessage' => 'La taille minimum est de {{ limit }} caractères',
                                // max length allowed by Symfony for security reasons
                                'max' => 4096,
                            ]),
                        ],
                        'label' => 'Nouveau mot de passe',
                    ],
                    'second_options' => [
                        'label' => 'Entrez le mot de passe de nouveau',
                    ],
                    'invalid_message' => 'Les deux champs doivent correspondre.',
                    // Instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                ])
            ->add('submit', SubmitType::class, [
                'label' => "Valider"
            ]);
    }
}