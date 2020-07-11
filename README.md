# Tatter\Assets

Asset publishing and loading for CodeIgniter 4

[![](https://github.com/tattersoftware/codeigniter4-assets/workflows/PHPUnit/badge.svg)](https://github.com/tattersoftware/codeigniter4-assets/actions?query=workflow%3A%22PHPUnit)

## Quick Start

1. Install with Composer: `> composer require tatter/assets`
2. Put CSS & JS files in: **public/assets**
3. Add additional assets to config: **app/Config/Assets.php**
3. Add in head tag: `<?= service('assets')->css() ?>`
4. Add to footer: `<?= service('assets')->js() ?>`

## Features

Provides out-of-the-box asset loading for CSS and JavaScript files for CodeIgniter 4

## Installation

Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities
and always be up-to-date:
* `> composer require tatter/assets`

Or, install manually by downloading the source files and adding the directory to
`app/Config/Autoload.php`.

## Configuration (optional)

The library's default behavior can be overridden or augment by its config file. Copy
**examples/Assets.php** to **app/Config/Assets.php** and follow the instructions in the
comments. If no config file is found the library will use its defaults.

## Usage

If installed correctly CodeIgniter 4 will detect and autoload the library, service, and
config. Use the library methods `css()` and `js()` to display tags for the route-specific assets:
`<?= service('assets')->css() ?>`

## Structure

The library searches the assets directory (default: **public/assets**) for files matching
the current route, loading them in a cascading fashion for each route segment.

**Example:** https://example.com/users/view/12

The library will first load any root assets (`public/assets/*.css *.js`), then assets in
the "users" subfolder (`public/assets/users/`), then "view" subfolder, then "12" subfolder.
Any missing or invalid folders are ignored.

Additional assets may be specified from the config variable `$routes` - this is particularly
helpful for including pre-bundled libraries. `$routes` maps each route to an asset file or
a directory of assets to load for that route.

**Example:**

```
public $routes = [
	'' => [
		'bootstrap/dist/css/bootstrap.min.css',
		'bootstrap/dist/js/bootstrap.bundle.min.js'
	],
	'files/upload' => [
		'vendor/dropzone'
	]
];
```

This tells the library to load the Bootstrap assets for every route (`''`) without having
to move it from its pre-bundled subdirectory. It also will load any assets in the `dropzone`
directory for the specified route.

## Publishing

**Assets** can publish resources for you. This is particularly helpful if you need files
from a vendor package but don't want to host the package in your **public/** folder.
The **Manifests** library uses manifest files to locate and copy matching assets into your
assets folder (defined by `$config->fileBase`). This library includes a convenience command
to assist with asset publication:

	php spark assets:publish

By default `assets:publish` will scan all namespaces for JSON files in **{namespaceRoot}/Manifests**
and (assuming they are valid) will publish the assets defined there. Behavior is
customizable using **Config/Assets.php** but the default is to copy assets into
**public/assets/vendor/** into the subdirectory defined in the manifest.

If you are using version control it is recommended to exclude your asset publish directory,
for example by adding **public/assets/vendor/** to your **.gitignore** file.

### Manifests

Manifests are JSON files with at least the following three properties:
* `source` - The directory (relative to `$config->publishBase`) of the assets
* `destination` - The directory (relative to `$config->fileBase`) for the assets
* `resources` - The list of resources to publish, each with at least its own `source`.

See [manifests/](manifests/) for some example manifest files compatible with their Composer
sources.

## Example

You want to make a simple web app for browsing and uploading files, based on Bootstrap's
frontend. Start your CodeIgniter 4 project, then add Bootstrap and DropzoneJS to handle
the uploads:

	composer require twbs/bootstrap enyo/dropzone

Add this module as well:

	composer require tatter/assets

Create manifests and the config file in your project:
```
mkdir app/Manifests
cp vendor/tatter/assets/manifests/Dropzone.json app/Manifests/
cp vendor/tatter/assets/manifests/Bootstrap.json app/Manifests/
cp vendor/tatter/assets/examples/Assets.php app/Config/
```

Edit your config file so Bootstrap will always load, and DropzoneJS will load on certain routes:

```
public $routes = [
	'' => [
		'vendor/bootstrap/bootstrap.min.css',
		'vendor/bootstrap/bootstrap.bundle.min.js',
	],
	'files' => [
		'vendor/dropzone/',
	],
];
```

Run the publish command to inject the assets into **public/vendor/**:

	php spark assets:publish

Finally, add the service methods to the header and footer of your view template so the CSS
and JS tags are loaded automatically:
```
<head>
	<?= service('assets')->css() ?>
</head>
<body>

	...
	
	<?= service('assets')->js() ?>
</body>
```
