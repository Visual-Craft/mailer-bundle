<?php

namespace VisualCraft\Bundle\MailerBundle\MessageFactory;

interface MessageFactoryInterface
{
    /**
     * @param string $type
     * @param array $options
     * @return \Swift_Message
     */
    public function createMessage($type, array $options = []);
}
