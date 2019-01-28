<?php

namespace VisualCraft\Bundle\MailerBundle\Tests\MailHandlerRegistry;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ServiceLocator;
use VisualCraft\Bundle\MailerBundle\MailType\MailTypeInterface;
use VisualCraft\Bundle\MailerBundle\MailTypeRegistry\LazyMailTypeRegistry;

class LazyMailTypeRegistryTest extends TestCase
{
    public function testGetMailHandlerReturnHandlerTest()
    {
        $alias = 'foo';
        $mailerHandler = $this->createMailerHandler();
        $serviceLocator = $this->createMock(ServiceLocator::class);
        $serviceLocator
            ->expects($this->once())
            ->method('get')
            ->with($alias)
            ->willReturn($mailerHandler)
        ;
        $serviceLocator
            ->expects($this->once())
            ->method('has')
            ->willReturn(true)
        ;
        $mailHandlerRegistry = new LazyMailTypeRegistry($serviceLocator);

        $this->assertSame($mailerHandler, $mailHandlerRegistry->getMailType($alias));
    }

    /**
     * @expectedException \VisualCraft\Bundle\MailerBundle\Exception\MissingMailTypeException
     */
    public function testGetMailHandlerWithRegisteredBytNotExistingService()
    {
        $container = $this->createMock(ServiceLocator::class);
        $container
            ->method('has')
            ->willReturn(false)
        ;
        $serviceAlias = 'foo';
        $mailHandlerRegistry = new LazyMailTypeRegistry($container);
        $mailHandlerRegistry->getMailType($serviceAlias);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetMailHandlerWithNotInstanceOfMailerHandlerInterface()
    {
        $alias = 'foo';
        $container = $this->createMock(ServiceLocator::class, ['get', 'has']);
        $container
            ->expects($this->once())
            ->method('get')
            ->with($alias)
            ->willReturn(new \stdClass())
        ;
        $container
            ->method('has')
            ->willReturn(true)
        ;
        $mailHandlerRegistry = new LazyMailTypeRegistry($container);
        $mailHandlerRegistry->getMailType($alias);
    }

    /**
     * @return MailTypeInterface|MockObject
     */
    protected function createMailerHandler()
    {
        return $this->createMock(MailTypeInterface::class);
    }
}
