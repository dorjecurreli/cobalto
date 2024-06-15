<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Artist;
use App\Entity\Artwork;
use App\Form\ArtistType;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/{_locale}/artists', name: 'admin_artists_', requirements: ['_locale' => '%app.supported_locales%'])]
class ArtistAdminController extends AbstractController
{
    public function __construct(
        private ArtistRepository $artistRepository,
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator
    )
    {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dashboard/artists/index.html.twig', [
            'artists' => $this->artistRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $artist = new Artist();
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($artist);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('artists.flash.success.add'));

            return $this->redirectToRoute('admin_artists_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/artists/create.html.twig', [
            'artist' => $artist,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Artist $artist): Response
    {
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $artworksToBeDeleted = $this->entityManager->getRepository(Artwork::class)->findBy(['artist' => null]);
            foreach ($artworksToBeDeleted as $artwork) {
                $this->entityManager->remove($artwork);
                $this->entityManager->flush();
            }

            $this->addFlash('success', $this->translator->trans('artists.flash.success.edit'));

            return $this->redirectToRoute('admin_artists_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/artists/edit.html.twig', [
            'artist' => $artist,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Artist $artist): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artist->getId(), $request->getPayload()->get('_token'))) {
            $this->entityManager->remove($artist);
            $this->entityManager->flush();
            $this->addFlash('success', $this->translator->trans('artists.flash.success.delete'));
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
