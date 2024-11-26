<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/series', name: 'series_')]
class SerieController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        //TODO renvoyer la liste de nos séries
        return $this->render('serie/list.html.twig');
    }

    #[Route('/{id}', name: 'detail', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function detail(int $id): Response
    {

        //TODO renvoyer une série spcécifique
        return $this->render('serie/detail.html.twig');
    }

    #[Route('/add', name: 'add', methods: ['GET', 'POST'])]
    public function add(): Response
    {
        //TODO renvoyer un formulaire d'ajout de série
        return $this->render('serie/add.html.twig');
    }



}
