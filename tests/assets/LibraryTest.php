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

		$this->assertEquals('<script src="http://example.com/assets/alert.js?v=1594481279" type="text/javascript"></script>', $result);
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
