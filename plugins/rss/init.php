<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Init for the RSS plugin
 *
 * @package SwiftRiver
 * @author Ushahidi Team
 * @category Plugins
 * @copyright (c) 2008-2011 Ushahidi Inc <htto://www.ushahidi.com>
 */
class Rss_Init {

	public function __construct() 
	{
	    // Register as a crawler
	    Swiftriver_Crawlers::register('rss', array(new Swiftriver_Crawler_Rss(), 'crawl'));

		// Hook into the settings page
		Swiftriver_Event::add('swiftriver.settings.nav', array($this, 'settings_nav'));
		
		// Hook into welcome page new river creation
		Swiftriver_Event::add('swiftriver.welcome.create_river', array($this, 'add_chanel_options'));
	    
		// Validate the channel option data before it's saved to the DB
		Swiftriver_Event::add('swiftriver.channel.option.pre_save', array($this, 'validate'));
		
		// Extract feed urls from an OPML file upload
		Swiftriver_Event::add('swiftriver.channel.option.file', array($this, 'opml_import'));
	}
	
	/**
	 * Add channel options to a river created via the welcome page
	 * 
	 * @return	void
	 */
	public function add_chanel_options()
	{
		list($river, $user, $keywords) = Swiftriver_Event::$data;
		
		// Get starter URLs from site settings
		$urls = Model_Setting::get_setting(RSS_Util::SETTING_KEY);
		if ( ! $urls) 
		{
			return;
		}
		$urls = json_decode($urls);
		if (empty($urls))
		{
			return;
		}
		
		// Add a rss channel
		$channel_filter = $river->create_channel_filter('rss', $user->id, TRUE);
		$channel_filter->add_option('keyword', array(
			'label' => 'Keyword',
			'type' => 'text',
			'value' => trim($keywords)
		));
		
		foreach ($urls as $url => $title)
		{
			$channel_filter->add_option('url', array(
				'label' => $title,
				'type' => 'text',
				'value' => $url
			));
		}
	}
	
	
	/**
	 * Display link in the settings navigation
	 * 
	 * @return	void
	 */
	public function settings_nav()
	{
		$active = Swiftriver_Event::$data;
		
		echo '<li '.(($active == 'rsswelcome') ? 'class="active"' : '').'>'.
			HTML::anchor(URL::site('settings/rsswelcome'), __('RSS Starter URLs')).
			'</li>';
	}


	/**
	 * Call back method for swiftriver.channel.option.pre_save to validate channel settings
	 */
	public function validate()
	{
		// Get the event data
		$option_data = & Swiftriver_Event::$data;
		
		
		if ( ! (isset($option_data['channel']) AND $option_data['channel'] == 'rss'))
			return;
			
			$url = $option_data['value'];

			if ( ! ($feed = RSS_Util::validate_feed_url($url)))
				throw new SwiftRiver_Exception_ChannelOption('Invalid URL');

			$option_data['value'] = $feed['value'];
			$option_data['title'] = $feed['title'];
	}
	
	/**
	 * Extract urls from an opml file
	 */
	public function opml_import()
	{
		// Get the event data
		$file = & Swiftriver_Event::$data;
		
		if ($file['key'] != 'opml_import')
			return;
			
		if ( ! Upload::type($file, array('xml')))
			throw new SwiftRiver_Exception_ChannelOption('Invalid file type');
			
		// Begin processing the feed
		$importer = new Swiftriver_OPML_Import();
		$importer->init($file['tmp_name']);
		
		// Get the feed entries
		if ( ! isset($file['option_data']))
		{
			$file['option_data'] = array();
		}
	
		foreach ($importer->get_feeds() as $feed)
		{
			$file['option_data'][] = array(
				'key' => 'url',
				'value' => $feed['url'],
				'title' => $feed['title']
			);
		}
	}
}

new Rss_Init;

?>