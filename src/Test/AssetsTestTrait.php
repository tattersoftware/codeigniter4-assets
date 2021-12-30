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
     * The virtual workspace.
     *
     * @var vfsStreamDirectory|null
     */
    protected static $root;

    /**
     * Whether the publishers have been run.
     *
     * @var bool
     */
    private static $published = false;

    /**
     * @var AssetsConfig
     */
    protected $assets;

    /**
     * Creates the VFS (if necessary) and updates the Assets config.
     */
    protected function setUpAssetsTestTrait(): void
    {
        if (self::$root === null) {
            self::$root = vfsStream::setup('root');
        }

        // Create the config
        $this->assets                = new AssetsConfig();
        $this->assets->directory     = self::$root->url() . DIRECTORY_SEPARATOR;
        $this->assets->useTimestamps = false; // These make testing much harder

        Asset::useConfig($this->assets);

        // Add VFS as an allowed Publisher directory
        config('Publisher')->restrictions[$this->assets->directory] = '*';
    }

    /**
     * Resets the VFS if $refreshVfs is truthy.
     */
    protected function tearDownAssetsTestTrait(): void
    {
        if (! empty($this->refreshVfs)) {
            self::$root      = null;
            self::$published = false;
        }

        Asset::useConfig(null);
    }

    /**
     * Publishes all files once so they are
     * available for bundles.
     */
    protected function publishAll(): void
    {
        if (self::$published) {
            return;
        }

        foreach (Publisher::discover() as $publisher) {
            $publisher->publish();
        }

        self::$published = true;
    }
}
