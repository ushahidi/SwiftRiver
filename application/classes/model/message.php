<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Messages
 * Implemented in User/Messages
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category  Models
 * @copyright Ushahidi - http://www.ushahidi.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3)
 */

class Model_Message extends ORM {

	/**
	 * "Belongs to" relationships
	 * @var array
	 */
	protected $_belongs_to = array(
		'recipient' => array(
			'model' => 'user',
			'foreign_key' => 'recipient_id',
		),
		'sender' => array(
			'model' => 'user',
			'foreign_key' => 'sender_id',
		)
	);

	/**
	 * Auto-update columns for creation
	 * @var string (array)
	 */
	protected $_created_column = array(
		'column' => 'timestamp',
		'format' => 'Y-m-d H:i:s'
	);

	/**
	 * Rule definitions for validation
	 * @var array
	 */
	protected $_rules = array(
		'subject' => array(
			'not_empty'  => NULL,
			'max_length' => array(255)
		),
		'message' => array(
			'not_empty'  => NULL
		)
	);

	/**
	 * Filter definitions for validation
	 * @var array
	 */
	protected $_filters = array(
		'subject' => array(
			array('HTML::chars')
		),
		'message' => array(
			array('HTML::chars')
		)
	);

	public function is_recipient($user_id = NULL)
	{
		if ( ! isset($user_id))
			$user_id = intval(Auth::instance()->get_user()->id);
		return $user_id == $this->recipient_id;
	}

	public function is_sender($user_id = NULL)
	{
		if ( ! isset($user_id))
			$user_id = intval(Auth::instance()->get_user()->id);
		return $user_id == $this->sender_id;
	}

	public function relative_time($now = NULL)
	{
		if ($now === NULL)
		{
			$now = time();
		}

		$diff = $now - strtotime($this->timestamp);
		$time = array(
			array(60,       'minute'),
			array(3600,     'hour'),
			array(86400,    'day'),
			array(604800,   'week'),
			array(2592000,  'month'),
			array(31104000, 'year')
		);

		for ($i = 5; $i > -1; $i--)
		{
			if ($diff > $time[$i][0])
			{
				$value   = floor($diff/$time[$i][0]);
				$return  = $value.' '.$time[$i][1];
				$return .= ($value == 1) ? '' : 's';
				$return .= ' ago';
				return $return;
			}
		}

		return 'just now';
	}

	public static function count_unread($user_id)
	{
		return DB::select(array(DB::expr('COUNT(*)'),'num_message'))
				->from('messages')
				->where('recipient_id', '=', $user_id)
				->where('read', '=', 0)
				->execute()
				->get('num_message', 0);
	}
} // End Message
