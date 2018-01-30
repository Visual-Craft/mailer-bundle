<?php

namespace VisualCraft\Bundle\MailerBundle\SwiftMailerProvider;

use Symfony\Component\DependencyInjection\ContainerInterface;
use VisualCraft\Bundle\MailerBundle\Exception\MissingSwiftMailerException;

class LazySwiftMailerProvider implements SwiftMailerProviderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $mailersMap;

    /**
     * @var string
     */
    private $defaultMailer;

    /**
     * @param ContainerInterface $container
     * @param array $mailersMap
     * @param string $defaultMailer
     */
    public function __construct(ContainerInterface $container, array $mailersMap, $defaultMailer)
    {
        $this->container = $container;
        $this->mailersMap = $mailersMap;
        $this->defaultMailer = $defaultMailer;
    }

    /**
     * {@inheritdoc}
     */
    public function getMailer($name = null)
    {
        if ($name === null) {
            $name = $this->defaultMailer;
        }

        if (!isset($this->mailersMap[$name])) {
            throw new MissingSwiftMailerException(sprintf("Swift mailer with name '%s' is not registered.", $name));
        }

        $serviceId = $this->mailersMap[$name];

        if (!$this->container->has($serviceId)) {
            throw new \LogicException(sprintf("Missing service '%s' for Swift mailer instance '%s'.", $serviceId, $name));
        }

        $mailer = $this->container->get($serviceId);

        if (!$mailer instanceof \Swift_Mailer) {
            throw new \LogicException(sprintf(
                "Expected service instance '%s' to be instance of \\Swift_Mailer, but got instance of '%s'.",
                $serviceId,
                get_class($mailer)
            ));
        }

        return $mailer;
    }
}
