<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Init for the FeedWriter plugin
 *
 * @package   SwiftRiver
 * @author    Ushahidi Team
 * @category  Plugins
 * @copyright (c) 2008-2012 Ushahidi Inc <http://ushahidi.com>
 */

class Feedwriter_Init
{
	public static function InjectMeta()
	{
		if (Request::current()->controller() == 'bucket' &&
			strlen(Request::current()->param('name')) > 0)
		{
			$rss_url = URL::site(Route::get('feeds')->uri(array(
				'account' => Request::current()->param('account'),
				'name'	  => Request::current()->param('name'),
				'action'  => 'rss'
			)), true);
			$atom_url = URL::site(Route::get('feeds')->uri(array(
				'account' => Request::current()->param('account'),
				'name'	  => Request::current()->param('name'),
				'action'  => 'atom'
			)), true);
			echo '<link rel="alternate" title="RSS" type="application/rss+xml" href="'.$rss_url.
				'" /><link rel="alternate" title="Atom" type="application/atom+xml" href="'.
				$atom_url.'" />';
		}
	}

	public static function InjectIcon()
	{
		$visited_account = ORM::factory('account',
			array('account_path' => Request::current()->param('account')));
		
		$bucket = ORM::factory('bucket')
			->where('bucket_name_url', '=', Request::current()->param('name'))
			->where('account_id', '=', $visited_account->id)
			->find();

		if (Request::current()->controller() == 'bucket' &&
			strlen(Request::current()->param('name')) > 0)
			echo HTML::script("plugins/feedwriter/media/js/icon.php?t=".$bucket->public_token);
	}
}

Swiftriver_Event::add('swiftriver.template.meta', array('Feedwriter_Init', 'InjectMeta'));
Swiftriver_Event::add('swiftriver.template.head', array('Feedwriter_Init', 'InjectIcon'));

// Bind the plugin to valid URLs
Route::set('feeds', '<account>/bucket/<name>/<action>',
	array(
		'action' => '(rss|atom)'
	))
	->defaults(array(
		'controller' => 'feedwriter',
		'action' => 'generate'
	));



?>
