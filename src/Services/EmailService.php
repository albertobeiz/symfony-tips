<?php


namespace App\Services;


class EmailService
{
    public function send(string $to, string $text)
    {
        echo sprintf('send %s to %s', $text, $to);
    }
}