<?php

namespace VisualCraft\Bundle\MailerBundle\Tests\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use VisualCraft\Bundle\MailerBundle\DependencyInjection\CompilerPass\RegisterMailHandlersPass;
use VisualCraft\Bundle\MailerBundle\MailHandlerInterface;

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
        $mailHandlerRegistryDefinition = $this->getMock(Definition::class);
        $container = $this->getMock(
            ContainerBuilder::class,
            ['findTaggedServiceIds', 'getDefinition']
        );
        $container
            ->method('getDefinition')
            ->willReturnMap([
                ['visual_craft_mailer.mail_handler_registry.lazy', $mailHandlerRegistryDefinition],
                ['my_mail_handler_service1', $this->createMailHandlerDefinition()],
                ['my_mail_handler_service2', $this->createMailHandlerDefinition()],
            ])
        ;
        $container->expects(self::exactly(1))
            ->method('findTaggedServiceIds')
            ->willReturn($services)
        ;

        $mailHandlerRegistryDefinition
            ->expects(self::once())
            ->method('replaceArgument')
            ->with(1, [
                'my_alias1' => 'my_mail_handler_service1',
                'my_alias2' => 'my_mail_handler_service2',
                'my_alias3' => 'my_mail_handler_service2',
            ])
        ;
        $registerMailHandlersPass = new RegisterMailHandlersPass();
        $registerMailHandlersPass->process($container);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Definition
     */
    private function createMailHandlerDefinition()
    {
        $definition = $this->getMock(Definition::class);
        $mailHandlerClass = get_class($this->getMockForAbstractClass(MailHandlerInterface::class));
        $definition
            ->method('getClass')
            ->willReturn($mailHandlerClass)
        ;
        $definition
            ->method('isAbstract')
            ->willReturn(false)
        ;
        $definition
            ->method('isPublic')
            ->willReturn(true)
        ;

        return $definition;
    }
}
