<?php

namespace OwlyCode\ReactBoard\Server;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use OwlyCode\ReactBoard\Application\ApplicationInterface;
use OwlyCode\ReactBoard\Application\ApplicationRepository;
use OwlyCode\ReactBoard\Exception\ApplicationNotFoundException;
use Ratchet\ConnectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationServer implements ServingCapableInterface
{
    private $applications;

    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher, ApplicationRepository $applications)
    {
        $this->dispatcher = $dispatcher;
        $this->applications = $applications;

        $this->dispatcher->addListener('reactboard.start', array($this, 'onReactBoardStart'));
    }

    public function onReactBoardStart()
    {
        $js = array();
        $css = array();

        foreach ($this->applications->getArray() as $application) {
            $js = array_merge($js, array_map(function($i) use ($application) { return $application->getName() . '/' . $i ; }, $application->getJavascripts()));
            $css = array_merge($css, array_map(function($i) use ($application) { return $application->getName() . '/' . $i ; }, $application->getStylesheets()));
        }

        $this->applications->get('home')->autoloadAssets($js, $css);
    }

    public function serve(ConnectionInterface $conn, RequestInterface $request = null, array $parameters) {
        try {
            $application = $this->applications->get($parameters['application']);
            $conn->send((string)$application->execute($parameters['module'], $request));
            $conn->close();
        } catch(ApplicationNotFoundException $e) {
            $response = new Response(404, null, $e->getMessage());
            $conn->send((string)$response);
            $conn->close();
        } catch(\Exception $e) {
            $response = new Response(500, null, $e->getMessage());
            $conn->send((string)$response);
            $conn->close();
        }
    }
}
