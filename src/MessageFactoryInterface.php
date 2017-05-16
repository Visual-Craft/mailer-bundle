<?php

namespace VisualCraft\Bundle\MailerBundle;

interface MessageFactoryInterface
{
    /**
     * @param string $alias
     * @param array $options
     * @return \Swift_Message
     */
    public function createMessage($alias, array $options = []);
}
