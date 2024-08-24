<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
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
        private EntityManagerInterface $entityManager

    ) {}


    #[Route('', name: 'list', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dashboard/events/index.html.twig', [
            'events' => $this->eventRepository->findAll(),
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

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('dashboard/events/show.html.twig', [
            'event' => $event,
        ]);
    }
}
