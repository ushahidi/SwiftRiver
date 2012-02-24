<?php defined('SYSPATH') or die('No direct script access');
/**
 * Reads an OPML file and fetches the RSS feed URLs. The RSS URLs 
 * are contained in the <outline> elements
 *
 * @author      Ushahidi Dev Team
 * @package     Swiftriver - https://github.com/ushahidi/Swiftriver_v2
 * @category    Libraries
 * @copyright   (c) 2012 Ushahidi Inc <http://www.ushahidi.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Swiftriver_OPML_Import {
	
	/**
	 * XML representation of the OMPL
	 * @var SimpleXMLElement
	 */
	private $xml;

	/**
	 * DOM representation of the OPML XML
	 * @var DOMDocument
	 */
	private $dom;

	/**
	 * List of feeds fetched from the OMPL file
	 * @var array
	 */
	private $feeds = array();

	/**
	 * Constructor. Prepares the system for processing an OPML file
	 */
	public function __construct()
	{
		$this->dom = new DOMDocument();
	}

	/**
	 * @param string $ompl_file Name of the OPML file
	 */
	public function init($opml_file_name)
	{
		try
		{
			// Load the OPML
			$this->dom->load($opml_file_name);

			// Get the body node
			$body = $this->dom->getElementsByTagName("body");

			// Create SimpleXML object from the body node
			$this->xml = simplexml_import_dom($body->item(0));

			$this->_traverse_xml();

			return TRUE;
		}
		catch (Kohana_Exception $e)
		{
			// Get the errors
			$errors = array();
			foreach (libxml_get_errors() as $error)
			{
				$errors[] = $error->code.": ".$error->message;
			}

			// Log the error
			Kohana::$log->add(Log::ERROR, implde("\n", $errors))
			return FALSE;
		}
		
	}

	/**
	 * Traverses the XML tree and populates the feeds array
	 */
	private function _traverse_xml()
	{
		foreach ($this->xml->children() as $feed_entry)
		{
			// Get the attriutes object of the <outline> item
			$attributes = $feed_entry->attributes();

			// Fetch the title and URL attributes
			$this->feeds[] = array(
				'title' => (string) $attributes->title,
				'url' => (string) $attributes->xmlUrl
			);
		}
	}

	/**
	 * Gets the feeds specified in the OMPL file
	 *
	 * @return array
	 */
	public function get_feeds()
	{
		return $this->feeds;
	}
}
?>