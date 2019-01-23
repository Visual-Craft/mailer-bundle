<?php

namespace VisualCraft\Bundle\MailerBundle\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VisualCraft\Bundle\MailerBundle\Mailer;
use VisualCraft\Bundle\MailerBundle\MessageFactory\MessageFactoryInterface;
use VisualCraft\Bundle\MailerBundle\SendStatus;
use VisualCraft\Bundle\MailerBundle\SwiftMailerProvider\SwiftMailerProviderInterface;

class MailerTest extends TestCase
{
    public function testThatMailAreProcessed()
    {
        $mailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $mailer
            ->method('send')
            ->willReturn(1)
        ;

        /** @var MockObject|SwiftMailerProviderInterface $mailerProvider */
        $mailerProvider = $this->getMockBuilder(SwiftMailerProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mailerProvider
            ->method('getMailer')
            ->willReturn($mailer)
        ;

        $messageFactory = $this->createMock(MessageFactoryInterface::class);
        $messageFactory
            ->expects($this->once())
            ->method('createMessage')
            ->willReturn(new \Swift_Message())
        ;

        $mailer = new Mailer($mailerProvider, $messageFactory);
        self::assertEquals(new SendStatus(1, []), $mailer->send('foo'));
    }
}
