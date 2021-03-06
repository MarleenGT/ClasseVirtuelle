<?php

namespace App\Form\Settings;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangeEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', ActualPasswordType::class, [
                'label' => false,
                'mapped' => false
            ])
            ->add('email')
            ->add('submit', SubmitType::class, [
                'label' => "Valider"
            ]);
    }
}