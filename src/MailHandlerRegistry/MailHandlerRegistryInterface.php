<?php

namespace VisualCraft\Bundle\MailerBundle\MailHandlerRegistry;

use VisualCraft\Bundle\MailerBundle\Exception\MissingMailHandlerException;
use VisualCraft\Bundle\MailerBundle\MailHandler\MailHandlerInterface;

interface MailHandlerRegistryInterface
{
    /**
     * @param string $alias
     * @throws MissingMailHandlerException
     * @return MailHandlerInterface
     */
    public function getMailHandler($alias);
}
