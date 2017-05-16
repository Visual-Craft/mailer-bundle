<?php

namespace VisualCraft\Bundle\MailerBundle;

use Symfony\Component\OptionsResolver\OptionsResolver;
use VisualCraft\Bundle\MailerBundle\Exception\InvalidMailHandlerOptionsException;

class MessageFactory implements MessageFactoryInterface
{
    /**
     * @var MailHandlerRegistryInterface
     */
    private $registry;

    /**
     * @param MailHandlerRegistryInterface $registry
     */
    public function __construct(MailHandlerRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage($alias, array $options = [])
    {
        $handler = $this->registry->getMailHandler($alias);
        $optionsResolver = $this->createOptionsResolverInstance();
        $handler->configureOptions($optionsResolver);

        try {
            $options = $optionsResolver->resolve($options);
        } catch (\Exception $e) {
            throw new InvalidMailHandlerOptionsException(sprintf("Invalid options are provided for mailer handler '%s'.", $alias), 0, $e);
        }

        $message = $this->createMessageInstance();
        $handler->buildMessage($message, $options);

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
        return \Swift_Message::newInstance();
    }
}
