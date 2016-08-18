<?php

namespace VisualCraft\Bundle\MailerBundle;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface MailHandlerInterface
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
