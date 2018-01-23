<?php

namespace VisualCraft\Bundle\MailerBundle;

use VisualCraft\Bundle\MailerBundle\Exception\MissingMailHandlerException;

interface MailHandlerRegistryInterface
{
    /**
     * @param string $alias
     * @throws MissingMailHandlerException
     * @return MailHandlerInterface
     */
    public function getMailHandler($alias);
}
