<?php

namespace VisualCraft\Bundle\MailerBundle\MailTypeRegistry;

use Symfony\Component\DependencyInjection\ServiceLocator;
use VisualCraft\Bundle\MailerBundle\Exception\MissingMailTypeException;
use VisualCraft\Bundle\MailerBundle\MailType\MailTypeInterface;

class LazyMailTypeRegistry implements MailTypeRegistryInterface
{
    /**
     * @var ServiceLocator
     */
    private $serviceLocator;

    /**
     * @param ServiceLocator $serviceLocator
     */
    public function __construct(ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * {@inheritdoc}
     * @throws \LogicException
     */
    public function getMailType($type)
    {
        if (!$this->serviceLocator->has($type)) {
            throw new MissingMailTypeException(sprintf(
                "Mail type '%s' is not registered.",
                $type
            ));
        }

        $mailType = $this->serviceLocator->get($type);

        if (!$mailType instanceof MailTypeInterface) {
            throw new \LogicException(sprintf(
                "Expected service instance '%s' to be instance of %s, but got instance of '%s'.",
                $type,
                MailTypeInterface::class,
                get_class($mailType)
            ));
        }

        return $mailType;
    }
}
