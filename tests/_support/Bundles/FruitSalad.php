<?php namespace Tests\Support\Bundles;

use Tatter\Assets\Bundle;

class FruitSalad extends Bundle
{
	/**
	 * Paths to include in this Bundle.
	 *
	 * @var string[]
	 */
	protected $paths = [
		'apple.css',
		'banana.js',
	];
}
