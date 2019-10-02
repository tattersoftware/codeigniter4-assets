<?php

use CodeIgniter\Config\Services;

class RoutedTest extends \ModuleTests\Support\AssetsTestCase
{
	public function setUp(): void
	{
		parent::setUp();
	}

	public function testBasicRoute()
	{
		$this->router->handle('factories/update');
			
		$assets = Services::assets();
		
		dd($assets->display('js'));
		
		$this->assertEquals($this->collection->getDefaultController(), $router->controllerName());
		$this->assertEquals($this->collection->getDefaultMethod(), $router->methodName());
	}
/*
	public function testDefaultRoute()
	{
		$this->router->handle('');
			
		$assets = Services::assets();
		
		dd($assets->display('js'));
		
		$this->assertEquals($this->collection->getDefaultController(), $router->controllerName());
		$this->assertEquals($this->collection->getDefaultMethod(), $router->methodName());
	}
*/
}
