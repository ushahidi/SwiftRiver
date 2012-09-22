<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Accounts
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Model_Account extends ORM
{
	/**
	 * An account has many rivers, buckets, snapshots, sources
	 *
	 * An account has and belongs to many droplet_links,
	 * droplet_places, droplet_tags, droplet_attachments
	 * and plugins
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'rivers' => array(),
		'buckets' => array(),
		'snapshots' => array(),
		'sources' => array(),
		'account_collaborators' => array(),
		'droplets_links' => array(
			'model' => 'droplets_link',
			'through' => 'accounts_droplets_links'
			),
		'droplets_media' => array(
			'model' => 'droplets_media',
			'through' => 'accounts_droplets_media'
			),		
		'droplets_tags' => array(
			'model' => 'droplets_tag',
			'through' => 'accounts_droplets_tags'
			),
		'droplets_places' => array(
			'model' => 'droplets_place',
			'through' => 'accounts_droplets_places'
			),
		'plugins' => array(
			'model' => 'plugin',
			'through' => 'accounts_plugins'
			),
		'account_droplet_tags' => array(
 			'model' => 'account_droplet_tag'
			),
		'account_droplet_links' => array(
 			'model' => 'account_droplet_link'
			),						
		'account_droplet_places' => array(
 			'model' => 'account_droplet_place'
			)		
		);		
	
	/**
	 * An account belongs to a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array('user' => array());
	
	/**
	 * Rules for the account model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'account_path' => array(
				array('not_empty'),
				array('alpha_dash')
			)
		);
	}
	
	/**
	 * Filters to run when data is set in this model. account_path is always set to lowercase
	 *
	 * @return array Filters
	 */
	public function filters()
	{
		return array(
			'account_path' => array(
				array('strtolower')
			),
			'account_path' => array(
				array('trim')
			)
		);
	}

	/**
	 * Overload saving to perform additional functions on the account
	 */
	public function save(Validation $validation = NULL)
	{

		// Do this for first time items only
		if ($this->loaded() === FALSE)
		{
			// Save the original creator of this account
			// Logged In User
			$user = Auth::instance()->get_user();
			if ($user)
			{
				$this->user_id = $user->id;
			}

			// Save the date the feed was first added
			$this->account_date_add = date("Y-m-d H:i:s", time());
		}
		else
		{
			$this->account_date_modified = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}
	
	/**
	 * Gets an account's collaborators as an array
	 *
	 * @return array
	 */	
	public function get_collaborators()
	{
		$collaborators = array();
		
		foreach ($this->account_collaborators->find_all() as $collaborator)
		{
			$collaborators[] = array('id' => $collaborator->user->id, 
			                         'name' => $collaborator->user->name,
			                         'account_path' => $collaborator->user->account->account_path,
			                         'collaborator_active' => $collaborator->collaborator_active,
			                         'avatar' => Swiftriver_Users::gravatar($collaborator->user->email, 40)
			);
		}
		
		return $collaborators;
	}
	
	/**
	 * Checks if the given user owns the account or is an account collaborator
	 *
	 * @param int $user_id Database ID of the user	
	 * @return int
	 */
	public function is_owner($user_id)
	{
		// Does the user exist?
		$user_orm = ORM::factory('user', $user_id);
		if ( ! $user_orm->loaded())
		{
			return FALSE;
		}
		
		// Does the user own the account?
		if ($this->user->id == $user_id)
		{
			return TRUE;
		}
		
		return FALSE;		
	}
	
}
