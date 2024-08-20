<?php
namespace App\Event;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

final class UserCreatedEvent extends Event
{
    public function __construct(
        private User $user,
    ) {}

    public function getUser(): User
    {
        return $this->user;
    }


}
