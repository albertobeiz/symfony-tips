<?php


namespace App\Services;


use App\Entity\User;

class EmailService
{
    public function onUserCreated(User $user)
    {
        $this->send($user->getEmail(), 'Bienvenido a Twitfony');
    }

    public function send(string $to, string $text)
    {
        echo sprintf("[Email Service] Send %s to %s\n", $text, $to);
    }
}