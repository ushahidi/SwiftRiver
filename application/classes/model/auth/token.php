<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Auth_Tokens
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Auth_Token extends ORM {
    
    /**
	 * Generate a secret user token
	 *
	 * @param $email
	 * @param $type
	 * @return Model_Auth_Token
	 */	
    public static function create_token($email, $type)
    {
        $auth_token = ORM::factory('auth_token');
        $auth_token->email = $email;
        $auth_token->token = Text::random('alnum', 16);
        $auth_token->type = $type;
        $auth_token->created_date = date("Y-m-d H:i:s", time());
        //Expire in 24 hours
        $auth_token->expire_date = date("Y-m-d H:i:s", time()+86400);
        $auth_token->save();
        
        return $auth_token;
    }
    
    /**
	 * Check if a token is valid
	 *
	 * @param $email
	 * @param $type
	 * @return boolean
	 */	
    public static function is_valid_token($email, $token, $type, $delete = TRUE)
    {
        $auth_token = ORM::factory("auth_token")
                        ->where("token", "=", $token)
                        ->where("type", "=", $type)
                        ->where("email", "=", $email)
                        ->where("expire_date", ">", DB::expr("SYSDATE()"))
                        ->find();
        $ret = FALSE;
        if ($auth_token->loaded())
        {
            $ret = TRUE;
            $auth_token->delete();
        }        
        return $ret;
    }
}

?>