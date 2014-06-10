<?php

namespace OwlyCode\ReactBoard\Server;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Mimetypes;
use OwlyCode\ReactBoard\Asset\AssetRepository;
use OwlyCode\ReactBoard\Exception\AssetNotFoundException;
use Ratchet\ConnectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class AssetServer implements ServingCapableInterface
{
    /**
     * @var OwlyCode\ReactBoard\Asset\AssetRepository
     */
    private $assets;

    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher, AssetRepository $assets)
    {
        $this->dispatcher = $dispatcher;
        $this->assets = $assets;
    }

    public function serve(ConnectionInterface $conn, RequestInterface $request = null, array $parameters) {
        try {
            $path = $this->assets->get($parameters['asset'])->getFullPath();

            if (!file_exists($path)) {
                throw new FileNotFoundException($path);
            }

            $response = new Response(200, array('Content-Type' => Mimetypes::getInstance()->fromFilename($path)), file_get_contents($path));

            $conn->send((string)$response);
            $conn->close();
        } catch(AssetNotFoundException $e) {
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
