<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Artist;
use App\Form\ArtistType;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/artists', name: 'admin_artists_')]
class ArtistAdminController extends AbstractController
{

    #[Route('', name: 'list', methods: ['GET'])]
    public function index(ArtistRepository $artistRepository): Response
    {
        return $this->render('dashboard/artists/index.html.twig', [
            'artists' => $artistRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $artist = new Artist();
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($artist);
            $entityManager->flush();

            return $this->redirectToRoute('admin_artists_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/artists/create.html.twig', [
            'artist' => $artist,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_artists_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/artists/edit.html.twig', [
            'artist' => $artist,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artist->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($artist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_artists_list', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Artist $artist): Response
    {
        return $this->render('dashboard/artists/show.html.twig', [
            'artist' => $artist,
        ]);
    }

}
