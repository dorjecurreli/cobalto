<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Repository\ArtistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArtistRepository $artistRepository): Response
    {
        return $this->render('app/index.html.twig', [
            'artists' => $artistRepository->findAll(),
        ]);
    }


    #[Route('/artist/{id}', name: 'app_artist')]
    public function artist(Artist $artist): Response
    {
        return $this->render('app/artists/index.html.twig', [
            'artist' => $artist,
        ]);
    }
}
