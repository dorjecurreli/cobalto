<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/admin/{_locale}/users', name: 'admin_users_', requirements: ['_locale' => '%app.supported_locales%'])]
class UserAdminController extends AbstractController
{

    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $params,
        private TranslatorInterface $translator,
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private MailerInterface $mailer
    )
    {
    }

    #[Route('', name: 'list')]
    public function index(): Response
    {
        return $this->render('dashboard/users/index.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userAlreadyExists = (bool) $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

            if ($userAlreadyExists) {
                $form->get('email')->addError(new FormError('This email is already in use.'));
                $this->addFlash(
                    'danger',
                    $this->translator->trans('users.flash.danger.add')
                );

                return $this->render('dashboard/users/create.html.twig', [
                    'user' => $user,
                    'form' => $form,
                ]);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();


            $resetToken = $this->resetPasswordHelper->generateResetToken($user);

            $email = (new TemplatedEmail())
                ->from(new Address('security@cobaltopoetry.art', 'Cobalto Security Bot'))
                ->to($user->getEmail())
                ->subject('Your password reset request')
                ->htmlTemplate('reset_password/set-password-email.html.twig')
                ->context([
                    'resetToken' => $resetToken,
                ])
            ;

            $this->mailer->send($email);

            $this->addFlash(
                'success',
                $this->translator->trans('users.flash.success.add')
            );

            return $this->redirectToRoute('admin_users_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/users/create.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }



    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('users.flash.success.edit'));

            return $this->redirectToRoute('admin_users_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->get('_token'))) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('users.flash.success.delete'));
        }

        return $this->redirectToRoute('admin_users_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('dashboard/users/show.html.twig', [
            'user' => $user,
            'availableLanguages' => $this->params->get('app.available_languages'),
        ]);
    }


    #[Route('/change-locale/{id}', name: 'change-locale', methods: ['POST'])]
    public function changeLocale(User $user, Request $request): Response
    {
        $submittedToken = $request->getPayload()->get('token');

        if (!$this->isCsrfTokenValid('switch-language', $submittedToken)) {
            $this->addFlash('danger', $this->translator->trans('global.flash.danger.invalid-csrf'));
            return $this->redirectToRoute('admin_users_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        $locale = $request->request->get('locale');

        $allowedLocales = '/^('. $this->params->get('app.supported_locales') . ')$/';

        if (0 === preg_match($allowedLocales, $locale)) {
            $this->addFlash('success', $this->translator->trans('users.flash.danger.lang-changed'));
            return $this->redirectToRoute('admin_users_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        $user->setLocale($locale);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->addFlash('success', $this->translator->trans('users.flash.success.lang-changed'));

        return $this->redirectToRoute('admin_users_show', [
            '_locale' => $locale,
            'id' => $user->getId(),
        ], Response::HTTP_SEE_OTHER
        );

    }
}
