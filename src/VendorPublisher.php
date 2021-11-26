<?php

namespace Tatter\Assets;

use CodeIgniter\Publisher\Publisher;
use DomainException;

/**
 * Vendor Publisher Abstract Class
 *
 * A Publisher wrapper for Assets sourced from
 * third-party files to be published to the configured
 * "vendor" path (see Assets Config) and used in
 * conjunction with VendorBundle.
 */
abstract class VendorPublisher extends Publisher
{
    /**
     * Destination path relative to AssetsConfig::directory.
     * Must be set by child classes.
     *
     * @var string
     */
    protected $path;

    /**
     * Set the real destination.
     */
    public function __construct(?string $source = null, ?string $destination = null)
    {
        if (! is_string($this->path)) {
            throw new DomainException('Invalid relative destination $path.');
        }

        $this->destination = VendorBundle::base();

        if (! is_dir($this->destination)) {
            mkdir($this->destination, 0775, true);
        }

        parent::__construct($source, $destination);
    }
}
