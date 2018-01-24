<?php

namespace VisualCraft\Bundle\MailerBundle\Tests;

use VisualCraft\Bundle\MailerBundle\Mailer;
use VisualCraft\Bundle\MailerBundle\MessageFactoryInterface;
use VisualCraft\Bundle\MailerBundle\SendStatus;
use VisualCraft\Bundle\MailerBundle\SwiftMailerProvider;

class MailerTest extends \PHPUnit_Framework_TestCase
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

        /** @var \PHPUnit_Framework_MockObject_MockObject|SwiftMailerProvider $mailerProvider */
        $mailerProvider = $this->getMockBuilder(SwiftMailerProvider::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mailerProvider
            ->method('getMailer')
            ->willReturn($mailer)
        ;

        $messageFactory = $this->getMock(MessageFactoryInterface::class);
        $messageFactory
            ->expects($this->once())
            ->method('createMessage')
            ->willReturn(\Swift_Message::newInstance())
        ;

        $mailer = new Mailer($mailerProvider, $messageFactory);
        self::assertEquals(new SendStatus(1, []), $mailer->send('foo'));
    }
}
