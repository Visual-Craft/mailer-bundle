<?php

namespace VisualCraft\Bundle\MailerBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use VisualCraft\Bundle\MailerBundle\DependencyInjection\CompilerPass\RegisterMailHandlersPass;

class VisualCraftMailerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container
            ->addCompilerPass(new RegisterMailHandlersPass())
        ;
    }
}
