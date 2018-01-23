<?php

namespace VisualCraft\Bundle\MailerBundle\Tests\MailHandler;

use VisualCraft\Bundle\MailerBundle\MailHandler\TwigAwareMailHandlerTrait;

class TwigAwareMailHandlerTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderBody()
    {
        $templateName = 'boo.html.twig';
        $templateParameters = ['bar' => 'foo'];
        $willReturn = 'foo';
        $twig = $this->getMockBuilder(\Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $twig
            ->expects($this->once())
            ->method('render')
            ->with($templateName, $templateParameters)
            ->willReturn($willReturn)
        ;

        $twigAwareMailHandler = $this->getMockForTrait(TwigAwareMailHandlerTrait::class);
        $twigAwareMailHandler->setTwigEnvironment($twig);
        $closure = function ($template, $parameters) {
            /** @var $this TwigAwareMailHandlerTrait */
            return $this->renderBody($template, $parameters);
        };
        $method = $closure->bindTo($twigAwareMailHandler, $twigAwareMailHandler);

        $this->assertSame($willReturn, $method($templateName, $templateParameters));
    }

    public function testRenderSubjectWillBeCalled()
    {
        $templateName = 'boo.html.twig';
        $templateParameters = ['bar' => 'foo'];
        $twig = $this->getMockBuilder(\Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $twig
            ->expects($this->once())
            ->method('render')
            ->with($templateName, $templateParameters)
        ;

        $twigAwareMailHandler = $this->getMockForTrait(TwigAwareMailHandlerTrait::class);
        $twigAwareMailHandler->setTwigEnvironment($twig);
        $closure = function ($template, $parameters) {
            /** @var $this TwigAwareMailHandlerTrait */
            return $this->renderSubject($template, $parameters);
        };
        $method = $closure->bindTo($twigAwareMailHandler, $twigAwareMailHandler);
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
        $twig = $this->getMockBuilder(\Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $twig
            ->method('render')
            ->willReturn($templateEngineReturn)
        ;

        $twigAwareMailHandler = $this->getMockForTrait(TwigAwareMailHandlerTrait::class);
        $twigAwareMailHandler->setTwigEnvironment($twig);
        $closure = function ($template, $parameters) {
            /** @var $this TwigAwareMailHandlerTrait */
            return $this->renderSubject($template, $parameters);
        };
        $method = $closure->bindTo($twigAwareMailHandler, $twigAwareMailHandler);

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
