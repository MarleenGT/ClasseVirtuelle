<?php


namespace App\Form;

use App\Entity\Eleves;
use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AddEleveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('eleve', EleveType::class, [
                'data_class' => Eleves::class
            ])
            ->add('users', UserType::class, [
                'data_class' => Users::class
            ])
            ->add('ajout', SubmitType::class, [
                'label' => "Ajouter"
            ]);
    }
}