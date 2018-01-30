<?php

namespace VisualCraft\Bundle\MailerBundle\SwiftMailerProvider;

interface SwiftMailerProviderInterface
{
    /**
     * @param string|null $name
     * @return \Swift_Mailer
     */
    public function getMailer($name = null);
}
