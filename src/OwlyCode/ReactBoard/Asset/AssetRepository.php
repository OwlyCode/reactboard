<?php

namespace OwlyCode\ReactBoard\Asset;

use OwlyCode\ReactBoard\Exception\AssetNotFoundException;

class AssetRepository
{
    private $assets = array();

    public function add(AssetInterface $asset)
    {
        $this->assets[$asset->getWebPath()] = $asset;

        return $this;
    }

    public function get($webPath)
    {
        if (!array_key_exists($webPath, $this->assets)) {
            throw new AssetNotFoundException(sprintf('The requested "%s" asset was not found.', $webPath));
        }

        return $this->assets[$webPath];
    }

    public function getAll()
    {
        return $this->assets;
    }
}
