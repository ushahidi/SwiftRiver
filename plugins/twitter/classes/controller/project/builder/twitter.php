<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Twitter Feed Builder Controller
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
			'hashtag' => '',
			'from' => '',
			'to' => '',
			'mention' => '',
			'place' => ''
			);

		// save the data
		if ($_POST)
		{
			// Validation Checks
			$post = Validation::factory($_POST)
				->rule('keywords', 'max_length', array(':value', '255'))
				->rule('hashtag', 'max_length', array(':value', '255'))
				->rule('from', 'max_length', array(':value', '255'))
				->rule('to', 'max_length', array(':value', '255'))
				->rule('mention', 'max_length', array(':value', '255'))
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

				//** I Know ORM doesn't look pretty :|

				if ($post['keywords'])
				{
					$option = ORM::factory('feed_option');
					$option->feed_id = $feed->id;
					$option->key = 'keywords';
					$option->value = trim($post['keywords']);
					$option->save();
				}

				if ($post['hashtag'])
				{
					$option = ORM::factory('feed_option');
					$option->feed_id = $feed->id;
					$option->key = 'hashtag';
					$option->value = trim($post['hashtag']);
					$option->save();
				}

				if ($post['from'])
				{
					$option = ORM::factory('feed_option');
					$option->feed_id = $feed->id;
					$option->key = 'from';
					$option->value = trim($post['from']);
					$option->save();
				}

				if ($post['to'])
				{
					$option = ORM::factory('feed_option');
					$option->feed_id = $feed->id;
					$option->key = 'to';
					$option->value = trim($post['to']);
					$option->save();
				}

				if ($post['mention'])
				{
					$option = ORM::factory('feed_option');
					$option->feed_id = $feed->id;
					$option->key = 'mention';
					$option->value = trim($post['mention']);
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