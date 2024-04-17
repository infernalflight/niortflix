<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SerieController extends AbstractController
{
    #[Route('/serie/create', name: 'app_serie_create')]
    public function create(EntityManagerInterface $em): Response
    {
        $serie = new Serie();
        $serie->setName('The Witcher');
        $serie->setOverview("Ciri & Geralt vivent de formidables aventures...");
        $serie->setFirstAirDate(new \DateTime('2015-04-14'));
        $serie->setStatus('Returning');
        $serie->setDateCreated(new \DateTime());

        $em->persist($serie);
        $em->flush();

        return new Response('Une serie a été crée');
    }

    #[Route('/serie/list', name: 'app_serie_list')]
    public function index(SerieRepository $serieRepository): Response
    {
        $series = $serieRepository->findAll();

        return $this->render('serie/index.html.twig', [
            'series' => $series
        ]);
    }

    #[Route('/serie/by_status/{status}', name: 'app_serie_list_by_status', requirements: ['status' => 'Returning|Canceled|Ended'])]
    public function listByStatus(SerieRepository $serieRepository, string $status): Response
    {
        $series = $serieRepository->findBy(['status' => $status], ['firstAirDate' => 'DESC']);

        return $this->render('serie/index.html.twig', [
            'series' => $series
        ]);
    }


    #[Route('/serie/details/{id}', name: 'app_serie_details', requirements: ['id' => '\d+'])]
    public function details(int $id, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->find($id);

        return $this->render('serie/details.html.twig', [
            'serie' => $serie
        ]);
    }


}
