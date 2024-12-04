<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\HelperUtilService\FileUploader;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Date;


#[Route('/series', name: 'series_')]
class SerieController extends AbstractController
{



    #[Route('/{page}', name: 'list', requirements: ['page' => '\d+'], methods: ['GET'])]
    public function list(SerieRepository $serieRepository, int $page = 1, int $offset = 50): Response
    {
//        $series = $serieRepository->findAll();
//        $series = $serieRepository->findBy([], ["popularity" => "DESC"], 30, 20);
//        $series = $serieRepository->findByGenresAndPopularity('comedy');

        $maxPage = ceil($serieRepository->count([]) / $offset);
        //s'assurer que la page page minimale est 1 et que la page max c'est la page max
        //sinon on redirige vers la page 1 ou la page max
        if ($page < 1) {
            return $this->redirectToRoute('series_list', ['page' => 1]);
        }
        if ($page > $maxPage) {
            return $this->redirectToRoute('series_list', ['page' => $maxPage]);
        }

        $series = $serieRepository->findWithPagination($page, $offset);

        return $this->render('serie/list.html.twig', [
            'series' => $series,
            'currentPage' => $page,
            'maxPage' => $maxPage
        ]);
    }

    #[Route('/detail/{id}', name: 'detail', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    //paramConverter implicite
        //le paramètre est converti automatiquement en instance de série
        // à utiliser maintenant MapEntity
    public function detail(/*#[MapEntity(mapping: ['id' => 'id'])]*/ Serie $serie): Response
    {
//
//        $serie = $serieRepository->find($id);
//
//        if(!$serie){
//            throw $this->createNotFoundException("Oops ! Serie not found !");
//        }

        //TODO renvoyer une série spécifique
        return $this->render('serie/detail.html.twig', [
            'serie' => $serie
        ]);
    }

    #[Route('/add', name: 'add', methods: ['GET', 'POST'])]
    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_USER")]
    public function save(
        Request                $request,
        EntityManagerInterface $entityManager,
        SerieRepository        $serieRepository,
        FileUploader           $fileUploader,
        int                    $id = null
    ): Response
    {
        $serie = !$id ? new Serie() : $serieRepository->find($id);

        if (!$serie) {
            throw $this->createNotFoundException('No such serie !');
        }

$serieForm = $this->createForm(SerieType::class, $serie);

$serieForm->get('genres')->setData(explode(' / ', $serie->getGenres()));
$serieForm->handleRequest($request);

if ($serieForm->isSubmitted() && $serieForm->isValid()) {

    $backdrop = $serieForm->get('backdrop')->getData();

    //dd($backdrop);
    if ($backdrop) {
        $fileName = $fileUploader->upload($backdrop, $this->getParameter('backdrop_path'), $serie->getName());
        $serie->setBackdrop($fileName);
    }

    $serie->setGenres(implode(' / ', $serieForm->get('genres')->getData()));
    $entityManager->persist($serie);
    $entityManager->flush();

    $this->addFlash('success', "The Tv Show " . $serie->getName() . " has been updated");
    return $this->redirectToRoute('series_detail', ['id' => $serie->getId()]);
}

//TODO renvoyer un formulaire d'ajout de série
return $this->render('serie/save.html.twig', [
    'serieForm' => $serieForm,
    'serieId' => $id
]);
}


#[
Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function delete(Serie $serie, EntityManagerInterface $entityManager): Response
{

    $entityManager->remove($serie);
    $entityManager->flush();

    $this->addFlash('success', 'Serie has been removed !');
    return $this->redirectToRoute('series_list');
}

}
