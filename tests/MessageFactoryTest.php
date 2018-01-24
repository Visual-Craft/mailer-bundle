<?php

namespace VisualCraft\Bundle\MailerBundle\Tests;

use VisualCraft\Bundle\MailerBundle\MailHandlerInterface;
use VisualCraft\Bundle\MailerBundle\MailHandlerRegistryInterface;
use VisualCraft\Bundle\MailerBundle\MessageFactory;

class MessageFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testMessageCreated()
    {
        $mailHandler = $this->getMock(MailHandlerInterface::class);
        $mailHandler
            ->expects($this->once())
            ->method('configureOptions')
        ;
        $mailHandler
            ->expects($this->once())
            ->method('buildMessage')
        ;
        $mailHandlerRegistry = $this->getMock(MailHandlerRegistryInterface::class);
        $mailHandlerRegistry
            ->method('getMailHandler')
            ->willReturn($mailHandler)
        ;

        $messageFactory = new MessageFactory($mailHandlerRegistry);
        $message = $messageFactory->createMessage('test');

        self::assertInstanceOf(\Swift_Message::class, $message);
    }
}
