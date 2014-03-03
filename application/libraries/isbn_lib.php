<?php

/**
 * ISBN Library. Contains is_valid_isbn_10 and is_valid_isbn_13. Functions
 * taken from Wikipedia.
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Libraries
 * @author      Alexander Poon
 */
class Isbn_lib {
	/**
	 * Checks to see if an ISBN is valid.
	 *
	 * @param   n
	 *			The ISBN
	 *
	 * @return  boolean
	 */
	function is_isbn_13_valid($n)
	{
	    if (strlen($n) != 13)
	    	return FALSE;

	    $check = 0;
	    for ($i = 0; $i < 13; $i+=2) $check += substr($n, $i, 1);
	    for ($i = 1; $i < 12; $i+=2) $check += 3 * substr($n, $i, 1);
	    return $check % 10 == 0;
	}

	/**
	 * Checks to see if an ISBN is valid.
	 *
	 * @param   n
	 *			The ISBN
	 *
	 * @return  boolean
	 */
	function is_isbn_10_valid($ISBN10){
	    if(strlen($ISBN10) != 10)
	        return false;
	 
	    $a = 0;
	    for($i = 0; $i < 10; $i++){
	        if ($ISBN10[$i] == "X" || $ISBN10[$i] == "x"){
	            $a += 10*intval(10-$i);
	        } else if (is_numeric($ISBN10[$i])) {
	            $a += intval($ISBN10[$i]) * intval(10-$i);
	        } else {
	            return false;
	        }
	    }
	    return ($a % 11 == 0);
	}

	/**
	 * Grabs book information from ISBNDB. NOTE: We assume that the ISBN
	 * is an ISBN 13
	 *
	 * @param   isbn_13
	 *			The ISBN
	 *
	 * @param   key
	 *			The ISBNDB key.
	 *
	 * @return  object
	 */
	function get_from_isbndb($isbn_13, $key)
	{
		$url = 'http://isbndb.com/api/v2/json/' . $key . '/book/' . $isbn_13;

		// Get the contents in JSON.
		$json = file_get_contents($url);

		return json_decode($json);
	}
}