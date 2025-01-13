<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Event;
use App\Repository\ArtistRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    #[Route('/{_locale?}', name: 'app_home', requirements: ['_locale' => '%app.supported_locales%'])]
    public function index(ArtistRepository $artistRepository): Response
    {
        return $this->render('app/index.html.twig', [
            'artists' => $artistRepository->findAll(),
            'availableLanguages' => $this->params->get('app.available_languages')
        ]);
    }

    #[Route('{_locale?}/artist/{id}', name: 'app_artist', requirements: ['_locale' => '%app.supported_locales%'])]
    public function artist(Artist $artist): Response
    {
        return $this->render('app/artists/index.html.twig', [
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
}
