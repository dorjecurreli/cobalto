<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Event;
use App\Repository\ArtistRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    private ParameterBagInterface $params;
    private EntityManagerInterface $entityManager;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $entityManager)
    {
        $this->params = $params;
        $this->entityManager = $entityManager;
    }

    #[Route('/{_locale?}', name: 'app_home', requirements: ['_locale' => '%app.supported_locales%'])]
    public function index(ArtistRepository $artistRepository, EntityManagerInterface $entityManager): Response
    {
        return $this->render('app/index.html.twig', [
            'artists' => $artistRepository->findAll(),
            'eventsAvailable' => $entityManager->getRepository(Event::class)->count([]) > 0,
            'availableLanguages' => $this->params->get('app.available_languages')
        ]);
    }

    #[Route('{_locale?}/artists', name: 'app_artists', requirements: ['_locale' => '%app.supported_locales%'])]
    public function artists(): Response
    {
        return $this->render('app/artists/index.html.twig', [
            'availableLanguages' => $this->params->get('app.available_languages'),
            'artists' => $this->entityManager->getRepository(Artist::class)->findAll(),
        ]);
    }

    #[Route('{_locale?}/artist/{id}', name: 'app_artist', requirements: ['_locale' => '%app.supported_locales%'])]
    public function artist(Artist $artist): Response
    {
        return $this->render('app/artists/artist.html.twig', [
            'artist' => $artist,
            'availableLanguages' => $this->params->get('app.available_languages')
        ]);
    }

    #[Route('{_locale?}/events', name: 'app_events', requirements: ['_locale' => '%app.supported_locales%'])]
    public function events(EventRepository $eventRepository): Response
    {
        return $this->render('app/events/index.html.twig', [
            'events' => $eventRepository->createQueryBuilder('e')
                ->orderBy('e.startDate', 'ASC')
                ->getQuery()
                ->getResult(),
            'availableLanguages' => $this->params->get('app.available_languages')
        ]);
    }

    #[Route('{_locale?}/event/{id}', name: 'app_event', requirements: ['_locale' => '%app.supported_locales%'])]
    public function event(Event $event): Response
    {
        return $this->render('app/events/show.html.twig', [
            'event' => $event,
            'availableLanguages' => $this->params->get('app.available_languages')
        ]);
    }

    #[Route('{_locale?}/about', name: 'app_about', requirements: ['_locale' => '%app.supported_locales%'])]
    public function about(): Response
    {
        return $this->render('app/about.html.twig', [
            'availableLanguages' => $this->params->get('app.available_languages')
        ]);
    }

    #[Route('{_locale?}/contacts', name: 'app_contacts', requirements: ['_locale' => '%app.supported_locales%'])]
    public function contacts(): Response
    {
        return $this->render('app/contacts.html.twig', [
            'availableLanguages' => $this->params->get('app.available_languages')
        ]);
    }
}
