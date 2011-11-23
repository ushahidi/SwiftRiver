<?php defined('SYSPATH') or die('No direct script access');

class Util_Channel_Filter {
	

	/** Get keywords array from channel filter options
	 * //TODO: Improve this to not query the DB every time.
	 *
	 * @return array
	 */
	public static function get_keywords() {
		$options = array();
		$filter_options = Model_Channel_Filter::get_channel_filter_options('twitter');
		foreach($filter_options as $filter_option) {
			$options[] = array($filter_option->key => $filter_option->value);
		}
		$keywords = array();
		foreach($options as $option) {
			if(isset($option['keyword'])) {
			    $keywords[] = $option['keyword'];
			}
		}
		return $keywords;
	}
}
?>
