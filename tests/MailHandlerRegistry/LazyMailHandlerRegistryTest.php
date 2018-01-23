<?php

namespace VisualCraft\Bundle\MailerBundle\Tests\MailHandlerRegistry;

use Symfony\Component\DependencyInjection\Container;
use VisualCraft\Bundle\MailerBundle\MailHandlerInterface;
use VisualCraft\Bundle\MailerBundle\MailHandlerRegistry\LazyMailHandlerRegistry;

class LazyMailHandlerRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMailHandlerReturnHandlerTest()
    {
        $alias = 'foo';
        $serviceName = 'foo_service';
        $mailerHandler = $this->createMailerHandler();
        $container = $this->getMock(Container::class, ['get', 'has']);
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
        $mailHandlerRegistry = new LazyMailHandlerRegistry($container, [$alias => $serviceName]);

        $this->assertSame($mailerHandler, $mailHandlerRegistry->getMailHandler($alias));
    }

    /**
     * @expectedException \VisualCraft\Bundle\MailerBundle\Exception\MissingMailHandlerException
     */
    public function testGetMailHandlerWithNotRegisterMailerHandler()
    {
        $container = $this->getMock(Container::class);
        $mailHandlerRegistry = new LazyMailHandlerRegistry($container, []);
        $mailHandlerRegistry->getMailHandler('foo');
    }

    /**
     * @expectedException \VisualCraft\Bundle\MailerBundle\Exception\MissingMailHandlerException
     */
    public function testGetMailHandlerWithRegisteredBytNotExistingService()
    {
        $container = $this->getMock(Container::class);
        $container
            ->method('has')
            ->willReturn(false)
        ;
        $serviceAlias = 'foo';
        $mailHandlerRegistry = new LazyMailHandlerRegistry($container, [$serviceAlias => 'foo_service']);
        $mailHandlerRegistry->getMailHandler($serviceAlias);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetMailHandlerWithNotInstanceOfMailerHandlerInterface()
    {
        $alias = 'foo';
        $serviceName = 'foo_service';
        $container = $this->getMock(Container::class, ['get', 'has']);
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
        $mailHandlerRegistry = new LazyMailHandlerRegistry($container, [$alias => $serviceName]);
        $mailHandlerRegistry->getMailHandler($alias);
    }

    /**
     * @return MailHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMailerHandler()
    {
        return $this->getMock(MailHandlerInterface::class);
    }
}
