<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Identities
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
class Model_Identity extends ORM
{
	/**
	 * An identity belongs to an account
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'account' => array()
		);

	/**
	 * An identity has and belongs to many sources
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'sources' => array(
			'model' => 'sources',
			'through' => 'identities_sources'
		),
		'droplets' => array()
	);

	/**
	 * Overload saving to perform additional functions on the identity
	 */
	public function save(Validation $validation = NULL)
	{
		// Swiftriver Plugin Hook
		Swiftriver_Event::run('swiftriver.identity.pre_save', $this);

		// Do this for first time identities only
		if ($this->loaded() === FALSE)
		{
			// Save the date the identity was first added
			$this->identity_date_add = date("Y-m-d H:i:s", time());
		}
		else
		{
			$this->identity_date_modified = date("Y-m-d H:i:s", time());
		}		

		return parent::save();
	}
	
	/**
	 * Creates an identity record from a droplet
	 *
	 * @param array $droplet Array containing the identity information
	 */
	public static function create_from_droplet(array & $droplet)
	{
		// Set up validation
		$validation = Validation::factory($droplet)
						->rule('identity_orig_id', 'not_empty')
						->rule('channel', 'not_empty');
		
		// Execute the validation rules
		if ($validation->check())
		{
			$origin_id = $droplet['identity_orig_id'];
			$channel = $droplet['channel'];
			
			// Check if the identity exists
			$orm_identity = self::get_identity_by_origin($origin_id, $channel);
			
			// Create new identity
			if ( ! $orm_identity->loaded())
			{
				$orm_identity->identity_orig_id = $droplet['identity_orig_id'];
				$orm_identity->identity_username = $droplet['identity_username'];
				$orm_identity->identity_name = $droplet['identity_name'];
				$orm_identity->save();
			}
			
		
			// Set the identity of the droplet
			$droplet['identity_id'] = $orm_identity->id;
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Retrives an identity based on its origin parameters; channel and origin id
	 * @param string $origin_id
	 * @param strign $channel
	 */
	public static function get_identity_by_origin($origin_id, $channel)
	{
		return ORM::factory('identity')
				->where('identity_orig_id', '=', $origin_id)
				->where('channel', '=', $channel)
				->find();
	}
	
}
?>