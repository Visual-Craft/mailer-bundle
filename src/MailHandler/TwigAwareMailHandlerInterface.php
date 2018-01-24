<?php

namespace VisualCraft\Bundle\MailerBundle\MailHandler;

interface TwigAwareMailHandlerInterface
{
    /**
     * @param \Twig_Environment $value
     */
    public function setTwigEnvironment(\Twig_Environment $value);
}
