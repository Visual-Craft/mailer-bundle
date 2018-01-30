<?php

namespace VisualCraft\Bundle\MailerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use VisualCraft\Bundle\MailerBundle\MailHandler\MailHandlerInterface;
use VisualCraft\Bundle\MailerBundle\MailHandler\TwigAwareMailHandlerInterface;

class RegisterMailHandlersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        $registry = $container->getDefinition('visual_craft_mailer.mail_handler_registry.lazy');
        $handlersTag = 'visual_craft_mailer.mail_handler';
        $handlers = $container->findTaggedServiceIds($handlersTag);
        $handlersMap = [];

        foreach ($handlers as $id => $attributes) {
            $definition = $container->getDefinition($id);

            if (!$definition->isPublic()) {
                throw new InvalidArgumentException(sprintf(
                    'The service "%s" must be public as it can be lazy-loaded.',
                    $id
                ));
            }

            if ($definition->isAbstract()) {
                throw new InvalidArgumentException(sprintf(
                    'The service "%s" must not be abstract as it can be lazy-loaded.',
                    $id
                ));
            }

            $class = $container->getParameterBag()->resolveValue($definition->getClass());

            if (!is_subclass_of($class, MailHandlerInterface::class)) {
                if (!class_exists($class, false)) {
                    throw new InvalidArgumentException(sprintf(
                        'Class "%s" used for service "%s" cannot be found.',
                        $class,
                        $id
                    ));
                }

                throw new InvalidArgumentException(sprintf(
                    'The service "%s" tagged "%s" must be a implement interface %s".',
                    $id,
                    $handlersTag,
                    MailHandlerInterface::class
                ));
            }

            if (is_subclass_of($class, TwigAwareMailHandlerInterface::class)) {
                if (!$container->hasDefinition('twig')) {
                    throw new InvalidArgumentException(sprintf("The service '%s' is require for '%s'", 'twig', TwigAwareMailHandlerInterface::class));
                }

                $definition->addMethodCall('setTwigEnvironment', [new Reference('twig')]);
            }

            foreach ($attributes as $attribute) {
                $handlersMap[$attribute['alias']] = $id;
            }
        }

        if ($handlersMap) {
            $registry->replaceArgument(1, $handlersMap);
        }
    }
}
