<?php

namespace App\Controller;

use App\Repository\SerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/serie', name: 'serie')]
final class SerieController extends AbstractController
{
    #[Route('/list/{status}/{page}', name: '_list',
        requirements: ['status' => 'returning|ended|canceled|all', 'page' => '\d+'],
        defaults: ['status' => 'all', 'page' => 1])]
    public function list(SerieRepository $serieRepository, string $status, int $page): Response
    {
        $nbParPage = 12;
        $offset = ($page - 1) * $nbParPage;
        $criterias = ['status' => $status];
        $nbTotal = $serieRepository->count($status === 'all' ? [] : $criterias);
        $series = $serieRepository->findBySeveralCriterias($status, $offset, $nbParPage);

            //$series = $serieRepository->findAll();
            //$series = $serieRepository->findBy([], ['name' => 'ASC'], $nbParPage, $offset);

            //$series = $serieRepository->findBy($criterias, ['name' => 'ASC'], $nbParPage, $offset);

        return $this->render('serie/list.html.twig', [
            'series' => $series,
            'page' => $page,
            'pages_total' => ceil($nbTotal / $nbParPage),
        ]);
    }
}
