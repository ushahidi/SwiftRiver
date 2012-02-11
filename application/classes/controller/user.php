<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * User Controller
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
class Controller_User extends Controller_Swiftriver {
	
	/**
	 * sub content
	 */
	private $sub_content;
	
	/**
	 * active
	 */
	private $active;
	
	/**
	 * template type
	 */
	private $template_type;
	
	/**
	 * visited account
	 */
	private $visited_account;

	/**
	 * visited account path
	 */
	private $visited_account_path;

	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		// Get the account we are visiting
		$this->visited_account_path = $this->request->param('account');
		
		$this->visited_account = ORM::factory('account', array('account_path' => $this->visited_account_path));
		
		if ( ! $this->visited_account->loaded())
		{
			$this->request->redirect('dashboard');
		}

		$this->template->content = View::factory('pages/user/main')
			 ->bind('template_type', $this->template_type)
			 ->bind('active', $this->active)
			 ->bind('user', $this->visited_account->user)
			 ->bind('account_path', $this->visited_account_path);					
		$this->template->content->fetch_url = URL::site().'user/'.$this->visited_account_path.'/manage';
		
		// Some info about the owner of the user profile being visited
		// Will be used later for following unfollowing
		$this->template->content->user_item = json_encode(array(
				"id" => $this->visited_account->user->id,
				"type" => "user",					
				"name" => $this->visited_account->user->name,
				"url" => URL::site().$this->visited_account_path,
				"subscribed" => $this->user->has('following', $this->visited_account->user),
				"is_owner" => $this->user->id == $this->visited_account->user->id				
			));		
	}
	
	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->active = 'rivers';
		$this->template_type = 'list';
		
		// Get rivers visible to a user
		$rivers = $this->user->get_other_user_visible_rivers($this->visited_account->user->id);
		$list_items = array();
		foreach ($rivers as $river) {
			$list_items[] = array(
					"id" => $river->id,
					"type" => "river",					
					"name" => $river->river_name,
					"url" => URL::site().$this->visited_account_path.'/river/index/'.$river->id,
					"subscribed" => $this->user->has('river_subscriptions', $river),
					"is_owner" => $river->is_owner($this->user->id)					
				);
		}		
		$this->template->content->list_items = json_encode($list_items);
	}
	
	/**
	 * @return	void
	 */
	public function action_buckets()
	{
		$this->active = 'buckets';
		$this->template_type = 'list';
		
		// Get rivers visible to a user
		$buckets = $this->user->get_other_user_visible_buckets($this->visited_account->user->id);
		$list_items = array();
		foreach ($buckets as $bucket) {
			$list_items[] = array(
					"id" => $bucket->id,
					"type" => "bucket",
					"name" => $bucket->bucket_name,
					"url" => URL::site().$this->visited_account_path.'/bucket/index/'.$bucket->id,
					"subscribed" => $this->user->has('bucket_subscriptions', $bucket),
					"is_owner" => $bucket->is_owner($this->user->id)
				);
		}
		$this->template->content->list_items = json_encode($list_items);
	}
	
	/**
	 * @return	void
	 */
	public function action_manage()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		switch ($this->request->method())
		{
			case "PUT":
				$item_array = json_decode($this->request->body(), TRUE);
				
				if ($item_array['type'] == 'river') 
				{
					
					$river_orm = ORM::factory('river', $item_array['id']);
					if ( ! $river_orm->loaded())
					{
						throw new HTTP_Exception_404(
					        'The requested page :page was not found on this server.',
					        array(':page' => $page)
					        );
					}
					
					// Are we adding a subscription?
					if ($item_array['subscribed'] == 1 AND 
						! $this->user->has('river_subscriptions', $river_orm))
					{
						$this->user->add('river_subscriptions', $river_orm);
					}
					
					// Are we removing a subscription?
					if ($item_array['subscribed'] == 0 AND 
						$this->user->has('river_subscriptions', $river_orm))
					{
						$this->user->remove('river_subscriptions', $river_orm);
					}					
				}
				
				if ($item_array['type'] == 'bucket') 
				{
					
					$bucket_orm = ORM::factory('bucket', $item_array['id']);
					if ( ! $bucket_orm->loaded())
					{
						throw new HTTP_Exception_404(
					        'The requested page :page was not found on this server.',
					        array(':page' => $page)
					        );
					}
					
					// Are we adding a subscription?
					if ($item_array['subscribed'] == 1 AND 
						! $this->user->has('bucket_subscriptions', $bucket_orm))
					{
						$this->user->add('bucket_subscriptions', $bucket_orm);
					}
					
					// Are we removing a subscription?
					if ($item_array['subscribed'] == 0 AND 
						$this->user->has('bucket_subscriptions', $bucket_orm))
					{
						$this->user->remove('bucket_subscriptions', $bucket_orm);
					}					
				}
				
				// Stalking!
				if ($item_array['type'] == 'user') 
				{
					
					$user_orm = ORM::factory('user', $item_array['id']);
					if ( ! $user_orm->loaded())
					{
						throw new HTTP_Exception_404(
					        'The requested page :page was not found on this server.',
					        array(':page' => $page)
					        );
					}
					
					// Are following
					if ($item_array['subscribed'] == 1 AND 
						! $this->user->has('following', $user_orm))
					{
						$this->user->add('following', $user_orm);
					}
					
					// Are unfollowing
					if ($item_array['subscribed'] == 0 AND 
						$this->user->has('following', $user_orm))
					{
						$this->user->remove('following', $user_orm);
					}					
				}
				
			break;
		}
	}
}