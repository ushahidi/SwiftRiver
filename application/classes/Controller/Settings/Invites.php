<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Invites Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Controller_Settings_Invites extends Controller_Settings_Main {
	
	
	/**
	 * List all the Plugins
	 *
	 * @param	string $page - page uri
	 * @return	void
	 */
	public function action_index()
	{
		if ($this->request->post())
		{
			$this->auto_render = FALSE;
			
			$post = Validation::factory($_FILES)
					->rule('file', 'Upload::not_empty')
					->rule('file', 'Upload::type', array(':value', array('txt')))
					->rule('file', 'Upload::valid');
			if ( ! $post->check())
			{
				$this->response->status(400);
				$this->response->headers('Content-Type', 'application/json');
				echo json_encode(array('error' => __("Invalid file")));
				return;
			}
			
			$file = $_FILES['file'];
			$file_contents = file_get_contents($file['tmp_name']);
			$errors = array();
			foreach (preg_split("/(\r?\n)/", $file_contents) as $line) {
				$email = trim($line);
				$messages = Model_User::new_user($email, $this->riverid_auth, TRUE);

				// Display the messages
				if (isset($messages['errors']))
				{
					$errors[] = $email.' - '.implode(" ",$messages['errors']);
				}
			}
			
			$ret = array();
			if ( ! empty($errors))
			{
				$ret['status_ok'] = FALSE;
				$ret['errors'] = $errors;
			}
			if (isset($messages['messages']))
			{
				$ret['status_ok'] = TRUE;
			}
			echo json_encode($ret);
			return;
		}
		
		$this->template->header->title = __('Invites');
		$this->settings_content = View::factory('pages/settings/invites');
		$this->active = 'invites';
	}
}
