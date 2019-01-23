<?php

namespace VisualCraft\Bundle\MailerBundle\Tests\MailHandlerRegistry;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use VisualCraft\Bundle\MailerBundle\MailType\MailTypeInterface;
use VisualCraft\Bundle\MailerBundle\MailTypeRegistry\LazyMailTypeRegistry;

class LazyMailTypeRegistryTest extends TestCase
{
    public function testGetMailHandlerReturnHandlerTest()
    {
        $alias = 'foo';
        $serviceName = 'foo_service';
        $mailerHandler = $this->createMailerHandler();
        $container = $this->createMock(Container::class, ['get', 'has']);
        $container
            ->expects($this->once())
            ->method('get')
            ->with($serviceName)
            ->willReturn($mailerHandler)
        ;
        $container
            ->method('has')
            ->willReturn(true)
        ;
        $mailHandlerRegistry = new LazyMailTypeRegistry($container, [$alias => $serviceName]);

        $this->assertSame($mailerHandler, $mailHandlerRegistry->getMailType($alias));
    }

    /**
     * @expectedException \VisualCraft\Bundle\MailerBundle\Exception\MissingMailTypeException
     */
    public function testGetMailHandlerWithNotRegisterMailerHandler()
    {
        $container = $this->createMock(Container::class);
        $mailHandlerRegistry = new LazyMailTypeRegistry($container, []);
        $mailHandlerRegistry->getMailType('foo');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetMailHandlerWithRegisteredBytNotExistingService()
    {
        $container = $this->createMock(Container::class);
        $container
            ->method('has')
            ->willReturn(false)
        ;
        $serviceAlias = 'foo';
        $mailHandlerRegistry = new LazyMailTypeRegistry($container, [$serviceAlias => 'foo_service']);
        $mailHandlerRegistry->getMailType($serviceAlias);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetMailHandlerWithNotInstanceOfMailerHandlerInterface()
    {
        $alias = 'foo';
        $serviceName = 'foo_service';
        $container = $this->createMock(Container::class, ['get', 'has']);
        $container
            ->expects($this->once())
            ->method('get')
            ->with($serviceName)
            ->willReturn(new \stdClass())
        ;
        $container
            ->method('has')
            ->willReturn(true)
        ;
        $mailHandlerRegistry = new LazyMailTypeRegistry($container, [$alias => $serviceName]);
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
