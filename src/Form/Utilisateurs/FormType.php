<?php


namespace App\Form\Utilisateurs;


use App\Entity\Classes;
use App\Entity\Matieres;
use App\Entity\Sousgroupes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('id_user', UserType::class, [
                'label' => false
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $name = $event->getForm()->getName();
                $form = $event->getForm();
                dump($event);
                if ($name === "eleve") {
                    $form->add('id_classe', EntityType::class, [
                        // looks for choices from this entity
                        'class' => Classes::class,
                        // uses the User.username property as the visible option string
                        'choice_label' => 'nom_classe',
                        'expanded' => true,
                    ])
                        ->add('id_sousgroupe', EntityType::class, [
                            'class' => Sousgroupes::class,
                            'choice_label' => 'nom_sousgroupe',
                            'expanded' => true,
                            'multiple' => true,
                        ]);
                } else if ($name === "professeur") {
                    $form->add('id_matiere', EntityType::class, [
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
                        ]);
                } else if ($name === "personnel"){
                    $form->add('poste');
                }

            })
            ->add('ajout', SubmitType::class, [
                'label' => "Ajouter"
            ]);

    }
//
//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults([
//            'data_class' => Profs::class,
//        ]);
//    }

}