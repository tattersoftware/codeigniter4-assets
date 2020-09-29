<?php namespace Tatter\Assets\Interfaces;

use Tatter\Assets\Config\Assets as AssetsConfig;

interface AssetHandlerInterface
{
	public function __construct(AssetsConfig $config = null);
	
	public function gather(string $route): array;
}
