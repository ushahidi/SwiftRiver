<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Auth_Tokens
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
class Model_Auth_Token extends ORM {
    
    /**
	 * Generate a secret user token
	 *
	 * @param $type Type of token
	 * @param $data Free text data to be stored in the token
	 * @param $valid_for Time in seconds the token is valid for
	 * @return Model_Auth_Token
	 */	
	public static function create_token($type, $data, $valid_for = 86400)
	{
		$auth_token = ORM::factory('Auth_Token');
		$auth_token->type = $type;
		$auth_token->data = json_encode($data);
		$auth_token->token = md5(Text::random('alnum', 16).serialize($data));
		$auth_token->created_date = date("Y-m-d H:i:s", time());
		$auth_token->expire_date = date("Y-m-d H:i:s", time() + $valid_for);
		$auth_token->save();
		
		return $auth_token;
	}
    
    /**
	 * Get a token
	 *
	 * @param $token
	 * @param $type
	 * @return mixed
	 */	
	public static function get_token($token, $type, $delete = TRUE)
	{
		$auth_token = ORM::factory("Auth_Token")
		                ->where("token", "=", $token)
		                ->where("type", "=", $type)
		                ->where("expire_date", ">", DB::expr("SYSDATE()"))
		                ->find();

		if ($auth_token->loaded())
		{
			return $auth_token;
		}        
		return FALSE;
	}
	
}

?>