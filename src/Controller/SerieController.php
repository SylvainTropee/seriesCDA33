<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/series', name: 'series_')]
class SerieController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(SerieRepository $serieRepository): Response
    {
        $series = $serieRepository->findAll();

//        $series = $serieRepository->findBy([], ["popularity" => "DESC"], 30, 20);

        //TODO renvoyer la liste de nos séries
        return $this->render('serie/list.html.twig', [
            'series' => $series
        ]);
    }

    #[Route('/{id}', name: 'detail', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function detail(int $id, SerieRepository $serieRepository): Response
    {

        $serie = $serieRepository->find($id);

        //TODO renvoyer une série spécifique
        return $this->render('serie/detail.html.twig', [
            'serie' => $serie
        ]);
    }

    #[Route('/add', name: 'add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager): Response
    {

        $serie = new Serie();
        $serie
            ->setBackdrop("backdrop.png")
            ->setDateCreated(new \DateTime())
            ->setName("The gentlemen")
            ->setGenres("Gangsters")
            ->setVote(8)
            ->setFirstAirDate(new \DateTime('-1 year'))
            ->setOverview("Un truc de gansters")
            ->setPopularity(450)
            ->setPoster('poster.png')
            ->setStatus("returning")
            ->setTmdbId(1234);

        dump($serie);

        $entityManager->persist($serie);
        $entityManager->flush();

        dump($serie);

        $serie->setName("The gentlewomen");
        $entityManager->flush();

        dump($serie);

        $entityManager->remove($serie);
        $entityManager->flush();


        //TODO renvoyer un formulaire d'ajout de série
        return $this->render('serie/add.html.twig');
    }


}
