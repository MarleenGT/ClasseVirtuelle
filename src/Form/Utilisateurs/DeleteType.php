<?php


namespace App\Form\Utilisateurs;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class, [
                'mapped' => false,
                'data' => $options['id']
            ])
            ->add('type', HiddenType::class, [
                'mapped' => false,
                'data' => $options['type']
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Supprimer"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'id' => null,
            'type' => ''
        ]);

    }
}