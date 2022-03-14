<?php

namespace Config;

/*
*
* This file contains example values to override or augment default library behavior.
* Recommended usage:
*	1. Copy the file to app/Config/Assets.php
*	2. Set any override variables
*	3. Add additional route-specific assets to $routes
*	4. Remove any lines to fallback to defaults
*
*/

use Tatter\Assets\Config\Assets as AssetsConfig;

class Assets extends AssetsConfig
{
    //--------------------------------------------------------------------
    // Library Behavior
    //--------------------------------------------------------------------

    /**
     * Asset URI base, relative to baseURL.
     */
    public string $uri = 'assets/';

    /**
     * Asset storage location in the filesystem.
     * Must be somewhere web accessible.
     */
    public string $directory = FCPATH . 'assets/';

    /**
     * Path for third-party published Assets. The path is relative to
     * both $directory and $uri. Recommended to add the resulting file
     * path to .gitignore so published vendor assets will not be tracked.
     */
    public string $vendor = 'vendor/';

    /**
     * Whether to append file modification timestamps on asset tags.
     * Makes it less likely for modified assets to remain cached.
     */
    public bool $useTimestamps = true;

    /**
     * Whether to cache bundles for faster route responses.
     */
    public bool $useCache = ENVIRONMENT === 'production';

    //--------------------------------------------------------------------
    // Route Assets
    //--------------------------------------------------------------------

    /**
     * Assets to apply to each route. Routes may use * as a wildcard to
     * allow any valid character, similar to URL Helper's url_is().
     * Keys are routes; values are an array of any of the following:
     *   - Bundle class names
     *   - File paths (relative to $directory)
     *   - URLs
     *
     * Example:
     *     $routes = [
     *         '*' => [
     *             'https://pagecdn.io/lib/cleave/1.6.0/cleave.min.js',
     *             \App\Bundles\Bootstrap::class,
     *          ],
     *         'admin/*' => [
     *             \Tatter\Frontend\Bundles\AdminLTE::class,
     *             'admin/login.js',
     *         ],
     *     ];
     *
     * @var array<string,string[]>
     */
    public array $routes = [];
}
