<?php defined('SYSPATH') or die('No direct script access');

/**
 * Welcome controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Welcome extends Controller_Swiftriver {
	
	/**
	 * Whether to automatically render the view
	 * @var bool
	 */
	public $auto_render = TRUE;
	
	
	public function before()
	{
		parent::before();
		
		// TODO
	}
	
	public function action_index()
	{
		$this->template->header->js = View::factory('pages/welcome/js/main');
		$this->template->header->title = __('Welcome');
		$this->template->content = View::factory('pages/welcome/main');
	}
	
	public function action_ajax()
	{
		$this->auto_render = FALSE;
		
		switch ($this->request->method())
		{
			case "POST":
				$keywords = explode(',', $this->request->post('keywords'));
				
				if (empty($keywords))
				{
					echo json_encode(array('status' => 'ERROR'));
					return;
				}
				
				// Trim the keywords and reduce to a string
				$format_keywords = function($a, $b) {
					if ( ! $a)
					{
						return trim($b);
					}
					else
					{
						return $a .= ', '.trim($b);
					}
				};
				$keywords_clean = array_reduce($keywords, $format_keywords);
								
				// Create the river
				$river_name = __('Public River ');
				$river_name_url = __('Public River ').Text::random('alnum', 10);
				$river = Model_River::create_new($river_name, TRUE, $this->user->account, $river_name_url);
				
				// Let plugins populate the channels
				$event_message = array($river, $this->user, $keywords_clean);
				Swiftriver_Event::run('swiftriver.welcome.create_river', $event_message);
				
				if ($river->channel_filters->count_all())
				{
					// Respond with the new river's url
					echo json_encode(array(
						'status' => 'OK',
						'url' => URL::site().$river->account->account_path.'/river/'.$river->river_name_url
					));
				}
				else
				{
					// No channel options added. Not ok.
					echo json_encode(array(
						'status' => 'ERROR',
					));
					$river->delete();
				}
			break;
		}
	}
}
?>
