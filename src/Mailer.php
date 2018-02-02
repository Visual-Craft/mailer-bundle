<?php

namespace VisualCraft\Bundle\MailerBundle;

use VisualCraft\Bundle\MailerBundle\MessageFactory\MessageFactoryInterface;
use VisualCraft\Bundle\MailerBundle\SwiftMailerProvider\SwiftMailerProviderInterface;

class Mailer
{
    /**
     * @var SwiftMailerProviderInterface
     */
    private $mailerProvider;

    /**
     * @var MessageFactoryInterface
     */
    private $messageFactory;

    /**
     * @param SwiftMailerProviderInterface $mailerProvider
     * @param MessageFactoryInterface $messageFactory
     */
    public function __construct(SwiftMailerProviderInterface $mailerProvider, MessageFactoryInterface $messageFactory)
    {
        $this->mailerProvider = $mailerProvider;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param string $type
     * @param array $options
     * @param string|null $swiftMailerName
     * @return SendStatus
     */
    public function send($type, array $options = [], $swiftMailerName = null)
    {
        $message = $this->messageFactory->createMessage($type, $options);
        $mailer = $this->mailerProvider->getMailer($swiftMailerName);
        $failedRecipients = [];
        $sent = $mailer->send($message, $failedRecipients);

        return new SendStatus($sent, $failedRecipients);
    }
}
