<?php

namespace App\Service;

use Symfony\Component\Templating\EngineInterface;
use Twilio\Rest\Client;


class NotificationService {

    private $templating;
    private $mailer;

    /**
     * NotificationService constructor.
     * @param EngineInterface $templating
     * @param \Swift_Mailer $mailer
     */
    public function __construct(EngineInterface $templating, \Swift_Mailer $mailer)
    {
        $this->mailer =$mailer;
        $this->templating = $templating;
    }

    /**
     * Creates and sends an email
     */
    public function sendEmail(): void
    {
        $message = (new \Swift_Message(getenv('MAIL_SUBJECT')))
            ->setFrom(getenv('MAIL_FROM'))
            ->setTo(getenv('MAIL_RECEIVER'))
            ->setBody(
                $this->templating->render(
                    'emails/notification_email.html.twig'

                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }

    /**
     * Creates and sends a sms message
     */
    public function sendSms(): void
    {
        $client = new Client(getenv('ACCOUNT_ID'), getenv('AUTH_TOKEN'));
        $client->messages->create(
            getenv('TWILIO_NUMBER'),
            array(
                'from' => getenv('PHONE_NUMBER'),
                'body' => 'HI! You get that message because you site loaded at least twice as slow as at least one of the
c                          competitors.'
            )
        );
    }
}