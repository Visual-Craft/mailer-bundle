<?php

namespace VisualCraft\Bundle\MailerBundle\Tests\DependencyInjection\CompilerPass;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VisualCraft\Bundle\MailerBundle\DependencyInjection\CompilerPass\RegisterMailTypesPass;
use VisualCraft\Bundle\MailerBundle\MailType\MailTypeInterface;
use VisualCraft\Bundle\MailerBundle\MailTypeRegistry\LazyMailTypeRegistry;

class RegisterMailTypesPassTest extends TestCase
{
    public function testThatMailHandlerServicesAreProcessed()
    {
        $services = [
            'my_mail_type_service1' => [
                ['type' => 'my_type1'],
            ],
            'my_mail_type_service2' => [
                ['type' => 'my_type2'],
                ['type' => 'my_type3'],
            ],
            'my_mail_type_service3' => [
                ['type' => 'my_type4'],
                [],
            ],
            'my_mail_type_service4' => [
                [],
            ],
        ];
        /** @var Definition[] $mailTypesDefinitions */
        $mailTypesDefinitions = [
            'my_mail_type_service1' => $this->createMailTypeDefinition(),
            'my_mail_type_service2' => $this->createMailTypeDefinition(),
            'my_mail_type_service3' => $this->createMailTypeDefinition(),
            'my_mail_type_service4' => $this->createMailTypeDefinition(),
        ];
        $mailTypeRegistryDefinition = $this->createMock(Definition::class);
        $container = $this->getMockBuilder(ContainerBuilder::class)
            ->setMethods(['findTaggedServiceIds', 'getDefinition', 'setDefinition'])
            ->getMock()
        ;
        $container
            ->method('getDefinition')
            ->willReturnMap([
                [LazyMailTypeRegistry::class, $mailTypeRegistryDefinition],
                ['my_mail_type_service1', $mailTypesDefinitions['my_mail_type_service1']],
                ['my_mail_type_service2', $mailTypesDefinitions['my_mail_type_service2']],
                ['my_mail_type_service3', $mailTypesDefinitions['my_mail_type_service3']],
                ['my_mail_type_service4', $mailTypesDefinitions['my_mail_type_service4']],
            ])
        ;
        $container->expects($this->once())
            ->method('findTaggedServiceIds')
            ->willReturn($services)
        ;

        $container->expects($spySetDefinition = $this->any())
            ->method('setDefinition')
        ;

        $mailTypeRegistryDefinition
            ->expects($spyAddArgument = $this->any())
            ->method('addArgument')
        ;
        $registerMailHandlersPass = new RegisterMailTypesPass();

        $registerMailHandlersPass->process($container);

        $addArgumentInvocations = $spyAddArgument->getInvocations();

        $this->assertEquals(1, count($addArgumentInvocations));

        $addArgument = end($addArgumentInvocations);
        $locatorDefinition = $addArgument->getParameters()[0];
        $setDefinitionInvocations = $spySetDefinition->getInvocations();

        $this->assertEquals(1, count($setDefinitionInvocations));

        $setDefinition = end($setDefinitionInvocations);
        $setDefinitionParamId = $setDefinition->getParameters()[0];

        $this->assertEquals($locatorDefinition, $setDefinitionParamId);

        $setDefinitionParamDefinition = $setDefinition->getParameters()[1];

        $arguments = [];

        /**
         * @var ServiceClosureArgument $value
         */
        foreach ($setDefinitionParamDefinition->getArgument(0) as $key => $value) {
            $this->assertInstanceOf(ServiceClosureArgument::class, $value);
            $arguments[$key] = (string) $value->getValues()[0];
        }

        $this->assertEquals($arguments, [
            $mailTypesDefinitions['my_mail_type_service4']->getClass() => 'my_mail_type_service4',
                'my_type1' => 'my_mail_type_service1',
                'my_type2' => 'my_mail_type_service2',
                'my_type3' => 'my_mail_type_service2',
                'my_type4' => 'my_mail_type_service3',
        ]);
    }

    /**
     * @dataProvider getNotValidMailHandlerOptions
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     *
     * @param array $options
     */
    public function testExceptionIfMailHandlerIsNotValid(array $options)
    {
        $mailHandlerRegistryDefinition = $this->createMock(Definition::class);
        $container = $this->getMockBuilder(ContainerBuilder::class)
            ->setMethods(['findTaggedServiceIds', 'getDefinition'])
            ->getMock()
        ;
        $container
            ->method('findTaggedServiceIds')
            ->willReturn(['my_mail_type_service1' => [['type' => 'my_type1']]])
        ;
        $container
            ->method('getDefinition')
            ->willReturnMap([
                [LazyMailTypeRegistry::class, $mailHandlerRegistryDefinition],
                ['my_mail_type_service1', $this->createMailTypeDefinition($options)],
            ])
        ;
        $registerMailHandlersPass = new RegisterMailTypesPass();
        $registerMailHandlersPass->process($container);
    }

    /**
     * @return array
     */
    public function getNotValidMailHandlerOptions()
    {
        return [
            [['isAbstract' => true]],
            [['class' => \stdClass::class]],
            [['class' => 'Foo']],
        ];
    }

    /**
     * @param array $customOptions
     * @return Definition|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createMailTypeDefinition(array $customOptions = [])
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefaults([
            'isAbstract' => false,
            'isPublic' => true,
            'class' => get_class($this->getMockForAbstractClass(MailTypeInterface::class)),
        ]);
        $options = $optionsResolver->resolve($customOptions);

        $definition = $this->createMock(Definition::class);
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
