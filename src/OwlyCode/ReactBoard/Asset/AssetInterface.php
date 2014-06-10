<?php

namespace OwlyCode\ReactBoard\Asset;

interface AssetInterface
{
    public function getFullPath();

    public function getWebPath();

    public function getType();
}
