<?php

namespace Tatter\Assets\Test;

use CodeIgniter\Publisher\Publisher;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Tatter\Assets\Asset;
use Tatter\Assets\Config\Assets as AssetsConfig;

/**
 * Asset Test Trait
 *
 * Trait to set up a VFS instance for testing
 * Assets, Bundles, and Publishers.
 */
trait AssetsTestTrait
{
    /**
     * Virtual workspace
     *
     * @var vfsStreamDirectory|null
     */
    protected $root;

    /**
     * @var AssetsConfig
     */
    protected $config;

    /**
     * Whether the publishers have been run.
     *
     * @var bool
     */
    private $published = false;

    /**
     * Creates the VFS (if necessary) and updates the Assets config.
     */
    protected function setUpAssetsTestTrait(): void
    {
        if ($this->root === null) {
            $this->root = vfsStream::setup('root');
        }

        // Create the config
        $this->config                = new AssetsConfig();
        $this->config->directory     = $this->root->url() . DIRECTORY_SEPARATOR;
        $this->config->useTimestamps = false; // These make testing much harder

        Asset::useConfig($this->config);

        // Add VFS as an allowed Publisher directory
        config('Publisher')->restrictions[$this->config->directory] = '*';
    }

    /**
     * Resets the VFS if $refreshVfs is truthy.
     */
    protected function tearDownAssetsTestTrait(): void
    {
        if (! empty($this->refreshVfs)) {
            $this->root      = null;
            $this->published = false;
        }

        Asset::useConfig(null);
    }

    /**
     * Publishes all files once so they are
     * available for bundles.
     */
    protected function publishAll(): void
    {
        if ($this->published) {
            return;
        }

        foreach (Publisher::discover() as $publisher) {
            $publisher->publish();
        }

        $this->published = true;
    }
}
