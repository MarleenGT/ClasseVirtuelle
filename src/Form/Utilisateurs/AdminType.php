<?php

namespace App\Form\Utilisateurs;

use App\Entity\Admins;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class, [
                'mapped' => false,
                'data' => $options['id']
            ])
            ->add('nom')
            ->add('prenom')
            ->add('id_user', UserType::class, [
                'label' => false
            ])
            ->add('type', HiddenType::class, [
                'mapped' => false,
                'data' => $options['type']
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $admin = $event->getData();
                $form = $event->getForm();

                if (!$admin || null === $admin->getId()) {
                    $form->add('ajout', SubmitType::class, [
                        'label' => "Ajouter"
                    ]);
                } elseif ($form->getName() === "modif") {
                    $form->add('submit', SubmitType::class, [
                        'label' => "Modifier",
                    ])
                        ->add('close', ButtonType::class, [
                            'label' => 'Annuler',
                            'attr' => [
                                'data-dismiss' => "modal"
                            ]
                        ]);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Admins::class,
            'id' => null,
            'type' => ''
        ]);
    }
}