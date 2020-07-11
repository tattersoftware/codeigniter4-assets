<?php

use CodeIgniter\Files\Exceptions\FileException;
use CodeIgniter\Files\Exceptions\FileNotFoundException;
use Tatter\Assets\Exceptions\ManifestsException;
use Tatter\Assets\Libraries\Manifests;
use Tests\Support\ManifestsTestCase;
use org\bovigo\vfs\vfsStream;

class MethodsTest extends ManifestsTestCase
{	
	public function testLocate()
	{
		$expected = [
			SUPPORTPATH . 'Manifests/LawyerPack.json',
			SUPPORTPATH . 'Manifests/Widgets.json',
			SUPPORTPATH . 'Manifests/frontend.json',
		];
		$paths = $this->manifests->locate();

		$this->assertEquals($expected, $paths);
	}
	
	public function testParse()
	{
		$path = SUPPORTPATH . 'Manifests/Widgets.json';
		
		$manifest = $this->manifests->parse($path);

		$this->assertEquals($this->testManifest, $manifest);
	}
	
	public function testValidate()
	{
		$result = $this->manifests->validate($this->testManifest);

		$this->assertTrue($result);
	}
	
	public function testValidateFailsMissingField()
	{
		$this->expectException(ManifestsException::class);
		$this->expectExceptionMessage(lang('Manifests.missingField', ['destination']));
		
		unset($this->testManifest->destination);
		
		$result = $this->manifests->validate($this->testManifest);
	}
	
	public function testValidateFailsMissingResourceSource()
	{
		$this->expectException(ManifestsException::class);
		$this->expectExceptionMessage(lang('Manifests.missingField', ['resource->source']));
		
		$this->testManifest->resources[] = (object)['destination' => 'test/'];
		
		$result = $this->manifests->validate($this->testManifest);
	}
	
	public function testAddIndexToDirectory()
	{
		$method = $this->getPrivateMethodInvoker($this->manifests, 'addIndexToDirectory');
		
		$result = $method($this->root->url() . '/');

		$this->assertTrue($result);
		$this->assertTrue($this->root->hasChild('index.html'));
		
		$contents = file_get_contents($this->root->url() . '/index.html');
		
		$this->assertEquals($this->manifests->getIndexHtml(), $contents);
	}

	public function testEnsureDirectory()
	{
		$method = $this->getPrivateMethodInvoker($this->manifests, 'ensureDirectory');
		
		$result = $method($this->root->url() . '/test');
		$this->assertTrue($result);
		
		$this->assertTrue($this->root->hasChild('test'));
	}

	public function testEnsureDirectoryFailsOnFile()
	{
		$method = $this->getPrivateMethodInvoker($this->manifests, 'ensureDirectory');
		$path = $this->root->url() . '/test';
		
		file_put_contents($path, '');
		
		$this->expectException(ManifestsException::class);
		$this->expectExceptionMessage(lang('Manifests.cannotCreateDirectory', [$path]));
		
		$result = $method($this->root->url() . '/test');
	}

	public function testEnsureDirectoryFailsNotWritable()
	{
		$method = $this->getPrivateMethodInvoker($this->manifests, 'ensureDirectory');
		$path = $this->root->url() . '/test';
		
		mkdir($path);
		$this->root->getChild('test')->chmod(0500);
		
		$this->expectException(ManifestsException::class);
		$this->expectExceptionMessage(lang('Manifests.directoryNotWritable', [$path]));
		
		$result = $method($path);
	}

	public function testSecureDestination()
	{
		$method = $this->getPrivateMethodInvoker($this->manifests, 'secureDestination');

		$result = $method('vendor/widgets');
		$this->assertTrue($result);
		
		$this->assertTrue($this->root->hasChild('assets'));
		$this->assertTrue($this->root->hasChild('assets/index.html'));

		$this->assertTrue($this->root->hasChild('assets/vendor'));
		$this->assertTrue($this->root->hasChild('assets/vendor/index.html'));

		$this->assertTrue($this->root->hasChild('assets/vendor/widgets'));
		$this->assertTrue($this->root->hasChild('assets/vendor/widgets/index.html'));
	}

	public function testSecureDestinationAbsolutePath()
	{
		$method = $this->getPrivateMethodInvoker($this->manifests, 'secureDestination');

		$result = $method($this->root->url() . '/assets/vendor/widgets');
		$this->assertTrue($result);
		
		$this->assertTrue($this->root->hasChild('assets'));
		$this->assertTrue($this->root->hasChild('assets/index.html'));

		$this->assertTrue($this->root->hasChild('assets/vendor'));
		$this->assertTrue($this->root->hasChild('assets/vendor/index.html'));

		$this->assertTrue($this->root->hasChild('assets/vendor/widgets'));
		$this->assertTrue($this->root->hasChild('assets/vendor/widgets/index.html'));
	}

	public function testExpandPaths()
	{
		$resource = (object)[
			'source'      => 'minified/script.min.js',
			'destination' => 'min/',
		];
		
		$this->manifests->expandPaths($resource, $this->testManifest);
		
		$expected = $this->config->publishBase . 'vendor/WidgetModule/dist/minified/script.min.js';
		$this->assertEquals($expected, $resource->source);
		
		$expected = $this->config->fileBase . 'vendor/widgets/min/';
		$this->assertEquals($expected, $resource->destination);
	}
	
	public function testGetMessages()
	{
		// Get a new version the library that is silent
		$this->config->silent = true;
		$manifests = new Manifests($this->config);
		
		$expected = [
			[lang('Files.fileNotFound', ['missing.json']), 'red']
		];
				
		// Create an error
		$manifests->parse('missing.json');
		$messages = $manifests->getMessages();
		
		$this->assertEquals($expected, $messages);
	}
}
