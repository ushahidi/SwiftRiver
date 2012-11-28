<?php defined('SYSPATH') or die('No direct script access.');

class HTML extends Kohana_HTML {
	
	/**
	 * Override Kohana's default html helper to point static css files 
	 * to a CDN resource
	 *
	 * @param   string   file name
	 * @param   array    default attributes
	 * @param   mixed    protocol to pass to URL::base()
	 * @param   boolean  include the index page
	 * @return  string
	 * @uses    URL::base
	 * @uses    HTML::attributes
	 */
	public static function style($file, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		$file = Swiftriver::get_cdn_url($file);
		return parent::style($file, $attributes, $protocol, $index);
	}

	/**
	 * Override Kohana's default html helper to point static css files 
	 * to a CDN resource
	 *
	 * @param   string   file name
	 * @param   array    default attributes
	 * @param   mixed    protocol to pass to URL::base()
	 * @param   boolean  include the index page
	 * @return  string
	 * @uses    URL::base
	 * @uses    HTML::attributes
	 */
	public static function script($file, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		$file = Swiftriver::get_cdn_url($file);
		return parent::script($file, $attributes, $protocol, $index);
	}
	
	/**
	 * Creates a image link.
	 *
	 *     echo HTML::image('media/img/logo.png', array('alt' => 'My Company'));
	 *
	 * @param   string   file name
	 * @param   array    default attributes
	 * @param   mixed    protocol to pass to URL::base()
	 * @param   boolean  include the index page
	 * @return  string
	 * @uses    URL::base
	 * @uses    HTML::attributes
	 */
	public static function image($file, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		$file = Swiftriver::get_cdn_url($file);
		return parent::image($file, $attributes, $protocol, $index);
	}
}