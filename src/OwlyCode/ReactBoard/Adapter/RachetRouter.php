<?php

namespace OwlyCode\ReactBoard\Adapter;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Url;
use OwlyCode\ReactBoard\Server\ApplicationServer;
use OwlyCode\ReactBoard\Server\ServingCapableInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServerInterface;
use Ratchet\Http\Router;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class RachetRouter extends Router implements HttpServerInterface
{
    /**
     * {@inheritdoc}
     */
    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null) {
        if (null === $request) {
            throw new \UnexpectedValueException('$request can not be null');
        }

        $context = $this->_matcher->getContext();
        $context->setMethod($request->getMethod());
        $context->setHost($request->getHost());

        try {
            $parameters = $this->_matcher->match($request->getPath());
        } catch (MethodNotAllowedException $nae) {
            return $this->close($conn, 403);
        } catch (ResourceNotFoundException $nfe) {
            return $this->close($conn, 404);
        }

        if ($parameters['_controller'] instanceof ServingCapableInterface) {
            $parameters['_controller']->serve($conn, $request, $parameters);
        } else {
            $query = array();
            foreach($query as $key => $value) {
                if ((is_string($key)) && ('_' !== substr($key, 0, 1))) {
                    $query[$key] = $value;
                }
            }
            $url = Url::factory($request->getPath());
            $url->setQuery($query);
            $request->setUrl($url);

            $conn->controller = $parameters['_controller'];
            $conn->controller->onOpen($conn, $request);
        }
    }
}
