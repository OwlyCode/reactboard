<?php

namespace OwlyCode\ReactBoard\Asset;

class ExternalAsset extends AbstractAsset implements AssetInterface
{
    public function getFullPath()
    {
        return $this->rootPath . DIRECTORY_SEPARATOR . $this->path;
    }

    public function getWebPath()
    {
        return 'global' . DIRECTORY_SEPARATOR . $this->path;
    }
}
