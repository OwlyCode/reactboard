<?php

namespace OwlyCode\ReactBoard\Server;

use Guzzle\Http\Message\RequestInterface;
use Ratchet\ConnectionInterface;

interface ServingCapableInterface
{
    public function serve(ConnectionInterface $conn, RequestInterface $request = null, array $parameters);
}
