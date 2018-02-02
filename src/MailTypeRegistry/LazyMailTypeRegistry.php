<?php

namespace VisualCraft\Bundle\MailerBundle\MailTypeRegistry;

use Symfony\Component\DependencyInjection\ContainerInterface;
use VisualCraft\Bundle\MailerBundle\Exception\MissingMailTypeException;
use VisualCraft\Bundle\MailerBundle\MailType\MailTypeInterface;

class LazyMailTypeRegistry implements MailTypeRegistryInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $typesMap;

    /**
     * @param ContainerInterface $container
     * @param array $typesMap
     */
    public function __construct(ContainerInterface $container, array $typesMap)
    {
        $this->container = $container;
        $this->typesMap = $typesMap;
    }

    /**
     * {@inheritdoc}
     * @throws \LogicException
     */
    public function getMailType($type)
    {
        if (!isset($this->typesMap[$type])) {
            throw new MissingMailTypeException(sprintf(
                "Mail type '%s' is not registered.",
                $type
            ));
        }

        if (!$this->container->has($this->typesMap[$type])) {
            throw new \RuntimeException(sprintf(
                "Mail type '%s' depends on missing service '%s'.",
                $type,
                $this->typesMap[$type]
            ));
        }

        $mailType = $this->container->get($this->typesMap[$type]);

        if (!$mailType instanceof MailTypeInterface) {
            throw new \LogicException(sprintf(
                "Expected service instance '%s' to be instance of %s, but got instance of '%s'.",
                $this->typesMap[$type],
                MailTypeInterface::class,
                get_class($mailType)
            ));
        }

        return $mailType;
    }
}
