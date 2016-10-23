<?php

namespace VisualCraft\Bundle\MailerBundle\Tests\MailHandler;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use VisualCraft\Bundle\MailerBundle\MailHandler\TemplatingAwareMailHandlerTrait;

class TemplatingAwareMailHandlerTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderBody()
    {
        $templateName = 'boo.html.twig';
        $templateParameters = ['bar' => 'foo'];
        $willReturn = 'foo';
        $templating = $this->getMockForAbstractClass(EngineInterface::class);
        $templating
            ->expects($this->once())
            ->method('render')
            ->with($templateName, $templateParameters)
            ->willReturn($willReturn)
        ;

        $templatingAwareMailHandler = $this->getMockForTrait(TemplatingAwareMailHandlerTrait::class);
        $templatingAwareMailHandler->setTemplatingEngine($templating);
        $closure = function ($template, $parameters) {
            /** @var $this TemplatingAwareMailHandlerTrait */
            return $this->renderBody($template, $parameters);
        };
        $method = $closure->bindTo($templatingAwareMailHandler, $templatingAwareMailHandler);

        $this->assertSame($willReturn, $method($templateName, $templateParameters));
    }

    public function testRenderSubjectWillBeCalled()
    {
        $templateName = 'boo.html.twig';
        $templateParameters = ['bar' => 'foo'];
        $templating = $this->getMockForAbstractClass(EngineInterface::class);
        $templating
            ->expects($this->once())
            ->method('render')
            ->with($templateName, $templateParameters)
        ;

        $templatingAwareMailHandler = $this->getMockForTrait(TemplatingAwareMailHandlerTrait::class);
        $templatingAwareMailHandler->setTemplatingEngine($templating);
        $closure = function ($template, $parameters) {
            /** @var $this TemplatingAwareMailHandlerTrait */
            return $this->renderSubject($template, $parameters);
        };
        $method = $closure->bindTo($templatingAwareMailHandler, $templatingAwareMailHandler);
        $method($templateName, $templateParameters);
    }

    /**
     * @dataProvider getRenderSubjectData
     *
     * @param string $templateEngineReturn
     * @param string $expectedReturn
     */
    public function testRenderSubject($templateEngineReturn, $expectedReturn)
    {
        $templating = $this->getMockForAbstractClass(EngineInterface::class);
        $templating
            ->method('render')
            ->willReturn($templateEngineReturn)
        ;

        $templatingAwareMailHandler = $this->getMockForTrait(TemplatingAwareMailHandlerTrait::class);
        $templatingAwareMailHandler->setTemplatingEngine($templating);
        $closure = function ($template, $parameters) {
            /** @var $this TemplatingAwareMailHandlerTrait */
            return $this->renderSubject($template, $parameters);
        };
        $method = $closure->bindTo($templatingAwareMailHandler, $templatingAwareMailHandler);

        $this->assertSame($expectedReturn, $method('boo.html.twig', ['bar' => 'bar']));
    }

    /**
     * @return array
     */
    public function getRenderSubjectData()
    {
        return [
            ['foo', 'foo'],
            [' foo ', 'foo'],
            ["foo\n", 'foo'],
            [null, ''],
        ];
    }
}
