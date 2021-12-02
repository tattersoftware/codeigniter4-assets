<?php

namespace Tatter\Assets\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Assets\Bundle;
use Tatter\Assets\RouteBundle;

/**
 * Assets Filter
 *
 * Injects Asset tags for the current route into
 * the response body HTML.
 */
class AssetsFilter implements FilterInterface
{
    /**
     * @codeCoverageIgnore
     *
     * @param mixed|null $arguments
     */
    public function before(RequestInterface $request, $arguments = null)
    {
    }

    /**
     * Gathers the route-specific assets and adds their tags to the response.
     *
     * @param class-string<Bundle>[]|null $arguments Additional Bundle classes
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ?ResponseInterface
    {
        // Ignore irrelevent responses
        if ($response instanceof RedirectResponse || empty($response->getBody())) {
            return null;
        }

        // Check CLI separately for coverage
        if (is_cli() && ENVIRONMENT !== 'testing') {
            return null; // @codeCoverageIgnore
        }

        // Only run on HTML content
        if (strpos($response->getHeaderLine('Content-Type'), 'html') === false) {
            return null;
        }

        $bundle = RouteBundle::createFromRoute(ltrim($request->getPath(), '/ '));

        // Check for additional Bundles specified in the arguments
        if (! empty($arguments)) {
            foreach ($arguments as $class) {
                $bundle->merge(new $class());
            }
        }

        $headTags = $bundle->head();
        $bodyTags = $bundle->body();

        // Short circuit?
        if ($headTags === '' && $bodyTags === '') {
            return null;
        }

        $body = $response->getBody();

        // Add any head content right before the closing head tag
        if ($headTags) {
            $body = str_replace('</head>', $headTags . PHP_EOL . '</head>', $body);
        }
        // Add any body content right before the closing body tag
        if ($bodyTags) {
            $body = str_replace('</body>', $bodyTags . PHP_EOL . '</body>', $body);
        }

        // Use the new body and return the updated Response
        return $response->setBody($body);
    }
}
