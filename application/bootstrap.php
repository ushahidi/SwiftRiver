<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/kohana/core'.EXT;

if (is_file(APPPATH.'classes/kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/kohana'.EXT;
}

/**
 * Set the default time zone.
 */
date_default_timezone_set('America/Chicago');

/**
 * Set the default locale.
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
	'base_url'   => '/',
	'index_file' => '',
	'cache_dir' => APPPATH.'/cache',
	'caching' => FALSE,
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	'auth'         => MODPATH.'auth',       // Basic authentication
	'orm'          => MODPATH.'orm',        // Object Relationship Mapping
	'cache'        => MODPATH.'cache',      // Caching with multiple backends
	'database'     => MODPATH.'database',   // Database access
	'image'        => MODPATH.'image',      // Image manipulation
	'pagination'   => MODPATH.'pagination', // Pagination
	'themes/default' => THEMEPATH.'default', // Themes
	));


/**
 * Activate Enabled Plugins
 */
Swiftriver_Plugins::load();

// Add the current default theme to the list of modules
$theme = ORM::factory('setting')->where('key', '=', 'site_theme')->find();

if ($theme->loaded() AND !empty($theme->value) AND $theme->value != "default")
{
	Kohana::modules(array_merge(
		array('themes/'.$theme->value => THEMEPATH.$theme->value),
		Kohana::modules()
	));
}

// Clean up
unset($active_plugins, $theme);
	

Cookie::$salt = 'cZjO0Lgfv7QrRGiG3XZJZ7fXuPz0vfcL';


/**
 * Swiftriver Login Route
 */	
Route::set('login', 'login(/<action>(/<id>))')
	->defaults(array(
		'controller' => 'login',
		'action'     => 'index',
	));

/**
 * Swiftriver Crawl Route
 */	
Route::set('crawler', 'crawler(/<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'main',
		'action'     => 'index',
		'directory'  => 'crawler'
	));	

/**
 * Swiftriver Media Router (JS/CSS)
 */	
Route::set('media', 'media(/<action>(/<id>))')
	->defaults(array(
		'controller' => 'media',
		'action'     => 'index',
	));

/**
 * Swiftriver Dashboard Route
 */	
Route::set('dashboard', 'dashboard(/<action>(/<id>))')
	->defaults(array(
		'controller' => 'dashboard',
		'action'     => 'index',
	));	

/**
 * Swiftriver Welcome Route
 */	
Route::set('welcome', 'welcome(/<action>(/<id>))')
	->defaults(array(
		'controller' => 'welcome',
		'action'     => 'index',
	));

/**
 * Swiftriver Bucket Route
 */
Route::set('bucket', 'bucket(/<action>(/<id>))', array('id' => '\d+'))
	->defaults(array(
		'controller' => 'bucket',
		'action'     => 'index',
	));	

/**
 * Swiftriver River Route
 */
Route::set('river', 'river(/<action>(/<id>))', array('id' => '\d+'))
	->defaults(array(
		'controller' => 'river',
		'action'     => 'index',
	));
	
/**
 * Swiftriver Droplet Route
 */
Route::set('droplet', 'droplet(/<action>(/<id>))', array('id' => '\d+'))
	->defaults(array(
		'controller' => 'droplet',
		'action'     => 'index',
	));

/**
 * Swiftriver Settings Route
 */	
Route::set('settings', 'settings(/<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'main',
		'action'     => 'index',
		'directory'  => 'settings'
	));	


//*********** WHEN NOT LOGGED IN USE ACCOUNT NAMESPACE ***********//

/**
 * Swiftriver Bucket Route
 */
Route::set('bucket_ext', '<account>(/<controller>(/<action>(/<id>)))', array('id' => '\d+'))
	->defaults(array(
		'controller' => 'bucket',
		'action'     => 'index',
	));	

/**
 * Swiftriver River Route
 */
Route::set('river_ext', '<account>(/<controller>(/<action>(/<id>)))', array('id' => '\d+'))
	->defaults(array(
		'controller' => 'river',
		'action'     => 'index',
	));

/**
 * Swiftriver Trends Buckets Route
 */	
Route::set('trend_bucket', '<account>/bucket/trend(/<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'main',
		'action'     => 'index',
		'directory'  => 'trend'
	));
	
/**
 * Swiftriver Visualize Rivers Route
 */	
Route::set('trend_river', '<account>/river/trend(/<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'main',
		'action'     => 'index',
		'directory'  => 'trend'
	));	

/**
 * Swiftriver Default Route
 */	
Route::set('default', '(/<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'swiftriver',
		'action'     => 'index',
	));