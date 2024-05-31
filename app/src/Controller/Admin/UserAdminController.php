<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/{_locale}/users', name: 'admin_users_', requirements: ['_locale' => '%app.supported_locales%'])]
class UserAdminController extends AbstractController
{
    #[Route('', name: 'list')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('dashboard/users/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }


    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userAlreadyExists = (bool) $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

            if ($userAlreadyExists) {
                $form->get('email')->addError(new FormError('This email is already in use.'));
                $this->addFlash(
                    'danger',
                    'Something went wrong while trying to create a User.'
                );
            } else {
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'User saved!'
                );

                return $this->redirectToRoute('admin_users_list', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('dashboard/users/create.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userAlreadyExists = (bool) $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if ($userAlreadyExists) {
                $form->get('email')->addError(new FormError('This email is already in use.'));
                $this->addFlash(
                    'danger',
                    'Something went wrong while trying to create a User.'
                );
            } else {
                $entityManager->flush();

                $this->addFlash('success', 'User edited!');

                return $this->redirectToRoute('admin_users_list', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('dashboard/users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'User deleted!');
        }

        return $this->redirectToRoute('admin_users_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user, ParameterBagInterface $params): Response
    {
        return $this->render('dashboard/users/show.html.twig', [
            'user' => $user,
            'availableLanguages' => $params->get('app.available_languages'),
        ]);
    }


    #[Route('/change-locale/{id}', name: 'change-locale', methods: ['POST'])]
    public function changeLocale(User $user, Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $params): Response
    {
        $submittedToken = $request->getPayload()->get('token');

        if (!$this->isCsrfTokenValid('switch-language', $submittedToken)) {
            $this->addFlash('danger', 'Invalid CSRF Token');
            return $this->redirectToRoute('admin_users_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        $locale = $request->request->get('locale');

        $allowedLocales = '/^('. $params->get('app.supported_locales') . ')$/';

        if (0 === preg_match($allowedLocales, $locale)) {
            $this->addFlash('danger', 'Invalid locale.');
            return $this->redirectToRoute('admin_users_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Language changed!');

        return $this->redirectToRoute('admin_users_show', [
            '_locale' => $locale,
            'id' => $user->getId(),
        ], Response::HTTP_SEE_OTHER
        );

    }
}
