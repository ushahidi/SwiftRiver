<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/Kohana/Core'.EXT;

if (is_file(APPPATH.'classes/Kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/Kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/Kohana'.EXT;
}

/**
 * Set the default time zone.
 */
date_default_timezone_set('UTC');

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
	'caching' => Kohana::$environment === Kohana::PRODUCTION,
	'profiling' => Kohana::$environment !== Kohana::PRODUCTION,
	'errors' => TRUE
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
	'auth'            => MODPATH.'auth',        // Basic authentication
	'orm'             => MODPATH.'orm',         // Object Relationship Mapping
	'riverid'         => MODPATH.'riverid',     // Ushahidi products Single Sign On
	'cache'           => MODPATH.'cache',       // Caching with multiple backends
	'dummy'           => MODPATH.'dummy_cache', // Blackhole cache driver
	'database'        => MODPATH.'database',    // Database access
	'image'           => MODPATH.'image',       // Image manipulation
	'minion'          => MODPATH.'minion',      // CLI
	'themes/default'  => THEMEPATH.'default',   // Themes
	'csrf'            => MODPATH.'csrf',        // CSRF
	'captcha'         => MODPATH.'captcha',     // Captcha
	'markdown'        => MODPATH.'markdown',    // Markdown module
	));


/**
 * Initialize the SwiftRiver runtime environment
 * Load plugins, themes and set the Cookie properties
 */
Swiftriver::init();

/**
 * Swiftriver Password Reset Route
 */	
Route::set('login_reset', 'login/reset/<email>/<token>',
    array(
        'email' => '[^/]++'
    ))
	->defaults(array(
		'controller' => 'login',
		'action'     => 'reset',
	));

/**
 * Swiftriver Account Create Route
 */	
Route::set('login_create', 'login/create/<email>/<token>',
    array(
        'email' => '[^/]++'
    ))
	->defaults(array(
		'controller' => 'login',
		'action'     => 'create',
	));

/**
 * Swiftriver Change Email Route
 */	
Route::set('login_changeemail', 'login/changeemail/<old_email>/<new_email>/<token>',
    array(
		'old_email' => '[^/]++',
        'new_email' => '[^/]++'
    ))
	->defaults(array(
		'controller' => 'login',
		'action'     => 'changeemail',
	));

/**
 * Swiftriver Login Route
 */	
Route::set('login', 'login(/<action>(/<id>))', array('id' => '\d+'))
	->defaults(array(
		'controller' => 'login',
		'action'     => 'index',
	));
	
/**
 * API Route
 */
Route::set('drops_api', 'api/drop(/<id>(/<action>))')
	->defaults(array(
		'controller' => 'drop',
		'action'     => 'index'
	));	
		
Route::set('bucket_comment_api', 'api/bucket/<id>/comment')
	->defaults(array(
		'controller' => 'bucket',
		'action'     => 'comment_api'
	));	
	
/**
 * Swiftriver Media Router (JS/CSS/Thumb)
 */	
Route::set('media_js', 'media/js')
	->defaults(array(
		'controller' => 'media',
		'action'     => 'js',
	));
	
Route::set('media_css', 'media/css')
	->defaults(array(
		'controller' => 'media',
		'action'     => 'css',
	));

Route::set('media_thumb', 'media/thumb')
	->defaults(array(
		'controller' => 'media',
		'action'     => 'thumb',
	));	 	
	
Route::set('media', 'media(/<file>)', array('file' => '.+'))
    ->defaults(array(
    'controller'    => 'media',
    'action'        => 'index',
    'file'          => NULL,
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
 * Swiftriver Settings Route
 */	
Route::set('settings', 'settings(/<controller>(/<action>(/<id>)))', array('id' => '\d+'))
	->defaults(array(
		'controller' => 'main',
		'action'     => 'index',
		'directory'  => 'settings'
	));
	

/**
 * Account Registration Route
 */
Route::set('register', 'register')
	->defaults(array(
		'controller' => 'user',
		'action'     => 'register'
	));	

/**
 * Search 
 */
Route::set('search', 'search(/<action>(/<id>(/<id2>)))')
    ->defaults(array(
    	'controller' => 'search',
    	'action' => 'index'
    ));
	
/**
 * Account Route
 */
Route::set('account_pages', '<account>/<action>',
    array(
    	'action' => '(rivers|buckets|create|settings|share|followers|following|invite)'
    ))
	->defaults(array(
		'controller' => 'user',
		'action'     => 'index'
	));


Route::set('account_bucket_new', '<account>/bucket/new')
	->defaults(array(
		'controller' => 'bucket',
		'action'     => 'new'
	));

/**
 * Trends
 */
Route::set('account_trend', '<account>/<context>/<name>/trend/<controller>(/<action>(/<id>))',
	array(
		'context' => '(river|bucket)',
		'id' => '\d+'
	))
	->defaults(array(
		'controller' => 'main',
		'action'     => 'index',
		'directory'  => 'trend'
	));
	
/**
 * River and bucket settings
 */
Route::set('river_bucket_settings', '<account>/<directory>/<name>/settings(/<controller>(/<action>(/<id>(/<id2>))))',
	array(
		'directory' => '(river|bucket)',
	))
	->defaults(array(
		'controller' => 'settings',
		'action'     => 'index'
	));

/**
 * River and bucket discussions
 */
Route::set('river_bucket_discussion', '<account>/<directory>/<name>/discussion(/<action>(/<id>))',
	array(
		'directory' => '(river|bucket)',
	))
	->defaults(array(
		'controller' => 'discussion',
		'action'     => 'index'
	));	

/**
 * River and bucket create
 */
Route::set('river_bucket_create', '<account>/<directory>/create(/<action>(/<id>))',
	array(
		'directory' => '(river|bucket)',
		'id' => '\d+'
	))
	->defaults(array(
		'controller' => 'create',
		'action'     => 'index'
	));

/**
 * Rivers and Buckets
 */
Route::set('account', '<account>(/<controller>/<name>(/<action>(/<id>(/<id2>))))',
	array(
		'controller' => '(user|river|bucket)',
	))
	->defaults(array(
		'controller' => 'user',
		'action'     => 'index'
	));


/**
 * Swiftriver Default Route
 */	
Route::set('default', '(/<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'swiftriver',
		'action'     => 'index',
		'id' => '\d+'
	));

	
/**
 * Error handler
 */	
Route::set('error', 'error/<action>(/<message>)', array('action' => '[0-9]++', 'message' => '.+'))
->defaults(array(
    'controller' => 'error_handler'
));
