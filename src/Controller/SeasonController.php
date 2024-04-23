<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SeasonController extends AbstractController
{
    #[Route('/season/create', name: 'app_season_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $season = new Season();

        $form = $this->createForm(SeasonType::class, $season);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($season);
            $em->flush();

            $this->addFlash('success', 'Une saison a été ajoutée');

            return $this->redirectToRoute('app_serie_details', ['id' => $season->getSerie()->getId()]);
        }

        return $this->render('season/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
