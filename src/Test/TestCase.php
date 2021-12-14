<?php

namespace Tatter\Assets\Test;

use CodeIgniter\Test\CIUnitTestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Tatter\Assets\Asset;
use Tatter\Assets\Config\Assets as AssetsConfig;

abstract class TestCase extends CIUnitTestCase
{
    /**
     * Virtual workspace
     *
     * @var vfsStreamDirectory
     */
    protected $root;

    /**
     * @var AssetsConfig
     */
    protected $config;

    /**
     * Preps the config and VFS.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->root = vfsStream::setup('root');

        // Create the config
        $this->config                = config('Assets');
        $this->config->directory     = $this->root->url() . DIRECTORY_SEPARATOR;
        $this->config->useTimestamps = false; // These make testing much harder

        Asset::useConfig($this->config);

        // Add VFS as an allowed Publisher directory
        config('Publisher')->restrictions[$this->config->directory] = '*';
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->root = null; // @phpstan-ignore-line
        $this->resetServices();
    }
}
