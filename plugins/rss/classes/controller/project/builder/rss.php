<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Rss Feed Builder Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Project_Builder_Rss extends Controller_Project_Builder_Main {
	
	/**
	 * @return	void
	 */
	public function before($id = NULL)
	{
		// Execute parent::before first
		parent::before();
	}

	/**
	 * RSS Feed Builder
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('rss/builder')
			->bind('post', $post)
			->bind('errors', $errors)
			->bind('menu', $this->menu);

		$id = $this->request->param('feed_id');
		$post = array(
			'url' => '',
			'keywords' => ''
			);

		// save the data
		if ($_POST)
		{
			// Validation Checks
			$post = Validation::factory($_POST)
				->rule('url', 'not_empty')
				->rule('url', 'max_length', array(':value', '255'))
				->rule('users', 'Valid::url')
				->rule('keywords', 'max_length', array(':value', '255'));

			if ($post->check())
			{
				// Save The Feed
				$feed = ORM::factory('feed', $id);
				$feed->project_id = $this->project->id;
				$feed->user_id = $this->user->id;
				$feed->service = 'rss';	// *** Name of the Plugin/Service
				$feed->save();

				// Delete existing keys first
				$existing = ORM::factory('feed_option')
					->where('feed_id', '=', $feed->id)
					->find_all();
				foreach ($existing as $key)
				{
					$key->delete();
				}

				if ($post['url'])
				{
					$option = ORM::factory('feed_option');
					$option->feed_id = $feed->id;
					$option->key = 'url';
					$option->value = trim($post['url']);
					$option->save();
				}

				if ($post['keywords'])
				{
					$option = ORM::factory('feed_option');
					$option->feed_id = $feed->id;
					$option->key = 'keywords';
					$option->value = trim($post['keywords']);
					$option->save();
				}
				
				// Redirect Back
				Request::current()->redirect('project/'.$this->project->id.'/builder');
			}
			else
			{
				//validation failed, get errors
				$errors = $post->errors('options');
			}
		}
		else
		{
			if ($id)
			{
				$options = ORM::factory('feed_option')
					->where('feed_id', '=', $id)
					->find_all();
				foreach ($options as $option)
				{
					$post[$option->key] = $option->value;
				}
			}
		}
	}

}