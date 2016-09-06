<?php

namespace VisualCraft\Bundle\MailerBundle;

use Symfony\Component\OptionsResolver\OptionsResolver;
use VisualCraft\Bundle\MailerBundle\Exception\InvalidMailHandlerOptionsException;

class Mailer
{
    /**
     * @var MailHandlerRegistryInterface
     */
    private $registry;

    /**
     * @var SwiftMailerProvider
     */
    private $mailerProvider;

    /**
     * @param SwiftMailerProvider $mailerProvider
     * @param MailHandlerRegistryInterface $registry
     */
    public function __construct(SwiftMailerProvider $mailerProvider, MailHandlerRegistryInterface $registry)
    {
        $this->mailerProvider = $mailerProvider;
        $this->registry = $registry;
    }

    /**
     * @param string $alias
     * @param array $options
     * @param string|null $swiftMailerName
     * @return SendStatus
     */
    public function send($alias, array $options = [], $swiftMailerName = null)
    {
        $mailer = $this->mailerProvider->getMailer($swiftMailerName);
        $handler = $this->registry->getMailHandler($alias);
        $optionsResolver = new OptionsResolver();
        $handler->configureOptions($optionsResolver);

        try {
            $options = $optionsResolver->resolve($options);
        } catch (\Exception $e) {
            throw new InvalidMailHandlerOptionsException(sprintf("Invalid options are provided for mailer handler '%s'.", $alias), 0, $e);
        }

        $message = \Swift_Message::newInstance();
        $handler->buildMessage($message, $options);
        $failedRecipients = [];
        $sent = $mailer->send($message, $failedRecipients);

        return new SendStatus($sent, $failedRecipients);
    }
}
