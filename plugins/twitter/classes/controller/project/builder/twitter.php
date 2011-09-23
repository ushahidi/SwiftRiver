<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Twitter Feed Builder Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.swiftly.org
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Project_Builder_Twitter extends Controller_Project_Builder_Main {
	
	/**
	 * @return	void
	 */
	public function before($id = NULL)
	{
		// Execute parent::before first
		parent::before();
	}

	/**
	 * Twitter Feed Builder
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('twitter/builder')
			->bind('post', $post)
			->bind('errors', $errors)
			->bind('menu', $this->menu);

		$id = $this->request->param('feed_id');
		$post = array(
			'keywords' => '',
			'hashtags' => '',
			'users' => '',
			'place' => ''
			);

		// save the data
		if ($_POST)
		{
			// Validation Checks
			$post = Validation::factory($_POST)
				->rule('keywords', 'max_length', array(':value', '255'))
				->rule('hashtags', 'max_length', array(':value', '255'))
				->rule('users', 'max_length', array(':value', '255'))
				->rule('place', 'max_length', array(':value', '255'));

			if ($post->check())
			{
				// Save The Feed
				$feed = ORM::factory('feed', $id);
				$feed->project_id = $this->project->id;
				$feed->user_id = $this->user->id;
				$feed->service = 'twitter';	// *** Name of the Plugin/Service
				$feed->save();

				// Delete existing keys first
				$existing = ORM::factory('feed_option')
					->where('feed_id', '=', $feed->id)
					->find_all();
				foreach ($existing as $key)
				{
					$key->delete();
				}

				if ($post['keywords'])
				{
					$option = ORM::factory('feed_option');
					$option->feed_id = $feed->id;
					$option->key = 'keywords';
					$option->value = trim($post['keywords']);
					$option->save();
				}

				if ($post['hashtags'])
				{
					$option = ORM::factory('feed_option');
					$option->feed_id = $feed->id;
					$option->key = 'hashtags';
					$option->value = trim($post['hashtags']);
					$option->save();
				}

				if ($post['users'])
				{
					$option = ORM::factory('feed_option');
					$option->feed_id = $feed->id;
					$option->key = 'users';
					$option->value = trim($post['users']);
					$option->save();
				}

				if ($post['place'])
				{
					$option = ORM::factory('feed_option');
					$option->feed_id = $feed->id;
					$option->key = 'place';
					$option->value = trim($post['place']);
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