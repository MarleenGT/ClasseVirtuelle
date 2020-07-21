<?php

namespace App\Form\Utilisateurs;

use App\Entity\Personnels;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonnelType extends AbstractType
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
            ->add('poste')
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $personnel = $event->getData();
                $form = $event->getForm();
                if (!$personnel || null === $personnel->getId()) {
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
            'data_class' => Personnels::class,
            'id' => null,
            'type' => ''
        ]);
    }
}