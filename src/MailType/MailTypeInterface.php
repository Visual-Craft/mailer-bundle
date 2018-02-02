<?php

namespace VisualCraft\Bundle\MailerBundle\MailType;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface MailTypeInterface
{
    /**
     * @param OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver);

    /**
     * @param \Swift_Message $message
     * @param array $options
     */
    public function buildMessage(\Swift_Message $message, array $options);
}
