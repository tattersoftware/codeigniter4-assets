<?php namespace Tatter\Assets\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

/**
 * @deprecated Use the framework's publish command
 */
class Publish extends BaseCommand
{
	protected $group       = 'Publication';
	protected $name        = 'assets:publish';
	protected $description = "Scans for manifest files and publishes matching assets.";
	protected $usage       = "assets:publish";

	public function run(array $params)
	{
		helper('inflector');
		$manifests = Services::manifests();
		$hashes = [];
		
		$count = 0;
		foreach ($manifests->locate() as $path)
		{
			if (! $manifest = $manifests->parse($path))
			{
				CLI::write('Unable to parse manifest from ' . $path);
				$this->displayMessages($manifests->getMessages());
				continue;
			}
			
			// Check for duplicates
			$hash = md5_file($path);
			if (isset($hashes[$hash]))
			{
				continue;
			}

			if (! $manifests->publish($manifest))
			{
				CLI::write('Unable to publish manifest from ' . $path);
				$this->displayMessages($manifests->getMessages());
				continue;
			}
			
			$hashes[$hash] = true;
			CLI::write('Published ' . basename($path));
			$count++;
		}
		
		if ($count == 0)
		{
			CLI::write('No manifests published.');
		}
		else
		{
			CLI::write(counted($count, 'manifests') . ' published.', 'green');
		}
	}
	
	protected function displayMessages(array $messages)
	{
		foreach ($messages as $message)
		{
			CLI::write(...$message);
		}
	}
}
