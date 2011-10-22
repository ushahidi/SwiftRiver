<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Accounts
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @category Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Account extends ORM
{
	/**
	 * An account has many channel_filters, buckets, snapshots, sources
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'channel_filters' => array(),
		'buckets' => array(),
		'snapshots' => array(),
		'sources' => array()
		);

	/**
	 * An account has and belongs to many droplets, links, places, tags, attachments and plugins
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'droplets' => array(
			'model' => 'attachment',
			'through' => 'accounts_droplets'
			),
		'attachments' => array(
			'model' => 'attachment',
			'through' => 'droplets_attachments'
			),
		'links' => array(
			'model' => 'story',
			'through' => 'droplets_links'
			),
		'tags' => array(
			'model' => 'tag',
			'through' => 'droplets_tags'
			),
		'places' => array(
			'model' => 'link',
			'through' => 'droplets_places'
			),
		'plugins' => array(
			'model' => 'plugin',
			'through' => 'accounts_plugins'
			),					
		);		
	
	/**
	 * An account belongs to a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array('user' => array());
	
	/**
	 * Validation for accounts
	 * @param array $arr
	 * @return array
	 */
	public function validate($arr)
	{
		return Validation::factory($arr)
			->rule('project_title', 'not_empty')
			->rule('project_title', 'min_length', array(':value', 3))
			->rule('project_title', 'max_length', array(':value', 255));
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
}
