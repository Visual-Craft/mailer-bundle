<?php

namespace VisualCraft\Bundle\MailerBundle\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use VisualCraft\Bundle\MailerBundle\SwiftMailerProvider\LazySwiftMailerProvider;

class LazySwiftMailerProviderTest extends TestCase
{
    /**
     * @expectedException \VisualCraft\Bundle\MailerBundle\Exception\MissingSwiftMailerException
     */
    public function testGetMailerWithNotRegisteredMailer()
    {
        $container = $this->createMock(Container::class);
        $mailerProvider = new LazySwiftMailerProvider($container, [], 'default');
        $mailerProvider->getMailer('foo');
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetMailerWithRegisteredButNotExist()
    {
        $container = $this->createMock(Container::class);
        $container
            ->method('has')
            ->willReturn(false)
        ;
        $mailerProvider = new LazySwiftMailerProvider($container, ['foo' => 'bar'], 'default');
        $mailerProvider->getMailer('foo');
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetMailerWithRegisteredAndExistButNotInstanceOfSwiftMailer()
    {
        $serviceName = 'foo';
        $alias = 'bar';
        $container = $this->createMock(Container::class);
        $container
            ->method('has')
            ->willReturn(true)
        ;
        $container
            ->method('get')
            ->with($serviceName)
            ->willReturn(new \stdClass())
        ;
        $mailerProvider = new LazySwiftMailerProvider($container, [$alias => $serviceName], 'default');
        $mailerProvider->getMailer($alias);
    }

    public function testGetMailerReturnedValidMailer()
    {
        $serviceName = 'foo';
        $alias = 'bar';
        $mailer = $this->getMailerMock();
        $container = $this->createMock(Container::class);
        $container
            ->method('has')
            ->willReturn(true)
        ;
        $container
            ->method('get')
            ->with($serviceName)
            ->willReturn($mailer)
        ;
        $mailerProvider = new LazySwiftMailerProvider($container, [$alias => $serviceName], 'default');
        $this->assertSame($mailer, $mailerProvider->getMailer($alias));
    }

    public function testGetMailerReturnedValidDefaultMailer()
    {
        $serviceName = 'foo';
        $alias = 'bar';
        $mailer = $this->getMailerMock();
        $container = $this->createMock(Container::class);
        $container
            ->method('has')
            ->willReturn(true)
        ;
        $container
            ->method('get')
            ->with($serviceName)
            ->willReturn($mailer)
        ;
        $mailerProvider = new LazySwiftMailerProvider($container, [$alias => $serviceName], $alias);
        $this->assertSame($mailer, $mailerProvider->getMailer());
    }

    /**
     * @return MockObject|\Swift_Mailer
     */
    protected function getMailerMock()
    {
        return  $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
