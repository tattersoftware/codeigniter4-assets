<?php namespace ModuleTests\Support;

use CodeIgniter\Config\Services;
use CodeIgniter\Router\RouteCollection;
use CodeIgniter\Router\Router;

class AssetsTestCase extends \CodeIgniter\Test\CIUnitTestCase
{	
	/**
	 * @var \CodeIgniter\Router\RouteCollection $collection
	 */
	protected $collection;
	
	/**
	 * @var \CodeIgniter\Router\Router
	 */
	protected $router;
	
    public function setUp(): void
    {
        parent::setUp();

		$this->collection = Services::routes();
		$routes = [
			'factories'            => 'Factories::index',
			'factories/update'     => 'Factories::update',
			'factories/(:segment)' => 'Factories::show/$1',
			'widgets'              => '\Module\Controllers\WidgetController::index',
			'widgets/(:segment)'   => '\Module\Controllers\WidgetController::details/$1',
		];
		$this->collection->map($routes);

		$request = Services::request();
		$request->setMethod('get');
		
		$this->router = new Router($this->collection, $request);
	}
}
