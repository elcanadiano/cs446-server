<?php

/**
 * UWwaterloo Open Data API
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author	Khalim Husain
 */

class Open_data_api  {
	$baseURL = "https://api.uwaterloo.ca/v2";       // Current version of Open Data API
	$format = ".json";                              // .json OR .xml format can be returned
	$key = "dacd769dd10cb9e4b195989a2be137c8";      // EasyPeasyAllTheWay@gmail.com Open Data API key
	$filePath = "results/".$term."_".$subject;
        $call =  "/terms/$term/$subject/schedule";
        $decodedResponse = getJSONdata($call);
	
	// Scrape the subjects returned from the UWaterloo Open Data API and returns an array
	// Ensures unique entries
	function scrape_subjects() {
        	$subjects = array();

      		foreach ($decodedResponse["data"] as $info => $value) {
       	        	$item = $value["subject"];

                	if (! in_array($item, $array)) {
                        	array_push($array, $item);
                	}
        	}
	
        	return $subjects;
	}

	// Scrape the response from the UWaterloo Open Data API and return the array
	// This ensures unique course entries for a given subject and term
	function scrape_course_info($decodedResponse) {
        	$array = array();
       		foreach ($decodedResponse["data"] as $info => $value) {
                	$item = $value["subject"]." ".$value["catalog_number"]." - ".$value["title"];

                	if (! in_array($item, $array)) {
                        	array_push($array, $item);
               		}	
        	}	
        
		return $array;
	}

	function get_json_data($call) {
        $properURL = $GLOBALS['baseURL'].$call.$GLOBALS['format']."?key=".$GLOBALS['key'];

        $response = file_get_contents($properURL);
        $decodedResponse = json_decode($response, true);

        return $decodedResponse;
	}

}

$this-><WHATEVERYOuWANT>
