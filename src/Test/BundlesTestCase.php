<?php

namespace Tatter\Assets\Test;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Assets\Bundle;

abstract class BundlesTestCase extends CIUnitTestCase
{
    use AssetsTestTrait;

    /**
     * Publishes all files once so they are
     * available for bundles.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->publishAll();
    }

    /**
     * @dataProvider bundleProvider
     *
     * @param class-string<Bundle> $class
     * @param string[]             $expectedHeadFiles
     * @param string[]             $expectedBodyFiles
     */
    public function testBundlesFiles(string $class, array $expectedHeadFiles, array $expectedBodyFiles): void
    {
        $bundle = new $class();
        $head   = $bundle->head();
        $body   = $bundle->body();

        foreach ($expectedHeadFiles as $file) {
            $this->assertStringContainsString($file, $head);
        }

        foreach ($expectedBodyFiles as $file) {
            $this->assertStringContainsString($file, $body);
        }
    }

    /**
     * Returns an array of items to test with each item
     * as a triple of [string bundleClassName, string[] headFileNames, string[] bodyFileNames]
     */
    abstract public function bundleProvider(): array;
}
