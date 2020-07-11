<?php

use CodeIgniter\Files\Exceptions\FileException;
use CodeIgniter\Files\Exceptions\FileNotFoundException;
use Tatter\Assets\Exceptions\ManifestsException;
use Tests\Support\ManifestsTestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;

class PublishTest extends ManifestsTestCase
{
	public function testExplicitFiles()
	{
		$result = $this->manifests->publish($this->testManifest);
		$this->assertTrue($result);
		
		$expected = [
			'root' => [
				'assets' => [
					'index.html' => $this->manifests->getIndexHtml(),
					'vendor'     => [
						'index.html' => $this->manifests->getIndexHtml(),
						'widgets'    => [
							'index.html' => $this->manifests->getIndexHtml(),
							'css'        => [
								'index.html' => $this->manifests->getIndexHtml(),
								'widget_style.css' => "a {\n\tcolor: white;\n}\n",
							],
							'notAsset.json' => "{\"flower\": \"yes\"}",
						],
					],
				],
			],
		];
		
		$visitor = new vfsStreamStructureVisitor();
		$result  = vfsStream::inspect($visitor, $this->root)->getStructure();

		$this->assertEquals($expected, $result);
	}
	
	public function testRecursiveFlatten()
	{
		$path     = SUPPORTPATH . 'Manifests/frontend.json';
		$manifest = $this->manifests->parse($path);
		$result   = $this->manifests->publish($manifest);

		$this->assertTrue($result);

		$visitor = new vfsStreamStructureVisitor();
		$result  = vfsStream::inspect($visitor, $this->root)->getStructure();
		
		$this->assertTrue($this->root->hasChild('assets/vendor/frontend/frontend.css'));
		$this->assertTrue($this->root->hasChild('assets/vendor/frontend/frontend.js'));
		$this->assertTrue($this->root->hasChild('assets/vendor/frontend/frontend.min.css'));
		$this->assertTrue($this->root->hasChild('assets/vendor/frontend/frontend.min.js'));
	}
	
	public function testRespectsFilters()
	{
		$path     = SUPPORTPATH . 'Manifests/LawyerPack.json';
		$manifest = $this->manifests->parse($path);
		$result   = $this->manifests->publish($manifest);

		$this->assertTrue($result);
		
		$this->assertTrue($this->root->hasChild('assets/vendor/lawyers/file1.css'));
		$this->assertTrue($this->root->hasChild('assets/vendor/lawyers/file2.css'));
		$this->assertTrue($this->root->hasChild('assets/vendor/lawyers/file3.css'));
		$this->assertTrue($this->root->hasChild('assets/vendor/lawyers/image.png'));
		
		$this->assertFalse($this->root->hasChild('assets/vendor/lawyers/ignore.txt'));
	}
	
	public function testNothingReturnsTrue()
	{
		unset($this->testManifest->resources);
		$this->testManifest->resources = [
			0 => (object)[
				'source' => '.',
				'filter' => '#noMatch#',
			]
		];
		
		$result = $this->manifests->publish($this->testManifest);
		$this->assertTrue($result);
		
		$this->assertTrue($this->root->hasChild('assets/vendor/widgets/index.html'));
		$this->assertFalse($this->root->hasChild('assets/vendor/widgets/widget_style.css'));
	}
}
