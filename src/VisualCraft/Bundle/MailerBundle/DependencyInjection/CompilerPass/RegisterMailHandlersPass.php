<?php

namespace VisualCraft\Bundle\MailerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
            foreach ($attributes as $attribute) {
                $registry
                    ->addMethodCall('registerMailHandler', [$attribute['alias'], $id])
                ;
            }
        }
    }
}
