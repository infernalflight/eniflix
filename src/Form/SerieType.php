<?php

namespace App\Form;

use App\Entity\Serie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la serie',
                'required' => false,
                'row_attr' => [
                    'class' => 'input-group mb-3',
                ],
            ])
            ->add('overview', TextareaType::class)
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En cours' => 'returning',
                    'Terminé' => 'ended',
                    'Abandonné' => 'canceled',
                ],
                'placeholder' => 'Choisissez un statut',
            ])
            ->add('vote')
            ->add('popularity')
            ->add('genres')
            ->add('backdrop')
            ->add('poster_file', FileType::class, [
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'maxSizeMessage' => 'Votre fichier est trop lourd !!!',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Format non pris en charge'
                    ])
                ]
            ])
            ->add('firstAirDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('lastAirDate', null, [
                'widget' => 'single_text',
            ])
            ->add('tmdbId', null, [
                'label' => 'Identifiant tmdb',
                'required' => true,
            ])
            ->add('is_important', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr'  => [
                    'class' => 'btn btn-primary',
                    'style' => 'margin-top:25px; margin-bottom:25px',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
        ]);
    }
}
