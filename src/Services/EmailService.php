<?php


namespace App\Services;


class EmailService
{
    public function send(string $to, string $text)
    {
        echo sprintf("[Email Service] Send %s to %s\n", $text, $to);
    }
}