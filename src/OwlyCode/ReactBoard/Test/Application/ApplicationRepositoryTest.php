<?php

namespace OwlyCode\ReactBoard\Test\Application;

use OwlyCode\ReactBoard\Application\ApplicationRepository;

class ApplicationRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $dispatcher;

    private $application;

    private $mainApplication;

    protected function setUp()
    {
        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->application = $this->getMockBuilder('OwlyCode\ReactBoard\Application\ApplicationInterface')->getMock();
        $this->mainApplication = $this->getMockBuilder('OwlyCode\ReactBoard\Application\MainApplicationInterface')->getMock();
    }

    public function testRegisterAndInitApplication()
    {
        $this->application->expects($this->once())->method('getName')->will($this->returnValue('foo'));
        $this->application->expects($this->once())->method('buildContainer');
        $this->application->expects($this->once())->method('init');

        $repository = new ApplicationRepository($this->dispatcher);
        $repository->register($this->application);

        $this->assertSame($this->application, $repository->get('foo'));
        $this->assertSame(array('foo' => $this->application), $repository->getArray());

        $repository->init();
    }

    /**
     * @expectedException OwlyCode\ReactBoard\Exception\ApplicationNotFoundException
     * @expectedExceptionMessage The requested foo application was not registered in the ApplicationServer.
     */
    public function testApplicationNotFound()
    {
        $repository = new ApplicationRepository($this->dispatcher);

        $this->assertSame($this->application, $repository->get('foo'));
    }

    public function testRegisterMainApplication()
    {
        $this->mainApplication->expects($this->once())->method('getName')->will($this->returnValue('foo'));
        $this->mainApplication->expects($this->once())->method('buildContainer');

        $repository = new ApplicationRepository($this->dispatcher);
        $repository->register($this->mainApplication);

        $this->assertSame($this->mainApplication, $repository->getMainApplication());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage You cannot have two main applications.
     */
    public function testRegisterMainApplicationTwice()
    {
        $this->mainApplication->expects($this->once())->method('getName')->will($this->returnValue('foo'));
        $this->mainApplication->expects($this->once())->method('buildContainer');

        $repository = new ApplicationRepository($this->dispatcher);
        $repository->register($this->mainApplication);
        $repository->register($this->mainApplication);
    }

    protected function tearDown()
    {
        $this->dispatcher = null;
        $this->application = null;
        $this->mainApplication = null;
    }
}
