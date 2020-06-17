<?php


namespace App\Form\Cours;


use App\Entity\Classes;
use App\Entity\Matieres;
use App\Entity\Personnels;
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

        /**
         * Ajout de l'id prof en dur pour test. A ENLEVER DES QUE LA GESTION DES UTILISATEURS CONNECTES EST FAITE
         */
        $builder
            ->add('id_prof', HiddenType::class)
            ->add('id_matiere', EntityType::class, [
                // looks for choices from this entity
                'class' => Matieres::class,
                // uses the User.username property as the visible option string
                'choice_label' => 'nom_matiere',
                'expanded' => true
            ])
            ->add('id_classe', EntityType::class, [
                // looks for choices from this entity
                'class' => Classes::class,
                // uses the User.username property as the visible option string
                'choice_label' => 'nom_classe',
                'expanded'  => true,
            ])
            ->add('heure_debut')
            ->add('heure_fin')
            ->add('id_sousgroupe', EntityType::class, [
                'class' => Sousgroupes::class,
                'choice_label' => 'nom_sousgroupe',
                'expanded'  => true,
            ])
            ->add('commentaire')
        ->add('submit', SubmitType::class, [
            'label' => "Ajouter"
        ]);
    }
}