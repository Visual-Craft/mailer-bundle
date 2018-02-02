<?php

namespace VisualCraft\Bundle\MailerBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use VisualCraft\Bundle\MailerBundle\DependencyInjection\CompilerPass\RegisterMailTypesPass;

class VisualCraftMailerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container
            ->addCompilerPass(new RegisterMailTypesPass())
        ;
    }
}
