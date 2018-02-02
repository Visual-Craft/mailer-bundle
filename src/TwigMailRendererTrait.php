<?php

namespace VisualCraft\Bundle\MailerBundle;

trait TwigMailRendererTrait
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param \Twig_Environment $value
     */
    public function setTwig(\Twig_Environment $value)
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
