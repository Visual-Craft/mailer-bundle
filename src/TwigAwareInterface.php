<?php

namespace VisualCraft\Bundle\MailerBundle;

interface TwigAwareInterface
{
    /**
     * @param \Twig_Environment $value
     */
    public function setTwig(\Twig_Environment $value);
}
