<?php

namespace VisualCraft\Bundle\MailerBundle;

use VisualCraft\Bundle\MailerBundle\Exception\MissingMailHandlerException;

interface MailHandlerRegistryInterface
{
    /**
     * @param string $alias
     * @return MailHandlerInterface
     * @throws MissingMailHandlerException
     */
    public function getMailHandler($alias);
}
