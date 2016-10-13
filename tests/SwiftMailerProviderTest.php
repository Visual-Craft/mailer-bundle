<?php

namespace VisualCraft\Bundle\MailerBundle\Tests;

use Symfony\Component\DependencyInjection\Container;
use VisualCraft\Bundle\MailerBundle\SwiftMailerProvider;

class SwiftMailerProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \VisualCraft\Bundle\MailerBundle\Exception\MissingSwiftMailerException
     */
    public function testGetMailerNotRegisteredMailer()
    {
        $container = $this->getMock(Container::class);
        $mailerProvider = new SwiftMailerProvider($container, [], 'default');
        $mailerProvider->getMailer('foo');
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetMailerRegisteredButNotExist()
    {
        $container = $this->getMock(Container::class);
        $container
            ->method('has')
            ->willReturn(false)
        ;
        $mailerProvider = new SwiftMailerProvider($container, ['foo' => 'bar'], 'default');
        $mailerProvider->getMailer('foo');
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetMailerRegisteredAndExistButNotInstanceOfSwiftMailer()
    {
        $serviceName = 'foo';
        $alias = 'bar';
        $container = $this->getMock(Container::class);
        $container
            ->method('has')
            ->willReturn(true)
        ;
        $container
            ->method('get')
            ->with($serviceName)
            ->willReturn(new \stdClass())
        ;
        $mailerProvider = new SwiftMailerProvider($container, [$alias => $serviceName], 'default');
        $mailerProvider->getMailer($alias);
    }

    public function testGetMailerReturnedValidMailer()
    {
        $serviceName = 'foo';
        $alias = 'bar';
        $mailer = $this->getMailerMock();
        $container = $this->getMock(Container::class);
        $container
            ->method('has')
            ->willReturn(true)
        ;
        $container
            ->method('get')
            ->with($serviceName)
            ->willReturn($mailer)
        ;
        $mailerProvider = new SwiftMailerProvider($container, [$alias => $serviceName], 'default');
        self::assertSame($mailer, $mailerProvider->getMailer($alias));
    }

    public function testGetMailerReturnedValidDefaultMailer()
    {
        $serviceName = 'foo';
        $alias = 'bar';
        $mailer = $this->getMailerMock();
        $container = $this->getMock(Container::class);
        $container
            ->method('has')
            ->willReturn(true)
        ;
        $container
            ->method('get')
            ->with($serviceName)
            ->willReturn($mailer)
        ;
        $mailerProvider = new SwiftMailerProvider($container, [$alias => $serviceName], $alias);
        self::assertSame($mailer, $mailerProvider->getMailer());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Swift_Mailer
     */
    protected function getMailerMock()
    {
        return  $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }


}
