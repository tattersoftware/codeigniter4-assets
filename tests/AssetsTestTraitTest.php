<?php

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Assets\Test\AssetsTestTrait;

/**
 * @internal
 */
final class AssetsTestTraitTest extends CIUnitTestCase
{
    use AssetsTestTrait;

    protected $refreshVfs = false;

    public function testPublishesOnce()
    {
        $file = $this->config->directory . $this->config->vendor . 'fruit/third_party.js';

        $this->publishAll();
        $this->assertFileExists($file);

        unlink($file);

        $this->publishAll();
        $this->assertFileDoesNotExist($file);
    }

    public function testTearDownRefreshes()
    {
        $this->assertNotNull($this->root);

        $this->refreshVfs = true;
        $this->tearDownAssetsTestTrait();
        $this->assertNull($this->root);

        $this->refreshVfs = false;
    }
}
