<?php

namespace App\Form;

use App\Entity\Classes;
use App\Entity\Matieres;
use App\Entity\Profs;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddProfesseur extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email', CollectionType::class, [
                'entry_type' => Users::class
            ])
            ->add('id_matiere', EntityType::class, [
                // looks for choices from this entity
                'class' => Matieres::class,
                // uses the User.username property as the visible option string
                'choice_label' => 'nom_matiere',
            ])
            ->add('id_classe', EntityType::class, [
                // looks for choices from this entity
                'class' => Classes::class,
                // uses the User.username property as the visible option string
                'choice_label' => 'nom_classe',
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