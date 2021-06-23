<?php namespace Tests\Support\Bundles;

use Tatter\Assets\Bundle;

class LunchBreak extends Bundle
{
	/**
	 * Paths to include in this Bundle.
	 *
	 * @var string[]
	 */
	protected $paths = [
		'banana.js',
		'directory/machines.js',
	];

	/**
	 * URIs to include in this Bundle.
	 *
	 * @var string[]
	 */
	protected $uris = [
		'https://water.com/glassof.css',
	];
}
