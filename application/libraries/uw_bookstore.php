<?php

/**
 * UW booklook calls.
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author	Khalim Husain
 */

class Uw_bookstore {

	function getCoursesForBook($isbn, $term)
	{
		$baseURL = "https://fortuna.uwaterloo.ca/cgi-bin/cgiwrap/rsic/book/search.html?mv_profile=search_advanced";
		$title = "";
		$author = "";
		$param1 = "&mv_searchspec=";		// Title
		$param2 = "&mv_searchspec=";		// Author
		$param3 = "&mv_searchspec=".$isbn;	// ISBN
		$url = $baseURL.$param1.$param2.$param3;
		
		$html = file_get_contents($url);
		
		$courses = array();

		foreach (preg_split("/((\r?\n)|(\r\n?))/", $html) as $line) {
                	$str = trim($line, " ");
               		if (strpos($str, 'Sorry, no matches were found') !== false) {
                		return FALSE;
			}
        	}
       		
		$wantNextLine = 0; 
        	foreach (preg_split("/((\r?\n)|(\r\n?))/", $html) as $line) {
        	        $str = strip_tags(trim($line, " "));
                	if ($wantNextLine == 1) {
                        	$course = substr($str, 0, strrpos($str, " "));

                        	// Only add course if it doesn't already exist in the array
                        	if (! in_array($course, $courses)) {
                              		array_push($courses, $course);
                       		}
                        $wantNextLine = 0;
                 	}

			// If we found the term, we need to make a note of it for the next iteration
                	if (strpos($str, $term) !== false) {
                        	$wantNextLine = 1;
                	}
       		}

		return $courses;
	}
}
