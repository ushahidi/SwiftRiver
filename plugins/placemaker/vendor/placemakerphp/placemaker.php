<?php

	/***********************************************************************
	PlaceMakerPHP 0.1.2
	A PHP wrapper for the Yahoo Placemaker API.
	By Jeffrey McManus, Platform Associates
	Copyright (c) 2009, Platform Associates LLC
	http://platformassociates.com
	http://blog.jeffreymcmanus.com
	
	This is open-source software; see the enclosed LICENSE.txt for details.
	
	Commercial licensing for this class is available; for information,
	contact us via http://platformassociates.com/contact
	
	See placemaker_test.php for usage examples.
	
	Documentation, such as it is, for the Placemaker API is located here:
	http://developer.yahoo.com/geo/placemaker/
	
	************************************************************************/

	class Placemaker
	{
		var $latitude;
		var $longitude;
		var $name = '';
		var $appid = '';
		var $documentType = 'text/plain';
		
		var $request = 'http://wherein.yahooapis.com/v1/document';
		
		// This version retrieves the first location it finds and tosses the rest (if any).
		// To get an array containing all the locations in a document, use get_all.
		function get($text) {
			// urlencode and concatenate the POST arguments 
			$postargs = 'appid=' . $this->appid . '&documentContent=' . urlencode($text) . '&documentType=' . $this->documentType;

			// Set up CURL
			$session = curl_init($this->request);

			// Tell curl to use HTTP POST
			curl_setopt ($session, CURLOPT_POST, true); 

			// Tell curl that this is the body of the POST
			curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs); 

			// Tell curl not to return headers, but do return the response
			curl_setopt($session, CURLOPT_HEADER, false); 
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

			$response = curl_exec($session); 
			curl_close($session);

			// Now parse the response using PHP SimpleXML
			$xml = simplexml_load_string($response);
			
			$place = new Placemaker();
			if (isset($xml->document->placeDetails->place->name)) {
				$place->name = $xml->document->placeDetails->place->name;
			}
			
			if (isset($xml->document->placeDetails->place->centroid->longitude)) {
				$place->longitude = $xml->document->placeDetails->place->centroid->longitude;
			}
			
			if (isset($xml->document->placeDetails->place->centroid->latitude)) {
				$place->latitude = $xml->document->placeDetails->place->centroid->latitude;
			}
			return $place;
		}
		
		// Extracts all the locations from a document.
		// Retrieves an array of Placemaker objects.
		function get_all($text) {
			$retval = array();
			
			// urlencode and concatenate the POST arguments 
			$postargs = 'appid=' . $this->appid . '&documentContent=' . urlencode($text) . '&documentType=' . $this->documentType;

			// Set up CURL
			$session = curl_init($this->request);

			// Tell curl to use HTTP POST
			curl_setopt ($session, CURLOPT_POST, true); 

			// Tell curl that this is the body of the POST
			curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs); 

			// Tell curl not to return headers, but do return the response
			curl_setopt($session, CURLOPT_HEADER, false); 
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

			$response = curl_exec($session); 
			curl_close($session);
			
			// Now parse the response using PHP SimpleXML
			@$xml = simplexml_load_string($response);

			foreach($xml->document->placeDetails as $pd) {
				$place = new Placemaker();
				$place->name = $pd->place->name;
				$place->longitude = $pd->place->centroid->longitude;
				$place->latitude = $pd->place->centroid->latitude;
				$retval[] = $place;
			}
			return $retval;
		}
	}
?>
