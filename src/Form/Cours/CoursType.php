<?php


namespace App\Form\Cours;

use App\Entity\Classes;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Sousgroupes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class CoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id_prof', HiddenType::class)
            ->add('typeChoice', ChoiceType::class, [
                'choices' => [
                    'Classe' => 'classe',
                    'Sous-groupe' => 'sousgroupe'
                ],
                'label' => 'Pour : ',
                'mapped' => false,
                'expanded' => true
            ])
            ->add('id_classe'
                , ChoiceType::class, [
                    'choices' => $options['classes']
                    , 'choice_label' => function (Classes $classe) {
                        return $classe ? $classe->getNomClasse() : '';
                    }]
            )
            ->add('id_sousgroupe'
                , ChoiceType::class, [
                    'choices' => $options['sousgroupes']
                    , 'choice_label' => function (Sousgroupes $sousgroupe) {
                        return $sousgroupe ? $sousgroupe->getNomSousgroupe() : '';
                    }]
            )
            ->add('matiere'
                , ChoiceType::class, [
                    'choices' => $options['matieres']
                    , 'choice_label' => function ($matiere) {
                        return $matiere;
                    }]
            )
            ->add('autre',
                TextType::class, [
                    'mapped' => false,
                    'required' => false
                ]
            )
            ->add('date', DateType::class, [
                'mapped' => false,
                'widget' => 'single_text'
            ])
            ->add('heure_debut', TimeType::class, [
                'mapped' => false,
                'widget' => 'single_text'
            ])
            ->add('heure_fin', TimeType::class, [
                'mapped' => false,
                'widget' => 'single_text'
            ])
            ->add('lien')
            ->add('commentaire')
            ->add('submit', SubmitType::class, [
                'label' => "Ajouter"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'matieres' => null,
            'classes' => null,
            'sousgroupes' => null
        ]);
    }
}