<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Buckets
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Bucket extends ORM {
	
	/**
	 * No. of droplets to return on each fetch
	 */
	const DROPLETS_PER_PAGE = 20;
	
	/**
	 * A bucket has and belongs to many droplets
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'droplets' => array(
			'model' => 'droplet',
			'through' => 'buckets_droplets'
			)			
		);

	/**
	 * A bucket belongs to an account and a user
	 *
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
	public static function get_droplets($id = NULL, $page = NULL)
	{
		$droplets = array(
			'total' => 0,
			'droplets' => array()
			);
		
		if ($id)
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
				->where('buckets_droplets.bucket_id', '=', $id)
				->order_by('droplets.id', 'DESC');
				
			// Order & Pagination offset
			$query->order_by('droplets.id', 'DESC');
			if ($page)
			{
			    $query->limit(self::DROPLETS_PER_PAGE);	
		        $query->offset(self::DROPLETS_PER_PAGE * ($page - 1));
	        }				

			// Get our droplets as an Array		
			$droplets['droplets'] = $query->execute()->as_array();
			$droplets['total'] = count($droplets['droplets']);
		}

		return $droplets;
	}
	
}
