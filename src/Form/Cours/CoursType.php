<?php


namespace App\Form\Cours;

use App\Entity\Classes;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
        $cours = $builder->getData();
        $type = null;
        $sg = null;
        $matiere = null;
        $classe = null;
        if ($cours->getId()) {
            foreach($options['matieres'] as $mat) {
                if ($cours->getMatiere() == $mat->getNomMatiere()) {
                    $matiere = $mat;
                    break;
                }
            }
            if ($cours->getIdClasse()) {
                $type = 'classe';
                $classe = $cours->getIdClasse();
                $sg = "";
            } else {
                $type = 'sousgroupe';
                $sg = $cours->getIdSousgroupe();
                $classe = "";
            }
        }
        $builder
            ->add('id', HiddenType::class, [
                'mapped' => false,
                'data' => $options['id']
            ])
            ->add('typeChoice', ChoiceType::class, [
                'choices' => [
                    'Classe' => 'classe',
                    'Sous-groupe' => 'sousgroupe'
                ],
                'label' => false,
                'mapped' => false,
                'expanded' => true,
                'data' => $type
            ])
            ->add('id_classe', ChoiceType::class, [
                'choices' => $options['classes'],
                'choice_label' => function (Classes $classe) {
                    return $classe ? $classe->getNomClasse() : '';
                },
                'label' => "Choix de la classe :",
                'data' => $classe
            ])
            ->add('id_sousgroupe'
                , ChoiceType::class, [
                    'choices' => $options['sousgroupes']
                    , 'choice_label' => function (Sousgroupes $sousgroupe) {
                        return $sousgroupe ? $sousgroupe->getNomSousgroupe() : '';
                    },
                    'label' => "Choix du sous-groupe :",
                    'data' => $sg
                ]
            )
            ->add('matiere'
                , ChoiceType::class, [
                    'choices' => $options['matieres']
                    , 'choice_label' => function ($matiere) {
                        return $matiere;
                    },
                    'label' => "Choix de la matière :",
                    'data' => $matiere
                ]
            )
            ->add('autre',
                TextType::class, [
                    'mapped' => false,
                    'required' => false,
                    'label' => "Intitulé de la matière :",
                ]
            )
            ->add('date', DateType::class, [
                'mapped' => false,
                'widget' => 'single_text',
                'label' => "Date du cours :",
                'input' => 'datetime',
                'data' => $cours->getHeureDebut()
            ])
            ->add('heure_debut', TimeType::class, [
                'mapped' => false,
                'widget' => 'single_text',
                'label' => "Heure du début du cours :",
                'input' => 'datetime',
                'data' => $cours->getHeureDebut()
            ])
            ->add('heure_fin', TimeType::class, [
                'mapped' => false,
                'widget' => 'single_text',
                'label' => "Heure de la fin du cours :",
                'input' => 'datetime',
                'data' => $cours->getHeureFin()
            ])
            ->add('lien', TextType::class, [
                'label' => "Lien du cours :",
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => "Remarques :",
                'required' => false
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $cours = $event->getData();
                $form = $event->getForm();
                if (!$cours || null === $cours->getId()) {
                    $form->add('submit', SubmitType::class, [
                        'label' => "Ajouter"
                    ]);
                } else {
                    $form->add('submit', SubmitType::class, [
                        'label' => "Modifier"
                    ]);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'matieres' => null,
            'classes' => null,
            'sousgroupes' => null,
            'id' => null
        ]);
    }
}