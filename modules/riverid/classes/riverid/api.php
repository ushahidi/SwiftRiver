<?php defined('SYSPATH') OR die('No direct script access');

/**
 * RiverID API
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Swiftriver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Inits
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class RiverID_API {
    
    protected $api_endpoint = '';
    
    protected static $singleton;
    
    public static function factory()
    {
        if ( ! self::$singleton) {
            self::$singleton = new RiverID_API(Kohana::$config->load('auth.api_endpoint'));
        }
        return self::$singleton;
    }
    
    
	private function __construct($api_endpoint)
	{
	    $this->api_endpoint = $api_endpoint;
	}    
    
    /**
	 * Checks if an email is registered.
	 *
	 * @param   string   email
	 * @return  boolean
	 */
    public function is_registered($email) 
    {
        $api_url = $this->api_endpoint.'/registered';
        
        $response = $this->__call_api($api_url, array('email' => $email));
        
        if ($response and $response->success and $response->response)
            return TRUE;
        
        return FALSE;
    }
    
	/**
	 * Logs a user in.
	 *
	 * @param   string   email
	 * @param   string   password
	 * @return  array
	 */    
    public function signin($email, $password)
    {
        $api_url = $this->api_endpoint.'/signin';
        
        $response = $this->__call_api($api_url, array('email' => $email, 'password' => $password));
        
        if ($response and $response->success)
            return array('status' => TRUE, 'session_id' => $response->response->session_id, 'user_id' => $response->response->user_id);
        if ($response and ! $response->success)
            return array('status' => FALSE, 'error' => $response->error);

        
        return array('status' => FALSE, 'error' => __('Unknown error'));
    }
    
	/**
	 * Change password
	 *
	 * @param   string   email
	 * @param   string   oldpassword
	 * @param   string   newpassword	 
	 * @return  array
	 */    
    public function changepassword($email, $oldpassword, $newpassword)
    {
        $api_url = $this->api_endpoint.'/changepassword';
        
        $response = $this->__call_api($api_url, array('email' => $email, 
                                                      'oldpassword' => $oldpassword,
                                                      'newpassword' => $newpassword
                                                      ));                                                                                                      
       
        if ($response and $response->success)
            return array('status' => TRUE);
        if ($response and ! $response->success)
            return array('status' => FALSE, 'error' => $response->error);

        
        return array('status' => FALSE, 'error' => __('Unknown error'));

    }
    
    /**
	 * Request password via email
	 *
	 * @param   string   email
	 * @param   string   mailbody
	 * @return  array
	 */
    public function requestpassword($email, $mailbody)
    {
        $api_url = $this->api_endpoint.'/requestpassword';
        
        $response = $this->__call_api($api_url, array('email' => $email, 'mailbody' => $mailbody));
       
        if ($response and $response->success)
            return array('status' => TRUE);
        if ($response and ! $response->success)
            return array('status' => FALSE, 'error' => $response->error);

        
        return array('status' => FALSE, 'error' => __('Unknown error'));        
    }
    
    /**
	 * Set password via a user token
	 *
	 * @param   string   email
	 * @param   string   mailbody
	 * @return  array
	 */    
    public function setpassword($email, $token, $password)
    {
        $api_url = $this->api_endpoint.'/setpassword';
        
        $response = $this->__call_api($api_url, array('email' => $email, 'token' => $token, 'password' => $password));
        
        if ($response and $response->success)
            return array('status' => TRUE);
        if ($response and ! $response->success)
            return array('status' => FALSE, 'error' => $response->error);

        
        return array('status' => FALSE, 'error' => __('Unknown error'));                
    }
    
    /**
	 * Request an email address change
	 *
	 * @param   string   oldemail
	 * @param   string   newemail
	 * @param   string   password
	 * @param   string   mailbody
	 * @return  array
	 */        
    public function changeemail($oldemail, $newemail, $password, $mailbody)
    {
        $api_url = $this->api_endpoint.'/changeemail';
        
        $response = $this->__call_api($api_url, array('oldemail' => $oldemail, 'newemail' => $newemail, 'password' => $password, 'mailbody' => $mailbody));
        
        if ($response and $response->success)
            return array('status' => TRUE);
        if ($response and ! $response->success)
            return array('status' => FALSE, 'error' => $response->error);

        
        return array('status' => FALSE, 'error' => __('Unknown error'));                        
    }

    /**
	 * Confirm an email address change via user token
	 *
	 * @param   string   email
	 * @param   string   token
	 * @return  array
	 */            
    public function confirmemail($email, $token)
    {
        $api_url = $this->api_endpoint.'/confirmemail';
        
        $response = $this->__call_api($api_url, array('email' => $email, 'token' => $token));
        
        if ($response and $response->success)
            return array('status' => TRUE);
        if ($response and ! $response->success)
            return array('status' => FALSE, 'error' => $response->error);

        
        return array('status' => FALSE, 'error' => __('Unknown error'));                                
    }
    
    /**
	 * Send HTTP request to the api endpoint
	 *
	 * @param   string   api_url
	 * @param   array    params
	 * @return  mixed    The response or false in case of failure
	 */
    private function __call_api($api_url, $params) {
                
        $curl_options = array(
			CURLOPT_URL => $api_url,
			CURLOPT_FAILONERROR => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_POST => TRUE,
			CURLOPT_POSTFIELDS => $params,
			CURLOPT_CONNECTTIMEOUT => 60,
			
			// FIXME: Verification must be done in production
			CURLOPT_SSL_VERIFYPEER => FALSE
		);
		
		$ch = curl_init($this->api_endpoint);
		curl_setopt_array($ch, $curl_options);
		$response = curl_exec($ch);		

		if (! $response) {
		    Kohana::$log->add(Log::ERROR, "RiverID api call failed. :error", array('error' => curl_error($ch)));		    
		}
		else
		{
		    $response = json_decode($response);
		}
		
		curl_close($ch);

		return $response;
    }
    
}