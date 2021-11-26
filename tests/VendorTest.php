<?php

use Tatter\Assets\VendorBundle;
use Tatter\Assets\VendorPublisher;
use Tests\Support\AssetsTestCase;

/**
 * @internal
 */
final class VendorTest extends AssetsTestCase
{
    public function testPublisherThrowsWithoutPath()
    {
        $this->expectException('DomainException');
        $this->expectExceptionMessage('Invalid relative destination $path');

        $publisher = new class () extends VendorPublisher {
        };
    }

    public function testPublisherSetsDestination()
    {
        // Allow publishing to the test folder
        config('Publisher')->restrictions[SUPPORTPATH] = '*';

        $publisher          = new class () extends VendorPublisher {
            protected $path = 'foobar';
        };

        $this->assertSame(SUPPORTPATH . 'Files/external/foobar/', $publisher->getDestination());
    }

    public function testBundleAddsPath()
    {
        $bundle = new class () extends VendorBundle {};

        $bundle->addPath('third_party.js');

        $this->assertSame('<script src="' . base_url('assets/external/third_party.js') . '" type="text/javascript"></script>', $bundle->body());
    }
}
