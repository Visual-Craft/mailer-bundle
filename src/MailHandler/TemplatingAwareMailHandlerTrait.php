<?php

namespace VisualCraft\Bundle\MailerBundle\Traits;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

trait TemplatingAwareMailHandlerTrait
{
    /**
     * @var EngineInterface
     */
    protected $templateEngine;

    /**
     * @param EngineInterface $value
     *
     * @return $this
     */
    public function setTemplateEngine(EngineInterface $value)
    {
        $this->templateEngine = $value;

        return $this;
    }

    /**
     * @param string $template
     * @param array $parameters
     *
     * @return string
     */
    protected function renderBody($template, array $parameters = [])
    {
        return $this->templateEngine->render($template, $parameters);
    }

    /**
     * @param string $template
     * @param array $parameters
     *
     * @return string
     */
    protected function renderSubject($template, array $parameters = [])
    {
        return trim($this->templateEngine->render($template, $parameters));
    }
}
