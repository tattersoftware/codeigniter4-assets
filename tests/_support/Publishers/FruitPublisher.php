<?php

namespace Tests\Support\Publishers;

use CodeIgniter\Publisher\Publisher;
use Tatter\Assets\Asset;

class FruitPublisher extends Publisher
{
    protected $source = SUPPORTPATH . 'Files/external';

    /**
     * Set the real destination.
     */
    public function __construct(?string $source = null, ?string $destination = null)
    {
        $config = Asset::config();

        $this->destination = $config->directory . $config->vendor . 'fruit';

        if (! is_dir($this->destination)) {
            mkdir($this->destination, 0775, true);
        }

        parent::__construct($source, $destination);
    }
}
