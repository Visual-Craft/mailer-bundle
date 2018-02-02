<?php

namespace VisualCraft\Bundle\MailerBundle\MailTypeRegistry;

use VisualCraft\Bundle\MailerBundle\Exception\MissingMailTypeException;
use VisualCraft\Bundle\MailerBundle\MailType\MailTypeInterface;

interface MailTypeRegistryInterface
{
    /**
     * @param string $type
     * @throws MissingMailTypeException
     * @return MailTypeInterface
     */
    public function getMailType($type);
}
