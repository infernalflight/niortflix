<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/serie', name: 'app_serie')]
class SerieController extends AbstractController
{
    #[Route('/create', name: '_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($serie);
            $em->flush();

            $this->addFlash('success', 'Une série a été crée.');

            return $this->redirectToRoute('app_serie_details', ['id' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig', [
            'serie_form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function update(Request $request, EntityManagerInterface $em, SerieRepository $serieRepository, int $id): Response
    {
        $serie = $serieRepository->find($id);

        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($serie);
            $em->flush();

            $this->addFlash('success', 'Une série a été modifiée.');

            return $this->redirectToRoute('app_serie_details', ['id' => $id]);
        }

        return $this->render('serie/edit.html.twig', [
            'serie_form' => $form,
        ]);
    }

    #[Route('/list/{page}', name: '_list', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function index(SerieRepository $serieRepository, int $page): Response
    {
        if ($page < 1) {
            throw new NotFoundHttpException('Page non valable');
        }

        // Grosse maille : la méthode héritée
        //$series = $serieRepository->findAll();

        $offset = ($page - 1) * 18;

        $series = $serieRepository->findBy([], ['popularity' => 'DESC'], 18, $offset);
        $nbSeries = $serieRepository->count();

        $maxPages = ceil($nbSeries / 18);

        // Plus fin : le queryBuilder
        //$series = $serieRepository->findBySeveralCriterias('SF', 'Comedy', (new \DateTime('-3 year'))->format('Y-m-d'));

        // Encore plus fin : avec DQL
        //$series = $serieRepository->findWithDql();

        // Le Fin du Fin : avec SQL Brut
        //$series = $serieRepository->findWithSql();


        return $this->render('serie/index.html.twig', [
            'series' => $series,
            'page' => $page,
            'max_pages' => $maxPages,
        ]);
    }

    #[Route('/by_status/{status}/{page}', name: '_list_by_status', requirements: ['status' => 'Returning|Canceled|Ended', 'page' => '\d+'], defaults: ['page' => 1])]
    public function listByStatus(SerieRepository $serieRepository, string $status, int $page): Response
    {
        $offset = ($page - 1) * 18;

        $series = $serieRepository->findBy(['status' => $status], ['firstAirDate' => 'DESC'], 18, $offset);
        $nbSeries = $serieRepository->count(['status' => $status]);

        $maxPages = ceil($nbSeries / 18);

        return $this->render('serie/index.html.twig', [
            'series' => $series,
            'page' => $page,
            'max_pages' => $maxPages,
        ]);
    }

    #[Route('/details/{id}', name: '_details', requirements: ['id' => '\d+'])]
    public function details(int $id, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->find($id);

        return $this->render('serie/details.html.twig', [
            'serie' => $serie
        ]);
    }

    #[Route('/delete/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    public function delete(int $id, Request $request, SerieRepository $serieRepository, EntityManagerInterface $em): Response
    {
        $serie = $serieRepository->find($id);

        if ($this->isCsrfTokenValid('delete'.$id, $request->get('token'))) {
            $em->remove($serie);
            $em->flush();

            $this->addFlash('success', 'Une série a été supprimée');
        } else {
            $this->addFlash('danger', "Tu veux me gruger ??? Le jeton n'est pas valable");
        }


        return $this->redirectToRoute('app_serie_list');
    }

}
