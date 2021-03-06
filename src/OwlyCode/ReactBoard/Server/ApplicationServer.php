<?php

namespace OwlyCode\ReactBoard\Server;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use OwlyCode\ReactBoard\Application\ApplicationRepository;
use OwlyCode\ReactBoard\Exception\ApplicationNotFoundException;
use Ratchet\ConnectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationServer implements ServingCapableInterface
{
    /**
     * @var OwlyCode\ReactBoard\Application\ApplicationRepository
     */
    private $applications;

    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher, ApplicationRepository $applications)
    {
        $this->dispatcher = $dispatcher;
        $this->applications = $applications;

        $this->dispatcher->addListener('reactboard.start', array($this, 'onReactBoardStart'));
    }

    public function onReactBoardStart()
    {
        $main = $this->applications->getMainApplication();
        $main->setApplications($this->applications);
        $main->setCurrentApplication($this->applications->get($main->getDefaultAppName()));
        $main->setCurrentModule($main->getDefaultModule());
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
