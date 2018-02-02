<?php

namespace VisualCraft\Bundle\MailerBundle\MessageFactory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use VisualCraft\Bundle\MailerBundle\Exception\InvalidMailTypeOptionsException;
use VisualCraft\Bundle\MailerBundle\MailTypeRegistry\MailTypeRegistryInterface;

class MessageFactory implements MessageFactoryInterface
{
    /**
     * @var MailTypeRegistryInterface
     */
    private $registry;

    /**
     * @param MailTypeRegistryInterface $registry
     */
    public function __construct(MailTypeRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage($type, array $options = [])
    {
        $mailType = $this->registry->getMailType($type);
        $optionsResolver = $this->createOptionsResolverInstance();
        $mailType->configureOptions($optionsResolver);

        try {
            $options = $optionsResolver->resolve($options);
        } catch (\Exception $e) {
            throw new InvalidMailTypeOptionsException(sprintf("Invalid options are provided for mail type '%s'.", $type), 0, $e);
        }

        $message = $this->createMessageInstance();
        $mailType->buildMessage($message, $options);

        return $message;
    }

    /**
     * @return OptionsResolver
     */
    protected function createOptionsResolverInstance()
    {
        return new OptionsResolver();
    }

    /**
     * @return \Swift_Message
     */
    protected function createMessageInstance()
    {
        return new \Swift_Message();
    }
}
