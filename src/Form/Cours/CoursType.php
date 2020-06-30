<?php


namespace App\Form\Cours;

use App\Entity\Classes;
use App\Entity\Matieres;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Sousgroupes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('id_matiere'
                , ChoiceType::class, [
                    'choices' => $options['matieres']
                    , 'choice_label' => function (Matieres $matiere) {
                        return $matiere ? $matiere->getNomMatiere() : '';
                    }]
            )
            ->add('id_classe'
                , ChoiceType::class, [
                    'choices' => $options['classes']
                    , 'choice_label' => function (Classes $classe) {
                        return $classe ? $classe->getNomClasse() : '';
                    }]
            )
            ->add('heure_debut', DateTimeType::class, [
                'date_widget' => 'single_text'
            ])
            ->add('heure_fin', DateTimeType::class, [
                'date_widget' => 'single_text'
            ])
            ->add('id_sousgroupe', EntityType::class, [
                'class' => Sousgroupes::class,
                'choice_label' => 'nom_sousgroupe',
                'expanded' => true,
            ])
            ->add('commentaire')
            ->add('submit', SubmitType::class, [
                'label' => "Ajouter"
            ]);
        dump($builder->get('id_matiere'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'matieres' => null,
            'classes' => null
        ]);
    }
}