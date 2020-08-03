<?php

namespace VisualCraft\Bundle\MailerBundle;

use Twig\Environment;

trait TwigMailRendererTrait
{
    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @param Environment $value
     */
    public function setTwig(Environment $value)
    {
        $this->twig = $value;
    }

    /**
     * @param string $template
     * @param array $parameters
     *
     * @return string
     */
    protected function renderBody($template, array $parameters = [])
    {
        return $this->twig->render($template, $parameters);
    }

    /**
     * @param string $template
     * @param array $parameters
     *
     * @return string
     */
    protected function renderSubject($template, array $parameters = [])
    {
        return trim($this->twig->render($template, $parameters));
    }
}
