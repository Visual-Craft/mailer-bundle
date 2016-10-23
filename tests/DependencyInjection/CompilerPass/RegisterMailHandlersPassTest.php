<?php

namespace VisualCraft\Bundle\MailerBundle\Tests\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testExceptionIfMailHandlerIsAbstract()
    {
        $mailHandlerRegistryDefinition = $this->getMock(Definition::class);
        $container = $this->getMock(
            ContainerBuilder::class,
            ['findTaggedServiceIds', 'getDefinition']
        );
        $container
            ->method('findTaggedServiceIds')
            ->willReturn(['my_mail_handler_service1' => [['alias' => 'my_alias1']]])
        ;
        $container
            ->method('getDefinition')
            ->willReturnMap([
                ['visual_craft_mailer.mail_handler_registry.lazy', $mailHandlerRegistryDefinition],
                ['my_mail_handler_service1', $this->createMailHandlerDefinition(['isAbstract' => true])],
            ])
        ;
        $registerMailHandlersPass = new RegisterMailHandlersPass();
        $registerMailHandlersPass->process($container);
    }

    /**
     * @param array $customOptions
     * @return \PHPUnit_Framework_MockObject_MockObject|Definition
     */
    private function createMailHandlerDefinition(array $customOptions = [])
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefaults([
            'isAbstract' => false,
            'isPublic' => true,
            'class' => get_class($this->getMockForAbstractClass(MailHandlerInterface::class)),
        ]);
        $options = $optionsResolver->resolve($customOptions);

        $definition = $this->getMock(Definition::class);
        $definition
            ->method('getClass')
            ->willReturn($options['class'])
        ;
        $definition
            ->method('isAbstract')
            ->willReturn($options['isAbstract'])
        ;
        $definition
            ->method('isPublic')
            ->willReturn($options['isPublic'])
        ;

        return $definition;
    }
}
