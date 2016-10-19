<?php

namespace VisualCraft\Bundle\MailerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class RegisterMailHandlersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $registry = $container->getDefinition('visual_craft_mailer.mail_handler_registry.lazy');
        $handlers = $container->findTaggedServiceIds('visual_craft_mailer.mail_handler');

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

            foreach ($attributes as $attribute) {
                $registry
                    ->addMethodCall('registerMailHandler', [$attribute['alias'], $id])
                ;
            }
        }
    }
}
