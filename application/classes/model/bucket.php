<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Buckets
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Bucket extends ORM {
	
	/**
	 * No. of droplets to return on each fetch
	 */
	const DROPLETS_PER_PAGE = 20;
	
	/**
	 * One-to-many relationship definitions
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		// A bucket has many droplets
		'droplets' => array(
			'model' => 'droplet',
			'through' => 'buckets_droplets'
			),
		'collaborators' => array(
			'model' => 'user',
			'through' => 'bucket_collaborators'
			)					
	);

	/**
	 * A bucket belongs to an account and a user
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'account' => array(),
		'user' => array()
	);

	/**
	 * Validation for buckets
	 * @param array $arr
	 * @return array
	 */
	public function validate($arr)
	{
		return Validation::factory($arr)
			->rule('bucket_name', 'not_empty')
			->rule('bucket_name', 'min_length', array(':value', 3))
			->rule('bucket_name', 'max_length', array(':value', 255));
	}		
		
	/**
	 * Overload saving to perform additional functions on the bucket
	 */
	public function save(Validation $validation = NULL)
	{
		// Do this for first time buckets only
		if ($this->loaded() === FALSE)
		{
			// Save the date the bucket was first added
			$this->bucket_date_add = date("Y-m-d H:i:s", time());
		}

		$bucket = parent::save();

		// Swiftriver Plugin Hook -- execute after saving a bucket
		Swiftriver_Event::run('swiftriver.bucket.save', $bucket);

		return $bucket;
	}
	
	/**
	 * Get the droplets for the specified bucket
	 *
	 * @param int $id ID of the Bucket
	 * @return array $droplets Total and Array of Droplets
	 */
	public static function get_droplets($bucket_id = NULL, $page = NULL, $max_id = PHP_INT_MAX)
	{
		$droplets = array(
			'total' => 0,
			'droplets' => array()
			);
		
		$bucket_orm = ORM::factory('bucket', $bucket_id);
		if ($bucket_orm->loaded())
		{
			// Build Buckets Query
			$query = DB::select(array(DB::expr('DISTINCT droplets.id'), 'id'), 
								'droplet_title', 'droplet_content', 
								'droplets.channel','identity_name', 'identity_avatar', 'droplet_date_pub')
				->from('droplets')
				->join('buckets_droplets', 'INNER')
				->on('buckets_droplets.droplet_id', '=', 'droplets.id')
				->join('identities')
				->on('droplets.identity_id', '=', 'identities.id')				
				->where('buckets_droplets.bucket_id', '=', $bucket_id)
				->where('droplets.droplet_processed', '=', 1)
				->where('droplets.id', '<=', $max_id)
				->order_by('buckets_droplets.droplet_date_added', 'DESC');
				
			// Order & Pagination offset
			$query->order_by('droplets.id', 'DESC');
			if ($page)
			{
				$query->limit(self::DROPLETS_PER_PAGE); 
				$query->offset(self::DROPLETS_PER_PAGE * ($page - 1));
			}
			
			Kohana::$log->add(Log::DEBUG, $query->__toString());
			
			// Get our droplets as an Array		
			$droplets['droplets'] = $query->execute()->as_array();
			
			// Populate buckets array			
			Model_Droplet::populate_buckets($droplets['droplets']);
			
			// Populate tags array			
			Model_Droplet::populate_tags($droplets['droplets'], $bucket_orm->account_id);
			
			// Populate links array			
			Model_Droplet::populate_links($droplets['droplets']);
			
			// Populate places array			
			Model_Droplet::populate_places($droplets['droplets']);
			
			// Populate the discussions array
			Model_Droplet::populate_discussions($droplets['droplets']);
			
			$droplets['total'] = count($droplets['droplets']);
		}

		return $droplets;
	}

	/**
	 * Get the droplets newer than the specified id
	 *
	 * @param int $id ID of the Bucket
	 * @return array $droplets Total and Array of Droplets
	 */
	public static function get_droplets_since_id($bucket_id, $since_id)
	{
		$droplets = array(
			'total' => 0,
			'droplets' => array()
			);
		
		$bucket_orm = ORM::factory('bucket', $bucket_id);
		if ($bucket_orm->loaded())
		{		
			// Build Buckets Query
			$query = DB::select(array(DB::expr('DISTINCT droplets.id'), 'id'), 
								'droplet_title', 'droplet_content', 
								'droplets.channel','identity_name', 'identity_avatar', 'droplet_date_pub')
				->from('droplets')
				->join('buckets_droplets', 'INNER')
				->on('buckets_droplets.droplet_id', '=', 'droplets.id')
				->join('identities')
				->on('droplets.identity_id', '=', 'identities.id')				
				->where('buckets_droplets.bucket_id', '=', $bucket_id)
				->where('droplets.droplet_processed', '=', 1)
				->where('droplets.id', '>', $since_id)
				->order_by('droplets.id', 'DESC');
				
			// Get our droplets as an Array		
			$droplets['droplets'] = $query->execute()->as_array();
			
			// Populate buckets array			
			Model_Droplet::populate_buckets($droplets['droplets']);
			
			// Populate tags array			
			Model_Droplet::populate_tags($droplets['droplets'], $bucket_orm->account_id);
			
			// Populate links array			
			Model_Droplet::populate_links($droplets['droplets']);			
			
			// Populate places array			
			Model_Droplet::populate_places($droplets['droplets']);			
			
			
			$droplets['total'] = count($droplets['droplets']);
		}

		return $droplets;
	}	
	
	/**
	 * Create a bucket from an array
	 *
	 * @param array
	 * @return Model_Bucket
	 */
	public static function create_from_array($bucket_array) 
	{		
		$bucket_orm = ORM::factory('bucket');
		$bucket_orm->account_id = $bucket_array['account_id'];
		$bucket_orm->user_id = $bucket_array['user_id'];
		$bucket_orm->bucket_name = $bucket_array['bucket_name'];
		$bucket_orm->save();
		return $bucket_orm;
	}
	
	/**
	 * Gets the list of users collaborating on a bucket
	 *
	 * @param int $bucket_id Database ID of the bucket
	 * @return array
	 */
	public static function get_collaborators($bucket_id)
	{
		$results = DB::select(array('bucket_collaborators.id', 'id'), 
			array('users.name', 'collaborator_name'))
			->from('bucket_collaborators')
			->join('buckets', 'INNER')
			->on('bucket_collaborators.bucket_id', '=', 'buckets.id')
			->join('users', 'INNER')
			->on('bucket_collaborators.collaborator_id', '=', 'users.id')
			->where('buckets.id', '=', $bucket_id)
			->execute();
		
		return $results->as_array();
	}
	
	/**
	 * Get the max droplet id in a bucket
	 *
	 * @param int $id ID of the Bucket
	 * @return int
	 */
	public static function get_max_droplet_id($bucket_id = NULL)
	{
		// Build Buckets Query
		$query = DB::select(array(DB::expr('MAX(droplets.id)'), 'id'))
			->from('droplets')
			->join('buckets_droplets', 'INNER')
			->on('buckets_droplets.droplet_id', '=', 'droplets.id')
			->join('identities')
			->on('droplets.identity_id', '=', 'identities.id')				
			->where('buckets_droplets.bucket_id', '=', $bucket_id)
			->where('droplets.droplet_processed', '=', 1);
			
		return $query->execute()->get('id', 0);		
	}
	
	/*
	 * Adds a droplet to bucket
	 *
	 * @param int $bucket_id Database ID of the bucket
	 * @param Model_Droplet $droplet Droplet instance to be associated with the river
	 * @return bool TRUE on succeed, FALSE otherwise
	 */
	public static function add_droplet($bucket_id, $droplet)
	{
		if ( ! $droplet instanceof Model_Droplet)
		{
			// Log the error
			Kohana::$log->add(Log::ERROR, "Expected Model_Droplet in parameter droplet. Found :type instead.", 
				array(":type" => gettype($droplet)));
			return FALSE;
		}
		
		// Get ORM reference for the river
		$bucket = ORM::factory('bucket', $bucket_id);
		
		// Check if the river exists and if its associated with the current droplet
		if ($bucket->loaded() AND ! $bucket->has('droplets', $droplet))
		{
			$bucket->add('droplets', $droplet);
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Checks if the given user owns the river, is an account collaborator
	 * or a river collaborator.
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
		
		// Is the user_id the owner?
		if ($this->account->user->id == $user_id)
		{
			return TRUE;
		}
				
		// Otherwise, is the user_id a collaborator
		return $this->has('collaborators', $user_orm);
	}
}
