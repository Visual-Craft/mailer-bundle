<?php

namespace VisualCraft\Bundle\MailerBundle;

use Twig\Environment;

interface TwigAwareInterface
{
    /**
     * @param Environment $value
     */
    public function setTwig(Environment $value);
}
