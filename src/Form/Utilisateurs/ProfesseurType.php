<?php

namespace App\Form\Utilisateurs;

use App\Entity\Classes;
use App\Entity\Matieres;
use App\Entity\Profs;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfesseurType extends AbstractType
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
            ->add('id_matiere', EntityType::class, [
                // looks for choices from this entity
                'class' => Matieres::class,
                // uses the User.username property as the visible option string
                'choice_label' => 'nom_matiere',
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('id_classe', EntityType::class, [
                // looks for choices from this entity
                'class' => Classes::class,
                // uses the User.username property as the visible option string
                'choice_label' => 'nom_classe',
                'expanded' => true,
                'multiple' => true,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $prof = $event->getData();
                $form = $event->getForm();

                if (!$prof || null === $prof->getId()) {
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
            'data_class' => Profs::class,
            'id' => null,
            'type' => ''
        ]);
    }
}