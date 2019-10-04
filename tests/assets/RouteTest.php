<?php

use CodeIgniter\Config\Services;

class RouteTest extends \ModuleTests\Support\AssetsTestCase
{
	public function testGetRouteDefault()
	{
		$route = $this->assets->getRoute();

		$this->assertEquals('home/index', $route);
	}

	public function testSanitizeValidWithDefault()
	{
		$route     = 'workers/index';
		$expected  = 'workers/index';

		$sanitized = $this->assets->setRoute($route)->getRoute();

		$this->assertEquals($expected, $sanitized);
	}

	public function testSanitizeValidWithoutDefault()
	{
		$route     = 'workers/';
		$expected  = 'workers/index';
		
		$sanitized = $this->assets->setRoute($route)->getRoute();

		$this->assertEquals($expected, $sanitized);
	}
}
