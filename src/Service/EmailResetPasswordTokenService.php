<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class EmailResetPasswordTokenService
{
    public function __construct(
        public readonly MailerInterface $mailer
    ) {}

    public function send(string $url, string $email)
    {
        $emailMessage = (new TemplatedEmail)
            ->from($_ENV['MAILER_SENDER'])
            ->to($email)
            ->subject('Reset your password')
            ->htmlTemplate('emails/reset-password.html.twig')
            ->textTemplate('emails/reset-password.txt.twig')
            ->context(['resetUrl' => $url]);

        $this->mailer->send($emailMessage);
    }
}
