<?php
/**
 * The directory in which application specific resources are located.
 * The application directory must contain the bootstrap.php file.
 */
$application = 'application';

/**
 * The directory in which your modules are located.
 *
 * @link http://kohanaframework.org/guide/about.install#modules
 */
$modules = 'modules';

/**
 * Themes directory.
 */
$themes = 'themes';

/**
 * Plugins directory.
 */
$plugins = 'plugins';

/**
 * Set the PHP error reporting level. If you set this in php.ini, you remove this.
 * @see  http://php.net/error_reporting
 *
 * When developing your application, it is highly recommended to enable notices
 * and strict warnings. Enable them by using: E_ALL | E_STRICT
 *
 * In a production environment, it is safe to ignore notices and strict warnings.
 * Disable them by using: E_ALL ^ E_NOTICE
 *
 * When using a legacy application with PHP >= 5.3, it is recommended to disable
 * deprecated notices. Disable with: E_ALL & ~E_DEPRECATED
 */
error_reporting(E_ALL | E_STRICT);

/**
 * End of standard configuration! Changing any of the code below should only be
 * attempted by those with a working knowledge of Kohana internals.
 */

// Set the full path to the docroot
$docroot = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;

// Make the application relative to the docroot, for symlink'd index.php
if ( ! is_dir($application) AND is_dir($docroot.$application))
	$application = $docroot.$application;

// Make the modules relative to the docroot, for symlink'd index.php
if ( ! is_dir($modules) AND is_dir($docroot.$modules))
	$modules = $docroot.$modules;

// Make the themes relative to the docroot, for symlink'd index.php
if ( ! is_dir($themes) AND is_dir($docroot.$themes))
	$themes = $docroot.$themes;

// Make the plugins relative to the docroot, for symlink'd index.php
if ( ! is_dir($plugins) AND is_dir($docroot.$plugins))
	$plugins = $docroot.$plugins;	

// Define the absolute paths for configured directories
$apppath = realpath($application).DIRECTORY_SEPARATOR;
$modpath = realpath($modules).DIRECTORY_SEPARATOR;
define('THEMEPATH', realpath($themes).DIRECTORY_SEPARATOR);
define('PLUGINPATH', realpath($plugins).DIRECTORY_SEPARATOR);

// Flag testing mode is on
define('TESTING_MODE', TRUE);

// Clean up the configuration vars
unset($application, $modules, $themes, $plugins, $docroot);

// Bootstrap the application
require $modpath.'unittest/bootstrap.php';
