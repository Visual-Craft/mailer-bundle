<?php

namespace VisualCraft\Bundle\MailerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use VisualCraft\Bundle\MailerBundle\MailType\MailTypeInterface;
use VisualCraft\Bundle\MailerBundle\TwigAwareInterface;

class RegisterMailTypesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        $registry = $container->getDefinition('visual_craft_mailer.mail_type_registry.lazy');
        $mailTypesTag = 'visual_craft_mailer.mail_type';
        $mailTypes = $container->findTaggedServiceIds($mailTypesTag);
        $mailTypesMap = [];

        foreach ($mailTypes as $id => $attributes) {
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

            if (!is_subclass_of($class, MailTypeInterface::class)) {
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
                    $mailTypesTag,
                    MailTypeInterface::class
                ));
            }

            if (is_subclass_of($class, TwigAwareInterface::class)) {
                if (!$container->hasDefinition('twig')) {
                    throw new InvalidArgumentException(sprintf("The service '%s' is require for '%s'", 'twig', TwigAwareInterface::class));
                }

                $definition->addMethodCall('setTwig', [new Reference('twig')]);
            }

            $noType = false;

            foreach ($attributes as $attribute) {
                if (isset($attribute['type'])) {
                    $mailTypesMap[$attribute['type']] = $id;
                } else {
                    $noType = true;
                }
            }

            if ($noType) {
                $mailTypesMap[$definition->getClass()] = $id;
            }
        }

        if ($mailTypesMap) {
            $registry->replaceArgument(1, $mailTypesMap);
        }
    }
}
