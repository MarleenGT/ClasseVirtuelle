<?php

namespace App\Form\Utilisateurs;

use App\Entity\Classes;
use App\Entity\Eleves;
use App\Entity\Sousgroupes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EleveType extends AbstractType
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
            ->add('id_classe', EntityType::class, [
                // looks for choices from this entity
                'class' => Classes::class,
                // uses the User.username property as the visible option string
                'choice_label' => 'nom_classe',
                'expanded'  => true,
            ])
            ->add('id_sousgroupe', EntityType::class, [
                'class' => Sousgroupes::class,
                'choice_label' => 'nom_sousgroupe',
                'expanded'  => true,
                'multiple'  => true,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $eleve = $event->getData();
                $form = $event->getForm();
                if (!$eleve || null === $eleve->getId()) {
                    $form->add('ajout', SubmitType::class, [
                        'label' => "Ajouter"
                    ]);
                } elseif($form->getName() === "modif"){
                    $form->add('submit', SubmitType::class, [
                        'label' => "Modifier"
                    ]);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Eleves::class,
            'id' => null,
            'type' => ''
        ]);
    }
}
