<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/serie', name: 'serie')]
final class SerieController extends AbstractController
{

    #[Route('/list/{page}', name: '_list', requirements: ['page' => '\d+'], defaults: ['page' => 1], methods: ['GET'])]
    public function list(SerieRepository $serieRepository, int $page, ParameterBagInterface $parameters): Response
    {
        //$series = $serieRepository->findAll();

        $nbPerPage = $parameters->get('serie')['nb_max'];
        $offset = ($page - 1) * $nbPerPage;
        $criterias = [
//            'status' => 'Returning',
//            'genre' => 'Drama',
        ];

        $series = $serieRepository->findBy(
            $criterias,
            ['popularity' => 'DESC'],
            $nbPerPage,
            $offset
        );

        $total = $serieRepository->count($criterias);
        $totalPages = ceil($total / $nbPerPage);

        return $this->render('serie/list.html.twig', [
                'series' => $series,
                'page' => $page,
                'total_pages' => $totalPages,
            ]
        );
    }

    #[Route('/liste-custom', name: '_custom_list')]
    public function listCustom(SerieRepository $serieRepository): Response
    {
        //$series = $serieRepository->findSeriesCustom(400, 8);
        $series = $serieRepository->findSeriesWithDQL(400, 8);

        // Le requêtage SQL raw nécessite qu'on adapte le template (firstAirDate -> first_air_date)
        //$series = $serieRepository->findSeriesWithSQL(400, 8);

        return $this->render('serie/list.html.twig', [
            'series' => $series,
            'page' => 1,
            'total_pages' => 10,
        ]);
    }



    #[Route('/detail/{id}', name: '_detail', requirements: ['id' => '\d+'])]
    public function detail(Serie $serie): Response
    {
        return $this->render('serie/detail.html.twig', [
            'serie' => $serie
        ]);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($serie);
            $em->flush();

            $this->addFlash('success', 'Une série a été enregistrée');

            return $this->redirectToRoute('serie_detail', ['id' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig', [
            'serie_form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function update(Serie $serie, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SerieType::class, $serie);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->flush();

            $this->addFlash('success', 'Une série a été mise à jour');

            return $this->redirectToRoute('serie_detail', ['id' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig', [
            'serie_form' => $form,
        ]);
    }

}
