<?php namespace Tatter\Assets\Interfaces;

use CodeIgniter\Config\BaseConfig;

interface AssetHandlerInterface
{
	public function __construct(BaseConfig $config = null);
	
	public function gather(string $route): array;
}
