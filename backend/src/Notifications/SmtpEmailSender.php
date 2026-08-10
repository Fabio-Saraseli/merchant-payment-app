<?php

namespace App\Notifications;

use PHPMailer\PHPMailer\PHPMailer;

class SmtpEmailSender implements EmailSenderInterface
{
    private $host;
    private $port;
    private $from;
    private $fromName;

    public function __construct(
        $host,
        $port,
        $from,
        $fromName
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->from = $from;
        $this->fromName = $fromName;
    }

    public function send($to, $subject, $message)
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = $this->host;
        $mail->Port = $this->port;
        $mail->SMTPAuth = false;

        $mail->setFrom(
            $this->from,
            $this->fromName
        );

        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
    }
}