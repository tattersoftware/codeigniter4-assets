<?php

namespace Tatter\Assets\Config;

use CodeIgniter\Config\BaseConfig;
use Tatter\Assets\Asset;
use Tatter\Assets\Bundle;

class Assets extends BaseConfig
{
    //--------------------------------------------------------------------
    // Library Behavior
    //--------------------------------------------------------------------

    /**
     * Asset URI base, relative to baseURL.
     *
     * @var string
     */
    public $uri = 'assets/';

    /**
     * Asset storage location in the filesystem.
     * Must be somewhere web accessible.
     *
     * @var string
     */
    public $directory = FCPATH . 'assets/';

    /**
     * Path for third-party published Assets. The path is relative to
     * both $directory and $uri. Recommended to add the resulting file
     * path to .gitignore so published vendor assets will not be tracked.
     *
     * @var string
     */
    public $vendor = 'vendor/';

    /**
     * Whether to append file modification timestamps on asset tags.
     * Makes it less likely for modified assets to remain cached.
     *
     * @var bool
     */
    public $useTimestamps = true;

    /**
     * Whether to cache bundles for faster route responses.
     *
     * @var bool
     */
    public $useCache = ENVIRONMENT === 'production';

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
    public $routes = [];

    /**
     * Gathers Assets and Bundles that match the relative URI path.
     * $uri may contain a wildcard (*) which will allow any valid character.
     * Based on URL Helper's "url_is()".
     *
     * @return string[]
     */
    final public function getForRoute(string $uri): array
    {
        $uri     = '/' . trim($uri, '/ ');
        $matched = [];

        foreach ($this->routes as $route => $items) {
            // Convert to a real regex
            $route = '/' . trim(str_replace('*', '(\S)*', $route), '/ ');

            if (preg_match("|^{$route}$|", $uri)) {
                $matched = array_merge($matched, $items);
            }
        }

        return array_values(array_unique($matched));
    }
}
