<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/season', name: 'season_')]
class SeasonController extends AbstractController
{
    #[Route('/add/{serieId}', name: 'add')]
    public function add(
        Request                $request,
        EntityManagerInterface $entityManager,
        SerieRepository        $serieRepository,
        int                    $serieId = null): Response
    {

        $season = new Season();
        if ($serieId) {
            $serie = $serieRepository->find($serieId);
            $season->setNumber(count($serie->getSeasons()) + 1);
            $season->setSerie($serie);
        }
        $seasonForm = $this->createForm(SeasonType::class, $season);

        $seasonForm->handleRequest($request);

        if ($seasonForm->isSubmitted() && $seasonForm->isValid()) {
            $season->setDateCreated(new \DateTime());
            $entityManager->persist($season);
            $entityManager->flush();

            $this->addFlash('success', 'Season created !');
            return $this->redirectToRoute('series_detail', ['id' => $season->getSerie()->getId()]);
        }

        //TODO envoie formulaire pour ajout
        return $this->render('season/add.html.twig', [
            'seasonForm' => $seasonForm
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Season $season, Request $request, EntityManagerInterface $entityManager): Response
    {
        $seasonForm = $this->createForm(SeasonType::class, $season);

        $seasonForm->handleRequest($request);

        if ($seasonForm->isSubmitted() && $seasonForm->isValid()) {
            $season->setDateModified(new \DateTime());
            $entityManager->persist($season);
            $entityManager->flush();

            $this->addFlash('success', 'Season updated !');
            return $this->redirectToRoute('series_detail', ['id' => $season->getSerie()->getId()]);
        }

        return $this->render('season/edit.html.twig', [
            'seasonForm' => $seasonForm
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Season $season, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($season);
        $entityManager->flush();

        $this->addFlash("success", 'Season deleted !');

        return $this->redirectToRoute('series_detail', ['id' => $season->getSerie()->getId()]);
    }
}
