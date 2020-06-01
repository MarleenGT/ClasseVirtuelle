<?php


namespace App\Form;

use App\Entity\Personnels;
use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AddPersonnelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('personnel', PersonnelType::class, array(
                'data_class' => Personnels::class
            ))
            ->add('users', UserType::class, array(
                'data_class' => Users::class
            ))
            ->add('ajout', SubmitType::class, ['label' => "Ajouter"]);
    }

}