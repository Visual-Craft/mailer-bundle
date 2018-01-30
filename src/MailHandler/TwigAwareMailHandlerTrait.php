<?php

namespace VisualCraft\Bundle\MailerBundle\MailHandler;

trait TwigAwareMailHandlerTrait
{
    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

    /**
     * @param \Twig_Environment $value
     */
    public function setTwigEnvironment(\Twig_Environment $value)
    {
        $this->twigEnvironment = $value;
    }

    /**
     * @param string $template
     * @param array $parameters
     *
     * @return string
     */
    protected function renderBody($template, array $parameters = [])
    {
        return $this->twigEnvironment->render($template, $parameters);
    }

    /**
     * @param string $template
     * @param array $parameters
     *
     * @return string
     */
    protected function renderSubject($template, array $parameters = [])
    {
        return trim($this->twigEnvironment->render($template, $parameters));
    }
}
