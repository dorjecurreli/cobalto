<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Form\EventType;
use App\Form\FacebookEventType;
use App\Repository\EventRepository;
use App\Service\FacebookService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


#[Route('/admin/{_locale}/events', name: 'admin_events_', requirements: ['_locale' => '%app.supported_locales%'])]
class EventAdminController extends AbstractController
{
    public function __construct(
        private EventRepository $eventRepository,
        private TranslatorInterface $translator,
        private EntityManagerInterface $entityManager,
        private FacebookService $facebookService
    ) {}


    #[Route('', name: 'list', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dashboard/events/index.html.twig', [
            'events' => $this->eventRepository->findAll(),
            'allFacebookEventsPublished' => $this->facebookService->allEventsPublished()
        ]);
    }


    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($event);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('events.flash.success.add'));

            return $this->redirectToRoute('admin_events_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/events/create.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('events.flash.success.edit'));

            return $this->redirectToRoute('admin_events_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/events/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Event $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->getPayload()->get('_token'))) {
            $this->entityManager->remove($event);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('events.flash.success.delete'));
        }

        return $this->redirectToRoute('admin_events_list', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/facebook', name: 'get_from_facebook', methods: ['GET', 'POST'])]
    public function getFromFacebook(): Response
    {
        $facebookEvents = $this->facebookService->getPageEvents();
        $forms = [];

        foreach ($facebookEvents as $facebookEvent) {

            $facebookEventAlreadyExists = (bool) $this->entityManager
                ->getRepository(Event::class)
                ->findOneBy(
                    ['facebookEventId' => $facebookEvent['id']]
                );

            if ($facebookEventAlreadyExists) {
                continue;
            }

            $startDateTime = new \DateTimeImmutable($facebookEvent['start_time']);

            $event = (new Event())
                ->setName($facebookEvent['name'])
                ->setDescription($facebookEvent['description'])
                ->setStartDate($startDateTime)
                ->setStartTime($startDateTime)
                ->setFacebookEventId($facebookEvent['id'])
                ->setLocation($facebookEvent['place']['name'] ?? '');


            $form = $this->createForm(FacebookEventType::class, $event, [
                'action' => $this->generateUrl('admin_events_handle_facebook_event'),
                'method' => 'POST',
            ]);

            $forms[$event->getName()] = $form->createView();
        }

        return $this->render('dashboard/events/facebook.html.twig', [
            'forms' => $forms,
        ]);
    }

    #[Route('/facebook/publish', name: 'handle_facebook_event', methods: ['POST'])]
    public function handleFacebookEvent(Request $request): Response
    {
        $form = $this->createForm(FacebookEventType::class);
        $form->handleRequest($request);

        $event = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($event);
            $this->entityManager->flush();

            $this->addFlash('success', "Event '{$event->getName()}' has been processed.");

            return $this->redirectToRoute('admin_events_get_from_facebook');
        }

        return $this->render('dashboard/events/facebook.html.twig', [
            'forms' => [$event->getName() => $form->createView()],
        ]);
    }



    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('dashboard/events/show.html.twig', [
            'event' => $event,
        ]);
    }


}
