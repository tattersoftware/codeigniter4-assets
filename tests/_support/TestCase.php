<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Assets\Asset;
use Tatter\Assets\Config\Assets as AssetsConfig;

abstract class TestCase extends CIUnitTestCase
{
    /**
     * @var AssetsConfig
     */
    protected $assets;

    /**
     * Preps the config for the test directory.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->assets                = config('Assets');
        $this->assets->directory     = SUPPORTPATH . 'Files/';
        $this->assets->vendor        = 'external/';
        $this->assets->useTimestamps = false; // These make testing much harder

        Asset::useConfig($this->assets);
    }
}
