<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Comments
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
class Model_Bucket_Comment extends ORM
{
	/**
	 * A comment belongs to an bucket and a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'bucket' => array(),
		'user' => array()
		);

	/**
	 * A comment has many droplet_scores
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'bucket_comment_scores' => array()
		);	
		
	/**
	 * Auto-update columns for creation
	 * @var string
	 */
    protected $_created_column = array('column' => 'comment_date_add', 'format' => 'Y-m-d H:i:s');
	
	/**
	 * Auto-update columns for updates
	 * @var string
	 */
    protected $_updated_column = array('column' => 'comment_date_modified', 'format' => 'Y-m-d H:i:s');

	/**
	 * Validation rules for comments
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'comment_content' => array(
				array('not_empty'),
				array('min_length', array(':value', 3)),
			),
		);
	}
	
	/**
	 * Creates a new comment
	 *
	 * @param array $comment
	 * @return Model_Bucket_Comment
	 */
	public static function create_new($comment_text, $bucket_id, $user_id)
	{
		$comment = ORM::factory('Bucket_Comment');
		$comment->bucket_id = $bucket_id;
		$comment->user_id = $user_id;
		$comment->comment_content = $comment_text;
		$comment->save();
		
		return $comment;
	}
	
}
