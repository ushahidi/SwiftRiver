<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Model_Droplet_Comment
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Model_Droplet_Comment extends ORM {
	
	protected $_belongs_to = array(
		'droplet' => array(),
		'user' => array()
	);
	
	/**
	 * Auto-update columns for creation
	 * @var string
	 */
    protected $_created_column = array('column' => 'date_added', 'format' => 'Y-m-d H:i:s');



	/**
	 * Overload saving to perform additional functions on the comment
	 */
	public function save(Validation $validation = NULL)
	{
		$ret = parent::save();
		
		DB::update('droplets')
		   ->set(array('comment_count' => DB::expr('comment_count + 1')))
		   ->where("id", "=", $this->droplet_id)
		   ->execute();
		
		return $ret;
	}
}
?>