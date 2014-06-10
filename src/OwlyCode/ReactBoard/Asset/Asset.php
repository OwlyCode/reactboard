<?php

namespace OwlyCode\ReactBoard\Asset;

use OwlyCode\ReactBoard\Application\ApplicationInterface;

class Asset extends AbstractAsset implements AssetInterface
{
    private $application;

    public function __construct(ApplicationInterface $application, $rootPath, $path, $type = null)
    {
        parent::__construct($rootPath, $path, $type);
        $this->application = $application;
    }

    public function getFullPath()
    {
        return $this->rootPath . DIRECTORY_SEPARATOR . $this->path;
    }

    public function getWebPath()
    {
        return $this->application->getName() . DIRECTORY_SEPARATOR . $this->path;
    }
}
