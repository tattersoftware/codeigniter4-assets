<?php

use Tatter\Assets\Test\BundlesTestCase;
use Tests\Support\Bundles\FruitSalad;

/**
 * @internal
 */
final class BundlesTestCaseTest extends BundlesTestCase
{
    /**
     * Mocks publishing the bundle content.
     */
    protected function setUp(): void
    {
        parent::setUp();

        copy(SUPPORTPATH . 'Files/apple.css', $this->config->directory . 'apple.css');
        copy(SUPPORTPATH . 'Files/banana.js', $this->config->directory . 'banana.js');
    }

    public function bundleProvider(): array
    {
        return [
            [
                FruitSalad::class,
                [
                    'apple.css',
                ],
                [
                    'banana.js',
                ],
            ],
        ];
    }
}
