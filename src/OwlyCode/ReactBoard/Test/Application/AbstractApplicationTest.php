<?php

namespace OwlyCode\ReactBoard\Test\Application;

use OwlyCode\ReactBoard\Application\InteractionEvent;

class AbstractApplicationTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $twig;
    private $dispatcher;
    private $request;

    protected function setUp()
    {
        $this->twig = $this->getMockBuilder('\Twig_Environment')->disableOriginalConstructor()->getMock();
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->request = $this->getMockBuilder('Guzzle\Http\Message\RequestInterface')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    public function testRender()
    {
        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with($this->stringContains('PHPUnitFrameworkMockObject/views/foo.html'), array('bar' => 'baz'))
            ->will($this->returnValue('foobar'))
        ;

        $this->container->expects($this->once())->method('get')->with('twig')->will($this->returnValue($this->twig));

        $application = $this->getMockForAbstractClass('OwlyCode\ReactBoard\Application\AbstractApplication');
        $application->setContainer($this->container);
        $this->assertSame('foobar', $application->render('foo.html', array('bar' => 'baz')));
    }

    public function testGetViewDir()
    {
        $application = $this->getMockForAbstractClass('OwlyCode\ReactBoard\Application\AbstractApplication');
        $application->setContainer($this->container);
        $this->assertContains('PHPUnitFrameworkMockObject/views', $application->getViewDir());
    }

    public function testGet()
    {
        $this->container->expects($this->once())->method('get')->with('twig')->will($this->returnValue($this->twig));
        $application = $this->getMockForAbstractClass('OwlyCode\ReactBoard\Application\AbstractApplication');
        $application->setContainer($this->container);
        $this->assertSame($this->twig, $application->get('twig'));
    }

    public function testExecute()
    {
        $this->dispatcher->expects($this->once())->method('dispatch')->with('default.request.bar', $this->callback(function ($object) {
            return $object instanceof InteractionEvent;
        }));

        $this->container->expects($this->once())->method('get')->with('event_dispatcher')->will($this->returnValue($this->dispatcher));
        $application = $this->getMockForAbstractClass('OwlyCode\ReactBoard\Application\AbstractApplication');
        $application->setContainer($this->container);
        $application->execute('bar', $this->request);
    }

    protected function tearDown()
    {
        $this->container = null;
        $this->twig = null;
        $this->dispatcher = null;
        $this->request = null;
    }
}
