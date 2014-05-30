<?php

namespace OwlyCode\ReactBoard\Server;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Mimetypes;
use OwlyCode\ReactBoard\Application\ApplicationInterface;
use OwlyCode\ReactBoard\Application\ApplicationRepository;
use Ratchet\ConnectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;

class AssetServer implements ServingCapableInterface
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
    }

    public function serve(ConnectionInterface $conn, RequestInterface $request = null, array $parameters) {
        try {
            $application = $this->applications->get($parameters['application']);

            $path = $application->getAssetsDir() . DIRECTORY_SEPARATOR . $parameters['asset'];

            if(!file_exists($path)) {
                throw new FileNotFoundException($path);
            }

            $response = new Response(200, array('Content-Type' => Mimetypes::getInstance()->fromFilename($path)), file_get_contents($path));

            $conn->send((string)$response);
            $conn->close();
        } catch(ApplicationNotFoundException $e) {
            $response = new Response(404, null, '');
            $conn->send((string)$response);
            $conn->close();
        } catch(FileNotFoundException $e) {
            $response = new Response(404, null, '');
            $conn->send((string)$response);
            $conn->close();
        } catch(\Exception $e) {
            $response = new Response(500, null, '');
            $conn->send((string)$response);
            $conn->close();
        }
    }
}
