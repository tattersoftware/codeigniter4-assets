<?php

use CodeIgniter\Config\Services;
use Tests\Support\AssetsTestCase;

class RouteTest extends AssetsTestCase
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
