<?php

namespace Tests\Support;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Test\FilterTestTrait;
use Tatter\Assets\Filters\AssetsFilter;
use Tests\Support\Bundles\FruitSalad;
use Tests\Support\Bundles\LunchBreak;

/**
 * @internal
 */
final class FilterTest extends TestCase
{
    use FilterTestTrait;

    /**
     * @var string
     */
    private $body = <<<'EOD'
        <html>
        <head>
        	<title>Test</title>
        </head>
        <body>
        	<h1>Hello</h1>
        </body>
        </html>
        EOD;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assets->routes = [
            '*' => [
                'https://pagecdn.io/lib/cleave/1.6.0/cleave.min.js',
                FruitSalad::class,
            ],
            'admin/*' => [
                LunchBreak::class,
                'directory/machines.js',
            ],
        ];

        $this->response->setBody($this->body);
        $this->response->setHeader('Content-Type', 'text/html');
    }

    public function testFilter()
    {
        $expected = <<<'EOD'
            <html>
            <head>
            	<title>Test</title>
            <link href="http://example.com/assets/apple.css" rel="stylesheet" type="text/css" />
            </head>
            <body>
            	<h1>Hello</h1>
            <script src="https://pagecdn.io/lib/cleave/1.6.0/cleave.min.js" type="text/javascript"></script>
            <script src="http://example.com/assets/banana.js" type="text/javascript"></script>
            </body>
            </html>
            EOD;

        $this->request->setPath('foobar');

        $caller = $this->getFilterCaller(AssetsFilter::class, 'after');
        $result = $caller();

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($expected, $result->getBody());
    }

    public function testFilterWithArguments()
    {
        $expected = <<<'EOD'
            <html>
            <head>
            	<title>Test</title>
            <link href="http://example.com/assets/apple.css" rel="stylesheet" type="text/css" />
            <link href="https://water.com/glassof.css" rel="stylesheet" type="text/css" />
            </head>
            <body>
            	<h1>Hello</h1>
            <script src="https://pagecdn.io/lib/cleave/1.6.0/cleave.min.js" type="text/javascript"></script>
            <script src="http://example.com/assets/banana.js" type="text/javascript"></script>
            <script src="http://example.com/assets/directory/machines.js" type="text/javascript"></script>
            </body>
            </html>
            EOD;

        $this->request->setPath('foobar');

        $caller = $this->getFilterCaller(AssetsFilter::class, 'after');
        $result = $caller([LunchBreak::class]);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($expected, $result->getBody());
    }

    public function testEmptyTags()
    {
        $this->assets->routes = [];

        $caller = $this->getFilterCaller(AssetsFilter::class, 'after');

        $this->assertNull($caller());
    }

    public function testEmptyBody()
    {
        $this->response->setBody('');
        $caller = $this->getFilterCaller(AssetsFilter::class, 'after');

        $this->assertNull($caller());
    }

    public function testRedirect()
    {
        $this->response = redirect('');
        $caller         = $this->getFilterCaller(AssetsFilter::class, 'after');

        $this->assertNull($caller());
    }

    public function testWrongContentType()
    {
        $this->response->setHeader('Content-Type', 'application/json');
        $caller = $this->getFilterCaller(AssetsFilter::class, 'after');

        $this->assertNull($caller());
    }
}
