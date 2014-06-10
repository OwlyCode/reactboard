<?php

namespace OwlyCode\ReactBoard\Test\Asset;

use OwlyCode\ReactBoard\Asset\Asset;
use OwlyCode\ReactBoard\Asset\AssetTypes;

class AssetTest extends \PHPUnit_Framework_TestCase
{
    public function testInstanciation()
    {
        $application = $this->getMockBuilder('OwlyCode\ReactBoard\Application\ApplicationInterface')->getMock();
        $application->expects($this->once())->method('getName')->will($this->returnValue('app'));
        $asset = new Asset($application, 'foo/bar', 'baz.js');

        $this->assertSame($asset->getFullPath(), 'foo/bar/baz.js');
        $this->assertSame($asset->getWebPath(), 'app/baz.js');
        $this->assertSame($asset->getType(), AssetTypes::JAVASCRIPT);

        $asset = new Asset($application, 'foo/bar', 'baz.css');
        $this->assertSame($asset->getType(), AssetTypes::CSS);

        $asset = new Asset($application, 'foo/bar', 'baz.css', AssetTypes::JAVASCRIPT);
        $this->assertSame($asset->getType(), AssetTypes::JAVASCRIPT);
    }
}
