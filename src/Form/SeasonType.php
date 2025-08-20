<?php

namespace App\Form;

use App\Entity\Season;
use App\Entity\Serie;
use App\Repository\SerieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number')
            ->add('firstAirDate')
            ->add('overview')
            ->add('tmdbId')
            ->add('poster')
            ->add('serie', EntityType::class, [
                'placeholder' => '-- Choose a Serie --',
                'class' => Serie::class,
                'choice_label' => function (Serie $serie) {
                    return sprintf('%s (%s)', $serie->getName(), count($serie->getSeasons()));
                },
                'query_builder' => function (SerieRepository $repo) {
                    return $repo->createQueryBuilder('s')
                        ->addSelect('seasons')
                        ->leftJoin('s.seasons', 'seasons')
                        ->orderBy('s.name', 'ASC');
                }
            ])
            ->add('submit', SubmitType::class, [])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
        ]);
    }
}
