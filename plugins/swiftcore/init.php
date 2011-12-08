<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Init for the Swiftcore plugin
 *
 * @package SwiftRiver
 * @author Ushahidi Team
 * @category Plugins
 * @copyright (c) 2008-2011 Ushahidi Inc <htto://www.ushahidi.com>
 */
class Swiftcore_Init {
	
	public function __construct()
	{
		// Register the callback method for the extract_metadata event
		Swiftriver_Event::add('swiftriver.droplet.extract_metadata', array($this, 'extract_metadata'));
		
		
	}
	
	/**
	 * Callback for the "swiftriver.droplet.extract_metadata" event
	 * This method uses the SwiftCore API to extract semantics from 
	 * the droplet
	 */
	public function extract_metadata()
	{
		// Get the droplet
		$droplet = & Swiftriver_Event::$data;
		
		// URL for extracting semantics
		$api_url = Kohana::$config->load('swiftcore.api_url');
		
		// Initialize cURL session
		$ch = curl_init();
		
		// HTTP POST fields and their data
		$post_fields = array(
			'text' => urlencode($droplet['droplet_content'])
		);
		
		// url-ify the fields
		$fields_str = '';
		foreach ($post_fields as $key => $value)
		{
			$fields_str .= $key."=".$value."&";
		}
		rtrim($fields_str, "&");
		
		// cURL options
		$curl_options = array(
			CURLOPT_URL => $api_url,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $fields_str,
			CURLOPT_CONNECTTIMEOUT => 3,
			CURLOPT_HEADER => FALSE,
			CURLOPT_HTTPHEADER => array('Accept: application/json')
		);
		
		// Set options
		curl_setopt_array($ch, $curl_options);
		
		// Execute
		$response = curl_exec($ch);
		
		// Get the response code
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
		if ($response AND $status ==  200)
		{
		    Kohana::$log->add(Log::DEBUG, $response);
		    
			// Convert the response to JSON
			$semantics = get_object_vars(json_decode($response));
			
			$places = array();
			if(array_key_exists('gpe', $semantics)) 
			{			    
			    foreach($semantics['gpe'] as $gpe) 
			    {
			        if($gpe[1])
			        {
			            $places[] = array(
			                'name' => $gpe[0],
			                'latitude' => $gpe[1]->lat,
			                'longitude' => $gpe[1]->lng,
			                'source' => null
			                );
		            }
			    }
			}
			$droplet['places'] = $places;
			
			
			// Get the other semantics and generalize them as "tags"
			$tags = array();
			foreach ($semantics as $key => $entities)
			{
				if ($key != 'gpe' and $key != 'location')
				{
					foreach ($entities as $entity)
					{
						$tags[] = array('tag_name' => $entity, 'tag_type' => $key);
					}
				}
			}
			// Set the tags
			$droplet['tags'] = $tags;
		}
		else
		{
			// Log the cURL error
			Kohana::$log->add(Log::ERROR, "Semantic extraction failed. HTTP Code: :code. Error: :error", 
				array(
					":code" => $status, 
					":error" => curl_error($ch)
			));
		}
		
		// Close session
		curl_close($ch);		
	}	
}

// Initialize the plugin
new Swiftcore_Init;

?>