<?php

namespace App\Controller;

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
}
