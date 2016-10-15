<?php

namespace VisualCraft\Bundle\MailerBundle\MailHandlerRegistry;

use Symfony\Component\DependencyInjection\ContainerInterface;
use VisualCraft\Bundle\MailerBundle\Exception\MissingMailHandlerException;
use VisualCraft\Bundle\MailerBundle\Exception\UnexpectedMailHandlerTypeException;
use VisualCraft\Bundle\MailerBundle\MailHandlerInterface;
use VisualCraft\Bundle\MailerBundle\MailHandlerRegistryInterface;

class LazyMailHandlerRegistry implements MailHandlerRegistryInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $handlersMap;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->handlersMap = [];
    }

    /**
     * @param string $alias
     * @param string $serviceId
     */
    public function registerMailHandler($alias, $serviceId)
    {
        $this->handlersMap[$alias] = $serviceId;
    }

    /**
     * {@inheritdoc}
     */
    public function getMailHandler($alias)
    {
        if (!isset($this->handlersMap[$alias])) {
            throw new MissingMailHandlerException(sprintf(
                "Mailer handler with alias '%s' is not registered.",
                $alias
            ));
        }

        if (!$this->container->has($this->handlersMap[$alias])) {
            throw new MissingMailHandlerException(sprintf(
                "Mailer handler with alias '%s' has reference to missing service '%s'.",
                $alias,
                $this->handlersMap[$alias]
            ));
        }

        $mailHandler = $this->container->get($this->handlersMap[$alias]);

        if (!$mailHandler instanceof MailHandlerInterface) {
            throw new UnexpectedMailHandlerTypeException();
        }

        return $mailHandler;
    }
}
