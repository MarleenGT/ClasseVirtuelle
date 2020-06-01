<?php


namespace App\Form;

use App\Entity\Profs;
use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AddProfesseurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profs', ProfesseurType::class, [
                'data_class' => Profs::class
            ])
            ->add('users', UserType::class, [
                'data_class' => Users::class
            ])
            ->add('ajout', SubmitType::class, [
                'label' => "Ajouter"
            ]);
    }

}