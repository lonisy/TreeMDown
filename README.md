# TreeMDown `[triː <'em> daʊn]`

... is a single page PHP application for browsing markdown documents in a file structure and translating them to HTML.

## Requirements / Dependencies

 * PHP >= 5.3
 * [Parsedown-Extra](https://github.com/erusev/parsedown-extra) via composer
 * Webserver (Apache, nginx, etc.)

**Note:** This application is currently tested on linux systems only.

## Installation

### Via composer

To get the latest stable release, check the versions at [Packagist](http://packagist.org) and add to your `composer.json`:

```json
{
	"require": {
		"hollodotme/treemdown": "~1.0"
	}
}
```

To get the bleeding edge version add this to your `composer.json`:

```json
{
	"repositories": [
		{
			"type": "vcs",
			"url": "git@github.com:hollodotme/TreeMDown.git"
		}
	],

	"require": {
		"hollodotme/treemdown": "dev-master"
	}
}
```

Now include the `vendor/autoload.php` and get started.

## Usage

### Basic

```php
<?php

use hollodotme\TreeMDown\TreeMDown;

$treemdown = new TreeMDown('/path/to/your/markdown/files');

$treemdown->display();

```

### With personalization and options

```php
<?php

use hollodotme\TreeMDown\TreeMDown;

// Create instance
$treemdown = new TreeMDown( '/path/to/your/markdown/files' );

# [Page meta data]
#
# Set a projectname
$treemdown->setProjectName('Your project name');

# Set a short description
$treemdown->setShortDescription('Your short description');

# Set a company name
$treemdown->setCompanyName('Your company name');

# [Output options]
#
# Show or hide empty folders in tree
#
# Default: Empty folders will be displayed
#
#$treemdown->showEmptyFolders();
$treemdown->hideEmptyFolders();

# Set the default file that is shown if no file or path is selected (initial state)
# The file path must be __relative__ to the root directory above: '/path/to/your/markdown/files'
#
# Default: index.md
#
$treemdown->setDefaultFile('README.md');

# [File system options]
#
# Set the patterns for files you want to include
#
# Default: array( '*.md', '*.markdown')
#
$treemdown->setIncludePatterns( array( '*.md', '*.markdown') );

# Set the patterns for files/path you want to exclude
#
# Default: array( '.*' )
#
$treemdown->setExcludePatterns( array( '.*' ) );

$treemdown->display();

```

## Contributions

This application uses the following libraries:

 * [Parsedown-Extra](https://github.com/erusev/parsedown-extra) / [ParseDown](http://parsedown.org)
 * [highlight.js](https://highlightjs.org)
 * [github markdown stylesheet by Chris Patuzzo](https://gist.github.com/tuzz/3331384)
 * [jQuery](http://jquery.com)
