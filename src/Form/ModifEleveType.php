<?php


namespace App\Form;


use App\Entity\Classes;
use App\Entity\Sousgroupes;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ModifEleveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('email', EntityType::class, [
                'class' => Users::class

            ])
            ->add('prenom')
            ->add('id_classe', EntityType::class, [
                'class' => Classes::class,
                'choice_label' => 'nom_classe',
                'expanded' => true,
            ])
            ->add('id_sousgroupe', EntityType::class, [
                'class' => Sousgroupes::class,
                'choice_label' => 'nom_sousgroupe',
                'expanded' => true,
                'multiple' => true,
            ]);
    }
}