# Tatter\Assets

Asset handling for CodeIgniter 4

[![](https://github.com/tattersoftware/codeigniter4-assets/workflows/PHPUnit/badge.svg)](https://github.com/tattersoftware/codeigniter4-assets/actions/workflows/test.yml)
[![](https://github.com/tattersoftware/codeigniter4-assets/workflows/PHPStan/badge.svg)](https://github.com/tattersoftware/codeigniter4-assets/actions/workflows/analyze.yml)
[![](https://github.com/tattersoftware/codeigniter4-assets/workflows/Deptrac/badge.svg)](https://github.com/tattersoftware/codeigniter4-assets/actions/workflows/inspect.yml)
[![Coverage Status](https://coveralls.io/repos/github/tattersoftware/codeigniter4-assets/badge.svg?branch=develop)](https://coveralls.io/github/tattersoftware/codeigniter4-assets?branch=develop)

## Quick Start

1. Install with Composer: `> composer require tatter/assets`
2. Enable the `assets` filter in **app/Config/Filters.php**
3. Assign `$routes` to their assets in **app/Config/Assets.php**

## Features

Provides automated asset loading for CSS and JavaScript files for CodeIgniter 4.

## Installation

Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities
and always be up-to-date:
* `> composer require tatter/assets`

Or, install manually by downloading the source files and adding the directory to
`app/Config/Autoload.php`.

## Configuration

The library's default behavior can be overridden or augmented by its config file. Copy
**examples/Assets.php** to **app/Config/Assets.php** and follow the instructions in the
comments. If no config file is found the library will use its default.

In order to use the `AssetsFilter` you must add apply it to your target routes. The filter
does its own route matching so it is safe to apply it globally in **app/Config/Filters.php**.
See [Controller Filters](https://codeigniter.com/user_guide/incoming/filters.html) for more
info, or the **Example** section below.

## Usage

If installed correctly CodeIgniter 4 will detect and autoload the library, config, and filter.

### Asset

You may use the `Asset` class to build a tag for a single asset file:
```php
<?php

use Tatter\Assets\Asset;

$asset = new Asset('<link href="/assets/styles.css" rel="stylesheet" type="text/css" />');
echo view('main', ['assset' => $asset]);
```
... then in your view file:

```php
<html>
<head>
	<title>Hello World</title>
	<?= $asset ?>
</head>
<body>
	...
```

The `Asset` class also comes with some named constructors to help you create the tag strings:
* `createFromPath(string $path)` - Returns an `Asset` from a file relative to your config's `$directory`.
* `createFromUri(string $uri, string $type = null)` - Returns an `Asset` from a remote URL, with an optional type (`css`, `js`, `img`; `null` to detect).

Named constructors make the above example much easier:
```php
<html>
<head>
	<title>Hello World</title>
	<?= \Tatter\Assets\Asset::createFromPath('styles.css') ?>
</head>
<body>
	...
```

### Bundle

Typically a project will need more than one single asset. The `Bundle` class allows you to collect
multiple `Asset`s into a single instance. Use the `head()` and `body()` methods to return the `Asset`s
destined for each tag, formatted as blocks of tags.

`Bundle`s can be created one of two ways.

#### Class Properties

Create your own `Bundle` class and use these properties to stage the assets you want it to have:
	* `$bundles`: Names of other `Bundle` classes to merge with.
	* `$paths`: Relative file paths to make into `Asset`s.
	* `$uris`: URLs to make into `Asset`s.
	* `$strings`: Direct strings to pass into an `Asset`.

Example:
```php
<?php namespace App\Bundles;

use Tatter\Assets\Bundle;

class FrontendBundle extends Bundle
{
    protected $bundles = [
        StylesBundle::class,
    ];

    protected $paths = [
        'bootstrap/dist/css/bootstrap.min.css',
        'bootstrap/dist/js/bootstrap.bundle.min.js',
    ];

    protected $uris = [
        'https://pagecdn.io/lib/cleave/1.6.0/cleave.min.js',
    ];
}
```

#### define()

`Bundle` also comes with an initialization method: `define()`. Supply your own version of this
method along with the fluent-style definition methods to create more complicated collections.

Example:
```php
<?php namespace App\Bundles;

use Tatter\Assets\Asset;
use Tatter\Assets\Bundle;

class ColorBundle extends Bundle
{
    protected function define()
    {
        $this
            ->add(Asset::createFromPath('styles.css')) // Add individual Assets
            ->merge($someOtherBundle); // Or combine multiple Bundles

        // Create more complex Assets
        $source = '<script src="https://pagecdn.io/lib/cleave/1.6.0/cleave.min.js" type="text/javascript"></script>';
        $inHead = true; // Force a JavaScript Asset to the <head> tag
        $asset  = new Asset($source, $inHead);
    }
}
```

### Filter

If you configured the `AssetsFilter` (see above) to load for your routes, you must also associate
the specific assets or bundles per route. Use the config ``$routes`` property, where the route
pattern is the key and the values are arrays of file paths, URLs, or bundle class names. E.g.:

```php
<?php namespace Config;

use Tatter\Assets\Config\Assets as AssetsConfig;

class Assets extends AssetsConfig
{
    public $routes = [
        '*' => [
            'bootstrap/bootstrap.min.css',
            'bootstrap/bootstrap.bundle.min.js',
            'font-awesome/css/all.min.css',
            'styles/main.css',
        ],
        'files' => [
            'dropzone/dropzone.min.css',
            'dropzone/dropzone.min.js',
        ],
    ];
}
```

## Example

You want to make a simple web app for browsing and uploading files, based on Bootstrap's
frontend. Start your CodeIgniter 4 project, then add Bootstrap and DropzoneJS to handle
the uploads:

	composer require twbs/bootstrap enyo/dropzone

> Note: You will need to copy files from **vendor** to **public/assets/** to make them
	accessible, or use the framework's `Publisher` class to handle this for you.

Add this module as well:

	composer require tatter/assets

Edit your **Filters.php** config file to enable the `AssetsFilter` on all routes:

```php
    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array
     */
    public $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
        ],
        'after'  => [
            'assets' => ['except' => 'api/*'],
        ],
    ];
```

Create a new `Bundle` to define your Bootstrap files in **app/Bundles/DropzoneJS.php**:

```php
<?php namespace App\Bundles;

use Tatter\Assets\Bundle;

class DropzoneJS extends Bundle
{
    protected $paths = [
        'dropzone/dropzone.min.css',
        'dropzone/dropzone.min.js',
    ];
}
```

Then copy **examples/Assets.php** from this repo to **app/Config/** and edit it so Bootstrap
will load on every route and DropzoneJS will load on specific routes:

```php
public $routes = [
    '*' => [
        'bootstrap/dist/css/bootstrap.min.css',
        'bootstrap/dist/js/bootstrap.bundle.min.js',
    ],
    'files/*' => [
        \App\Bundles\DropzoneJS::class,
    ],
    'upload' => [
        \App\Bundles\DropzoneJS::class,
    ],
];
```

> Note: We could have made a `Bundle` for Bootstrap as well but since they are only needed for one route this is just as easy.

If you finished all that then your assets should be injected into your `<head>` and `<body>` tags accordingly.

Your view file:
```html
<html>
<head>
	<title>File Upload</title>
</head>
<body>
	<h1>Hello</h1>
	<p>Put your upload form here.</p>
</body>
</html>
```

... served as:
```html
<html>
<head>
	<title>File Upload</title>

<link href="http://example.com/assets/bootstrap/dist/css/bootstrap.min.css?v=1234151511412" rel="stylesheet" type="text/css" />
<link href="http://example.com/assets/dropzone/dropzone.min.css?v=12341515141241" rel="stylesheet" type="text/css" />
</head>
<body>
	<h1>Hello</h1>
	<p>Put your upload form here.</p>

<script src="http://example.com/assets/bootstrap/dist/js/bootstrap.bundle.min.js?v=12341515735743" type="text/javascript"></script>
<script src="http://example.com/assets/dropzone/dropzone.min.js?v=12341515573424" type="text/javascript"></script>
</body>
</html>
```

## Vendor Classes

This library includes two abstract class stubs to ease working with third-party assets.
`VendorPublisher` is a wrapper for the framework's [Publisher Library](https://codeigniter.com/user_guide/libraries/publisher.html)
primed for use with `Assets`, and `VendorBundle` is a specialized version of this library's
`Bundle` primed to handle assets published via `VendorPublisher`. Together these two classes
can take a lot of the work out of managing assets you include from external sources.

Let's revisit the example above... Instead of copies the files into **public/assets/** ourselves
(and re-copying every time there is an update) let's create a `VendorPublisher` to do that
for us. In **app/Publishers/BootstrapPublisher.php**:
```php
<?php

namespace App\Publishers;

use Tatter\Assets\VendorPublisher;

class BootstrapPublisher extends VendorPublisher
{
    protected $source = 'vendor/twbs/bootstrap/dist';
    protected $path   = 'bootstrap';
}

```

That's all! `VendorPublisher` knows that `$path` is relative the to directory in your Assets
config file, so when you run `php spark publish` next all the latest Bootstrap assets will
be copied into that directory (default: **public/assets/vendor/**).

> Note: Since these are external dependencies be sure to exclude them from your repo with your **.gitignore** file.

Now lets use these assets. We can create a new `VendorBundle` and use the new `addPath()`
method to access the same files we just published from Composer's vendor directory.
In **app/Bundles/BootstrapBundle.php**:
```php
<?php

namespace App\Bundles;

use Tatter\Assets\VendorBundle;

class BootstrapBundle extends VendorBundle
{
    protected function define(): void
    {
        $this
            ->addPath('bootstrap/bootstrap.min.css')
            ->addPath('bootstrap/bootstrap.bundle.min.js');
    }
}
```

Now add the new bundle to our **app/Config/Assets.php** routes:
```php
public $routes = [
    '*' => [\App\Bundles\BootstrapBundle::class],
];
```

And we have hands-free Bootstrap updates from now on!
