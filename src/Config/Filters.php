<?php namespace Tatter\Assets\Config;

use Config\Filters;
use Tatter\Assets\Filters\AssetsFilter;

/**
 * @var Filters $filters
 */
$filters->aliases['assets'] = AssetsFilter::class;
