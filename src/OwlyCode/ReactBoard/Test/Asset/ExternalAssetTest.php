<?php

namespace OwlyCode\ReactBoard\Test\Asset;

use OwlyCode\ReactBoard\Asset\ExternalAsset;
use OwlyCode\ReactBoard\Asset\AssetTypes;

class ExternalAssetTest extends \PHPUnit_Framework_TestCase
{
    public function testInstanciation()
    {
        $asset = new ExternalAsset('foo/bar', 'baz.js');

        $this->assertSame($asset->getFullPath(), 'foo/bar/baz.js');
        $this->assertSame($asset->getWebPath(), 'global/baz.js');
        $this->assertSame($asset->getType(), AssetTypes::JAVASCRIPT);

        $asset = new ExternalAsset('foo/bar', 'baz.css');
        $this->assertSame($asset->getType(), AssetTypes::CSS);

        $asset = new ExternalAsset('foo/bar', 'baz.css', AssetTypes::JAVASCRIPT);
        $this->assertSame($asset->getType(), AssetTypes::JAVASCRIPT);
    }
}
