<?php

namespace VisualCraft\Bundle\MailerBundle\Traits;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

trait TemplatingAwareMailHandlerTrait
{
    /**
     * @var EngineInterface
     */
    protected $templatingEngine;

    /**
     * @param EngineInterface $value
     */
    public function setTemplatingEngine(EngineInterface $value)
    {
        $this->templatingEngine = $value;
    }

    /**
     * @param string $template
     * @param array $parameters
     *
     * @return string
     */
    protected function renderBody($template, array $parameters = [])
    {
        return $this->templatingEngine->render($template, $parameters);
    }

    /**
     * @param string $template
     * @param array $parameters
     *
     * @return string
     */
    protected function renderSubject($template, array $parameters = [])
    {
        return trim($this->templatingEngine->render($template, $parameters));
    }
}
