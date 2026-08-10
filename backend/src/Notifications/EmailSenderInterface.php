<?php

namespace App\Notifications;

interface EmailSenderInterface
{
    public function send($to, $subject, $message);
}