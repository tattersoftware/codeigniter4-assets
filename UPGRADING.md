# Upgrade Guide

## Version 2 to 3
***

> Note: This is a complete refactor! Please be sure to read the docs carefully before upgrading.

* The services no longer exist; remove all references to `Services::Assets` and `Services::manifests` to avoid exceptions
* This library no longer publishes Assets; convert Manifests to the framework's new [Publisher format](https://codeigniter.com/user_guide/libraries/publisher.html)
* Many of the example Manifests now have an official Publisher equivalent at [Tatter\Frontend](https://github.com/tattersoftware/codeigniter4-frontend)
* The `DirectoryHandler` (mapping public directories to routes) has no equivalent in `v3` so be sure to create explicit bundles and routes for any you were using
* The view files have been removed and replaced by `AssetsFilter` to handle tag injection directly; read the docs on setting up the filter

For an example of converting a `v2` JSON manifest to a `v3` framework Publisher compare these files:

* https://github.com/tattersoftware/codeigniter4-assets/blob/267220d437786e0ddb9d7681745f5942d95c543b/manifests/FontAwesome.json
* https://github.com/tattersoftware/codeigniter4-frontend/blob/ae61773f279333c3a606498364977a8cec45d303/src/Publishers/FontAwesomePublisher.php
