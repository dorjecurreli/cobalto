<?php

namespace App\EventSubscriber;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\UserCreatedEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class EmailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private MailerInterface $mailer
    ) {}
    public function onUserCreated(UserCreatedEvent $event): void
    {
        $user = $event->getUser();
        $resetToken = $this->resetPasswordHelper->generateResetToken($user);

        $email = (new TemplatedEmail())
            ->from(new Address('security@cobaltopoetry.art', 'Cobalto Security Bot'))
            ->to($user->getEmail())
            ->subject('Set your password')
            ->htmlTemplate('reset_password/set-password-email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ])
        ;

        $this->mailer->send($email);
    }
    public static function getSubscribedEvents(): array
    {
        return [
            UserCreatedEvent::class => 'onUserCreated',
        ];
    }


}
