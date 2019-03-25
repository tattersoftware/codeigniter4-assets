<?php

/***
*
* This file contains optional helper functions to make calling the library easier.
* Recommended usage:
*	1. Load the helper with `helper("assets")`
*	2. Call `css()` in <head> and `js()` before </body>
*
***/

if (! function_exists('css'))
{
	// outputs all route-relevant and configured CSS tags, or
	// given a path outputs a single CSS tag
	function css(string $file = null) {
		$assets = new Tatter\Assets\Assets();
	
		// intercept requests for a single file
		if (is_string($file))
			return $assets->displayFile($file);
		else
			return $assets->display("css");	
	}
}

if (! function_exists('js'))
{
	// outputs all route-relevant and configured JS tags, or
	// given a path outputs a single JS tag
	function js(string $file = null) {
		$assets = new Tatter\Assets\Assets();
	
		// intercept requests for a single file
		if (is_string($file))
			return $assets->displayFile($file);
		else
			return $assets->display("js");	
	}
}
