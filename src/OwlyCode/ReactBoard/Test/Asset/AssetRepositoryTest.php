<?php

namespace OwlyCode\ReactBoard\Asset;

use OwlyCode\ReactBoard\Asset\AssetRepository;
use OwlyCode\ReactBoard\Asset\ExternalAsset;

class AssetRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testRepository()
    {
        $repository = new AssetRepository();
        $asset = new ExternalAsset('foo', 'bar.js');

        $repository->add($asset);
        $this->assertSame($asset, $repository->get('global/bar.js'));
        $this->assertSame(array('global/bar.js' => $asset), $repository->getAll());
    }
}
