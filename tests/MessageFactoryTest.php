<?php

namespace VisualCraft\Bundle\MailerBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VisualCraft\Bundle\MailerBundle\MailType\MailTypeInterface;
use VisualCraft\Bundle\MailerBundle\MailTypeRegistry\MailTypeRegistryInterface;
use VisualCraft\Bundle\MailerBundle\MessageFactory\MessageFactory;

class MessageFactoryTest extends TestCase
{
    public function testMessageCreated()
    {
        $mailHandler = $this->createMock(MailTypeInterface::class);
        $mailHandler
            ->expects($this->once())
            ->method('configureOptions')
        ;
        $mailHandler
            ->expects($this->once())
            ->method('buildMessage')
        ;
        $mailHandlerRegistry = $this->createMock(MailTypeRegistryInterface::class);
        $mailHandlerRegistry
            ->method('getMailType')
            ->willReturn($mailHandler)
        ;

        $messageFactory = new MessageFactory($mailHandlerRegistry);
        $message = $messageFactory->createMessage('test');

        self::assertInstanceOf(\Swift_Message::class, $message);
    }

    /**
     * @expectedException \VisualCraft\Bundle\MailerBundle\Exception\InvalidMailTypeOptionsException
     */
    public function testThrowExceptionIfResolvingOptionFailed()
    {
        $mailHandler = $this->createMock(MailTypeInterface::class);
        $mailHandlerRegistry = $this->createMock(MailTypeRegistryInterface::class);
        $mailHandlerRegistry
            ->method('getMailType')
            ->willReturn($mailHandler)
        ;
        $optionResolver = $this->getMockBuilder(OptionsResolver::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $optionResolver->method('resolve')->willThrowException(new \LogicException('Message'));

        $messageFactory = $this->getMockBuilder(MessageFactory::class)
            ->setMethods(['createOptionsResolverInstance'])
            ->setConstructorArgs([$mailHandlerRegistry])
            ->getMock()
        ;

        $messageFactory
            ->method('createOptionsResolverInstance')
            ->willReturn($optionResolver)
        ;
        $messageFactory->createMessage('test');
    }
}
