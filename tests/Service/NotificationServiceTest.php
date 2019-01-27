<?php

namespace App\Tests\Service;

use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Templating\EngineInterface;

class NotificationServiceTest extends WebTestCase
{

    private $notificationService;
    private $mailer;

    public function testSendEmail()
    {
        $this->mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function (\Swift_Message $actual_message) {
                $this->assertEquals(getenv('MAIL_SUBJECT'), $actual_message->getSubject());
                $this->assertEquals([getenv('MAIL_RECEIVER')=> null], $actual_message->getTo());
                $this->assertEquals([getenv('MAIL_FROM') => null],$actual_message->getFrom());
                return true;
            }));

        $this->notificationService->sendEmail();
    }

    protected function setUp()
    {
        parent::setUp();
        $this->mailer = $this->createMock(\Swift_Mailer::class);
        $templating = $this->createMock(EngineInterface::class);
        $this->notificationService = new NotificationService($templating, $this->mailer);
    }
}