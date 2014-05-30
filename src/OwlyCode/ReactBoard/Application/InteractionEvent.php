<?php

namespace OwlyCode\ReactBoard\Application;

use Guzzle\Http\Message\RequestInterface;
use Symfony\Component\EventDispatcher\Event;

class InteractionEvent extends Event
{
    /**
     * @var Guzzle\Http\Message\RequestInterface
     */
    private $request;

    /**
     * @var mixed
     */
    private $result;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }
}
