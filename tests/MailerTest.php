<?php

namespace VisualCraft\Bundle\MailerBundle\Tests;

use VisualCraft\Bundle\MailerBundle\Mailer;
use VisualCraft\Bundle\MailerBundle\MailHandlerInterface;
use VisualCraft\Bundle\MailerBundle\MailHandlerRegistryInterface;
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
        $mailHandler = $this->getMock(MailHandlerInterface::class);
        $mailHandler
            ->expects(self::once())
            ->method('configureOptions')
            ->willReturn([])
        ;
        $mailHandler
            ->expects(self::once())
            ->method('buildMessage')
            ->will(self::returnArgument(0))
        ;
        $mailHandlerRegistry = $this->getMock(MailHandlerRegistryInterface::class);
        $mailHandlerRegistry
            ->method('getMailHandler')
            ->willReturn($mailHandler)
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

        $mailer = new Mailer($mailerProvider, $mailHandlerRegistry);
        self::assertEquals(new SendStatus(1, []), $mailer->send('foo', []));
    }
}
