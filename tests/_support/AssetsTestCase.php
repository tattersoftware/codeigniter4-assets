<?php

namespace Tests\Support;

use Tatter\Assets\Test\TestCase;

abstract class AssetsTestCase extends TestCase
{
    /**
     * Preps the config for the test directory.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->config->directory = SUPPORTPATH . 'Files/';
        $this->config->vendor    = 'external/';
    }
}
