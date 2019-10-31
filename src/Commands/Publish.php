<?php namespace Tatter\Assets\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

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
		
		$count = 0;
		foreach ($manifests->locate() as $path)
		{
			if (! $manifest = $manifests->parse($path))
			{
				CLI::write('Unable to parse manifest from ' . $path);
				$this->displayMessages($manifests->getMessages());
				continue;
			}

			if (! $manifests->publish($manifest))
			{
				CLI::write('Unable to publish manifest from ' . $path);
				$this->displayMessages($manifests->getMessages());
				continue;
			}
			
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
