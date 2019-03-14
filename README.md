# Tatter\Assets
Lightweight asset loader for CodeIgniter 4

## Quick Start

1. Run: `> composer require tatter/assets`
2. Put CSS & JS files in: public/assets
3. Add in head tag: `helper("tatter\assets"); css();`
4. Add to footer: `js();`

## Features

Provides out-of-the-box asset loading for CSS and JavaScript files

## Installation

Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities
and always be up-to-date:
`> composer require tatter/assets`

Or, install manually by downloading the source files and copying them into CodeIgniter 4's
app/ same subdirectories.

## Configuration (optional)

The library's default behavior can be overridden or augment by its config file. Copy
src/Config/Assets.php.example to app/Config/Assets.php and follow the instructions in the
comments. If no config file is found the library will use its defaults.

## Usage

If installed correctly CodeIgniter 4 will detect and autoload the library, helper, and
(optional) config. Initialize the helper before using its functions:
`helper("tatter\assets");`

Then call the helper functions `css()` and `js()` to output the appropriate assets.

## Structure

The library searches the assets directory (default: public/assets) for files matching the
current route, loading them in a cascading fashion for each route segment.
**Example:** https://example.com/users/view/12

The library will first load any root assets (public/assets/*.css *.js), then assets in
the users subfolder (public/assets/users/) then view subfolder, then 12 subfolder. Any
missing or invalid folders are ignored.

Additional assets may be loaded from the config variable $routes - this is particularly
helpful for including pre-bundled libraries.
**Example:**
```
public $routes = [
	"" => ["bootstrap/dist/css/bootstrap.min.css", "bootstrap/dist/js/bootstrap.bundle.min.js"]
];
```

This tells the library to load Bootstrap for every route ("") without having to move it
from its pre-bundled subdirectory.
