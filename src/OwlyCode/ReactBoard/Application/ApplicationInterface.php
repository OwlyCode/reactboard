<?php

namespace OwlyCode\ReactBoard\Application;

use Guzzle\Http\Message\RequestInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ApplicationInterface {
    public function buildContainer();

    public function init();

    public function getName();

    public function execute($moduleName, RequestInterface $request);

    public function setContainer(ContainerBuilder $container);
}
