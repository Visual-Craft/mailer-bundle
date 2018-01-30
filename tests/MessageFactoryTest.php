<?php

namespace VisualCraft\Bundle\MailerBundle\Tests;

use Symfony\Component\OptionsResolver\OptionsResolver;
use VisualCraft\Bundle\MailerBundle\MailHandler\MailHandlerInterface;
use VisualCraft\Bundle\MailerBundle\MailHandlerRegistry\MailHandlerRegistryInterface;
use VisualCraft\Bundle\MailerBundle\MessageFactory\MessageFactory;

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

    /**
     * @expectedException \VisualCraft\Bundle\MailerBundle\Exception\InvalidMailHandlerOptionsException
     */
    public function testThrowExceptionIfResolvingOptionFailed()
    {
        $mailHandler = $this->getMock(MailHandlerInterface::class);
        $mailHandlerRegistry = $this->getMock(MailHandlerRegistryInterface::class);
        $mailHandlerRegistry
            ->method('getMailHandler')
            ->willReturn($mailHandler)
        ;
        $optionResolver = $this->getMockBuilder(OptionsResolver::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $optionResolver->method('resolve')->willThrowException(new \LogicException('Message'));
        $messageFactory = $this->getMock(MessageFactory::class, ['createOptionsResolverInstance'], [$mailHandlerRegistry]);
        $messageFactory
            ->method('createOptionsResolverInstance')
            ->willReturn($optionResolver)
        ;
        $messageFactory->createMessage('test');
    }
}
