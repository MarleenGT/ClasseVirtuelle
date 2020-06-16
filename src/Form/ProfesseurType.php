<?php

namespace App\Form;

use App\Entity\Classes;
use App\Entity\Matieres;
use App\Entity\Profs;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('nom')
            ->add('prenom')
            ->add('id_user', UserType::class, [
                'label' => false
            ])
            ->add('id_matiere', EntityType::class, [
                // looks for choices from this entity
                'class' => Matieres::class,
                // uses the User.username property as the visible option string
                'choice_label' => 'nom_matiere',
                'expanded'  => true,
                'multiple'  => true,
            ])
            ->add('id_classe', EntityType::class, [
                // looks for choices from this entity
                'class' => Classes::class,
                // uses the User.username property as the visible option string
                'choice_label' => 'nom_classe',
                'expanded'  => true,
                'multiple'  => true,
            ])
            ->add('ajout', SubmitType::class, [
                'label' => "Ajouter"
            ])
            ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profs::class,
        ]);
    }
}