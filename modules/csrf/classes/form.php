<?php defined('SYSPATH') or die('No direct script access');

/**
 * Implements a form with a CSRF token. The token must be
 * verified by the controller that processes the form. The token
 * is a protection mechanism for CSRF attacks
 * 
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 *
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    CSRF - http://github.com/ushahidi/Swiftriver_v2
 * @category   Helpers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL) 
 */

class Form extends Kohana_Form {
	
	/**
	 * Generates an opening HTML form tag.
	 *
	 *     // Form will submit back to the current page using POST
	 *     echo Form::open();
	 *
	 *     // Form will submit to 'search' using GET
	 *     echo Form::open('search', array('method' => 'get'));
	 *
	 *     // When "file" inputs are present, you must include the "enctype"
	 *     echo Form::open(NULL, array('enctype' => 'multipart/form-data'));
	 *
	 * @param   mixed   form action, defaults to the current request URI, or [Request] class to use
	 * @param   array   html attributes
	 * @return  string
	 * @uses    Request::instance
	 * @uses    URL::site
	 * @uses    HTML::attributes
	 */
	public static function open($action = NULL, array $attributes = NULL)
	{
		if ($action instanceof Request)
		{
			// Use the current URI
			$action = $action->uri();
		}

		if ( ! $action)
		{
			// Allow empty form actions (submits back to the current url).
			$action = '';
		}
		elseif (strpos($action, '://') === FALSE)
		{
			// Make the URI absolute
			$action = URL::site($action);
		}

		// Add the form action to the attributes
		$attributes['action'] = $action;

		// Only accept the default character set
		$attributes['accept-charset'] = Kohana::$charset;

		if ( ! isset($attributes['method']))
		{
			// Use POST method
			$attributes['method'] = 'post';
		}

		// Only render the CSRF field when the POST method is used
		$hidden_csrf_field = ($attributes['method'] == 'post')
		    ? self::hidden('form_auth_id', CSRF::token())
		    : '';

		return '<form'.HTML::attributes($attributes).'>'
		    . $hidden_csrf_field;
	}
}

?>