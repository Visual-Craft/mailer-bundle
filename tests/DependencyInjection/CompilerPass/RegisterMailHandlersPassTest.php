<?php

namespace VisualCraft\Bundle\MailerBundle\Tests\DependencyInjection\CompilerPass;

use VisualCraft\Bundle\MailerBundle\DependencyInjection\CompilerPass\RegisterMailHandlersPass;

class RegisterMailHandlersPassTest extends \PHPUnit_Framework_TestCase
{
    public function testThatMailHandlerServicesAreProcessed()
    {
        $services = [
            'my_mail_handler_service1' => [['alias' => 'my_alias1']],
            'my_mail_handler_service2' => [
                ['alias' => 'my_alias2'],
                ['alias' => 'my_alias3'],
            ],
        ];
        $mailHandlerRegistryDefinition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $container = $this->getMock(
            'Symfony\Component\DependencyInjection\ContainerBuilder',
            ['findTaggedServiceIds', 'getDefinition']
        );
        $container->expects(self::once())
            ->method('getDefinition')
            ->with('visual_craft_mailer.mail_handler_registry.lazy')
            ->willReturn($mailHandlerRegistryDefinition)
        ;
        $container->expects(self::exactly(1))
            ->method('findTaggedServiceIds')
            ->will(self::returnValue($services))
        ;
        $mailHandlerRegistryDefinition->expects(self::exactly(3))
            ->method('addMethodCall')
            ->withConsecutive(
                [self::equalTo('registerMailHandler'), self::equalTo(['my_alias1', 'my_mail_handler_service1'])],
                [self::equalTo('registerMailHandler'), self::equalTo(['my_alias2', 'my_mail_handler_service2'])],
                [self::equalTo('registerMailHandler'), self::equalTo(['my_alias3', 'my_mail_handler_service2'])]
            )
        ;
        $registerMailHandlersPass = new RegisterMailHandlersPass();
        $registerMailHandlersPass->process($container);
    }
}
