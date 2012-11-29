<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Identities
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
			'model' => 'Sources',
			'through' => 'identities_sources'
		),
		'droplets' => array()
	);

    /**
	 * Auto-update columns for creation
	 * @var string
	 */
    protected $_created_column = array('column' => 'identity_date_add', 'format' => 'Y-m-d H:i:s');

    /**
	 * Auto-update columns for updates
	 * @var string
	 */
    protected $_updated_column = array('column' => 'identity_date_modified', 'format' => 'Y-m-d H:i:s');

	/**
	 * Given an array of droplets information, populate identities from the
	 * DB creating those that do not exist.
	 *
	 * @param array $identities Array containing the identity information
	 */
	public static function get_identities(array & $droplets)
	{
		if (empty($droplets))
			return;
			
		// Generate the identity hashes and create a hash array of the given identites
		$identities_idx = array();
		foreach ($droplets as $key => $droplet)
		{
			if ( ! isset($droplet['identity_id']))
			{
				$hash = md5($droplet['channel'].$droplet['identity_orig_id']);
				if (empty($identities_idx[$hash]))
				{
					$identities_idx[$hash] = array();
				}
				$identities_idx[$hash][] = $key;
			}
		}
		
		if (empty($identities_idx))
			return;
				
		Swiftriver_Mutex::obtain(get_class(), 3600);
		
		// Create the missing entries			
		// Find those that exist
		$found = DB::select('hash', 'id')
					->from('identities')
					->where('hash', 'IN', array_keys($identities_idx))
					->execute()
					->as_array();
		
		// Update the found entries
		$new_identity_count = count($identities_idx);
		foreach ($found as $hash)
		{
			foreach ($identities_idx[$hash['hash']] as $key)
			{
				$droplets[$key]['identity_id'] = $hash['id'];
			}
			$new_identity_count--;
			unset($identities_idx[$hash['hash']]);
		}
		
		if ( ! empty($identities_idx))
		{
			// Get a range of IDs to be used in inserting the new drops
			$base_id = self::get_ids($new_identity_count);
			
			$query = DB::insert('identities', array('id', 'hash', 'channel', 'identity_orig_id', 'identity_name', 'identity_username', 'identity_avatar'));
			foreach ($identities_idx as $hash => $keys)
			{
				$droplet = NULL;
				foreach ($keys as $key)
				{
					$droplet = $droplets[$key];
					$droplets[$key]['identity_id'] = $base_id;
				}
				$query->values(array(
					'id' => $base_id++,
					'hash' => $hash,
					'channel' => $droplet['channel'],
					'identity_orig_id' => $droplet['identity_orig_id'],
					'identity_name' => $droplet['identity_name'],
					'identity_username' => $droplet['identity_username'],
					'identity_avatar' => $droplet['identity_avatar']
				));
			}
			$query->execute();
		}
		
		Swiftriver_Mutex::release(get_class());
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
				$orm_identity->identity_avatar = $droplet['identity_avatar'];
				$orm_identity->channel = $droplet['channel'];
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
		return ORM::factory('Identity')
				->where('identity_orig_id', '=', $origin_id)
				->where('channel', '=', $channel)
				->find();
	}
	
	/**
	 * Get a range of IDs to be used for inserting identities
	 *
	 * @param int $num Number of IDs to be generated.
	 * @return int The lowe limit of the range requested
	 */
	public static function get_ids($num)
	{
	    // Build River Query
		$query = DB::query(Database::SELECT, "/*ms=master*/SELECT NEXTVAL('identities',$num) AS id");
		    
		return intval($query->execute()->get('id', 0));
	}
	
}
?>
