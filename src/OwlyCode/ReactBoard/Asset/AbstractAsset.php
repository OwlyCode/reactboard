<?php

namespace OwlyCode\ReactBoard\Asset;

abstract class AbstractAsset
{
    protected $rootPath;
    protected $path;
    protected $type;

    public function __construct($rootPath, $path, $type = null)
    {
        $this->rootPath = $rootPath;
        $this->path = $path;
        $this->type = $type ? $type : $this->guessType($path);
    }

    public function getRootPath()
    {
        return $this->rootPath;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getType()
    {
        return $this->type;
    }

    protected function guessType($path)
    {
        $infos = pathinfo($path);

        return $infos['extension'] === 'js' ? AssetTypes::JAVASCRIPT : AssetTypes::CSS;
    }
}
