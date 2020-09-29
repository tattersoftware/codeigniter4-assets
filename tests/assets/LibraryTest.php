<?php

use CodeIgniter\Config\Services;
use Tatter\Assets\Libraries\Assets;
use Tests\Support\AssetsTestCase;

class LibraryTest extends AssetsTestCase
{
	public function testTagAddsTimestamps()
	{
		$file   = 'alert.js';
		$result = $this->assets->tag($file);
		$suffix = '?v=' . filemtime($this->config->fileBase . $file);

		$this->assertEquals('<script src="http://example.com/assets/alert.js' . $suffix . '" type="text/javascript"></script>', $result);
	}

	public function testTagRespectsConfigUseTimestamps()
	{
		$this->config->useTimestamps = false;

		$file   = 'alert.js';
		$assets = new Assets($this->config);
		$result = $assets->tag($file);

		$this->assertEquals('<script src="http://example.com/assets/alert.js" type="text/javascript"></script>', $result);
	}
}
