<?php

namespace VisualCraft\Bundle\MailerBundle;

class Mailer
{
    /**
     * @var SwiftMailerProvider
     */
    private $mailerProvider;

    /**
     * @var MessageFactoryInterface
     */
    private $messageFactory;

    /**
     * @param SwiftMailerProvider $mailerProvider
     * @param MessageFactoryInterface $messageFactory
     */
    public function __construct(SwiftMailerProvider $mailerProvider, MessageFactoryInterface $messageFactory)
    {
        $this->mailerProvider = $mailerProvider;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param string $alias
     * @param array $options
     * @param string|null $swiftMailerName
     * @return SendStatus
     */
    public function send($alias, array $options = [], $swiftMailerName = null)
    {
        $message = $this->messageFactory->createMessage($alias, $options);
        $mailer = $this->mailerProvider->getMailer($swiftMailerName);
        $failedRecipients = [];
        $sent = $mailer->send($message, $failedRecipients);

        return new SendStatus($sent, $failedRecipients);
    }
}
