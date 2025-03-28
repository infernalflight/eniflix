<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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

        // Méthode héritée du Repository
        $series = $serieRepository->findBy($status !== 'all' ? $criterias : [], ['name' => 'ASC'], $nbParPage, $offset);

        // Méthode custom QueryBuilder
        //$series = $serieRepository->findBySeveralCriterias($status, $offset, $nbParPage);

        // Méthode avec DQL
        //$series = $serieRepository->getWithDql($status, $offset, $nbParPage);

        // Méthode avec Raw SQL
        //$series = $serieRepository->findWithRawSql($offset, $nbParPage);

        // Méthode héritée = Prendre toute la table
        //$series = $serieRepository->findAll();

        return $this->render('serie/list.html.twig', [
            'series' => $series,
            'page' => $page,
            'pages_total' => ceil($nbTotal / $nbParPage),
        ]);
    }

    #[Route('/detail/{id}', name: '_detail', requirements: ['id' => '\d+'])]
    public function detail(Serie $serie): Response
    {

        return $this->render('serie/detail.html.twig', [
            'serie' => $serie,
        ]);
    }


    #[Route('/create', name: '_create')]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // inutile car confié au LifeCycleCallback de l'entité
            //$serie->setDateCreated(new \DateTime());

            // Gestion upload
            $file = $form->get('poster_file')->getData();
            if ($file instanceof UploadedFile) {
                $name = $slugger->slug($serie->getName()).'-'.uniqid().'.'.$file->guessExtension();
                $file->move('uploads/posters/series', $name);
                $serie->setPoster($name);
            }

            $em->persist($serie);
            $em->flush();

            $this->addFlash('success', 'Une série a été enregistrée');

            return $this->redirectToRoute('serie_detail', ['id' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function update(Request $request, EntityManagerInterface $em, Serie $serie, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(SerieType::class, $serie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // inutile car confié au LifeCycleCallback de l'entité
            //$serie->setDateModified(new \DateTime());

            // Gestion upload
            $file = $form->get('poster_file')->getData();
            if ($file instanceof UploadedFile) {
                $name = $slugger->slug($serie->getName()).'-'.uniqid().'.'.$file->guessExtension();
                $file->move('uploads/posters/series', $name);
                if ($serie->getPoster()) {
                    unlink('uploads/posters/series/'.$serie->getPoster());
                }
                $serie->setPoster($name);
            }


            $isImportant = $form->get('is_important')->getData();

            $em->flush();

            $this->addFlash('success', 'Une série a été modifiée');

            return $this->redirectToRoute('serie_detail', ['id' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig', [
            'form' => $form,
        ]);
    }

}
