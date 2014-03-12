<?php

/**
 * ISBN Library. Contains is_valid_isbn_10 and is_valid_isbn_13. Functions
 * taken from Wikipedia.
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Libraries
 * @author      Alexander Poon, Khalim Husain
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
	 * Grabs book information from ISBNdb. NOTE: We assume that the ISBN
	 * is an ISBN 13. Returns an array containing a book object and a list of
	 * authors.
	 *
	 * @param   isbn_13
	 *			The ISBN
	 *
	 * @return  object
	 */
	function get_from_isbndb($isbn_13)
	{
		$key = config_item('isbndb_key');
		$url = 'http://isbndb.com/api/v2/json/' . $key . '/book/' . $isbn_13;

		// Get the contents in JSON.
		$json = json_decode(file_get_contents($url));

		if (!$json || isset($json->error))
		{
			return FALSE;
		}

		$data = $json->data[0];
		$isbndb_author_list = $data->author_data;
		$author_list = array();

		// Loop through the list.
		foreach ($isbndb_author_list as $isbndb_author)
		{
			$name = $isbndb_author->name;
			$pos = strpos($name, ', ');

			// Special case if the author name is in the order of Lastname, Firstname
			if ($pos !== FALSE)
			{
				$name = substr($name, $pos + 2) . ' ' . substr($name, 0, $pos);
			}

			// Add this stuff to the back of the author.
			if (isset($authors))
			{
				$authors .= ', ' . $name;
			}
			else
			{
				$authors = $name;
			}

			// Add it in the back of the array.
			array_push($author_list, array(
				'isbn_13' => $isbn_13,
				'name' => $name
			));
		}

		$book = array(
			'isbn_13' => $data->isbn13,
			'title' => $data->title,
			'authors' => $authors,
			'publisher' => $data->publisher_name,
			'edition' => $data->edition_info,
			'msrp' => NULL,
			'year' => NULL,
			'amazon_detail_url' => NULL,
			'amazon_small_image' => NULL,
			'amazon_medium_image' => NULL,
			'amazon_large_image' => NULL
		);

		return array(
			'author_list' => $author_list,
			'book' => $book
		);
	}


	/**
	 * Grabs book information from Amazon. NOTE: We assume that the ISBN
	 * is an ISBN 13. Returns an array containing a book object and a list of
	 * authors.
	 *
	 * @param   isbn_13
	 *			The ISBN
	 *
	 * @return  object
	 */
	function get_info_from_amazon($isbn_13)
	{	
		// We omit author
		$tags = array(
			array(
				'name' => 'title',
				'start' => '<Title>',
				'end' => '</Title>',
				'required' => TRUE
			),
			array(
				'name' => 'publisher',
				'start' => '<Publisher>',
				'end' => '</Publisher>'
			),
			array(
				'name' => 'edition',
				'start' => '<Edition>',
				'end' => '</Edition>'
			),
			array(
				'name' => 'msrp',
				'start' => '<FormattedPrice>',
				'end' => '</FormattedPrice>'
			),
			array(
				'name' => 'year',
				'start' => '<PublicationDate>',
				'end' => '</PublicationDate>'
			),
			array(
				'name' => 'amazon_detail_url',
				'start' => '<DetailPageURL>',
				'end' => '</DetailPageURL>'
			),
			array(
				'name' => 'amazon_small_image',
				'start' => '<SmallImage><URL>',
				'end' => '</URL>'
			),
			array(
				'name' => 'amazon_medium_image',
				'start' => '<MediumImage><URL>',
				'end' => '</URL>'
			),
			array(
				'name' => 'amazon_large_image',
				'start' => '<LargeImage><URL>',
				'end' => '</URL>'
			),
		);
		$timestamp = gmdate("Y-m-d\TH:i:s\Z");
		$timestamp = str_replace(":", "%3A", $timestamp);

		$canonicalQuery = 'AWSAccessKeyId='.config_item('amazon_access_key')
		. '&AssociateTag=' . config_item('amazon_associate_tag')
		. '&IdType=ISBN&ItemId=' . $isbn_13
		. '&Operation=ItemLookup'
		. '&ResponseGroup=Medium&SearchIndex=All'
		. '&Service=AWSECommerceService'
		. '&Timestamp=' . $timestamp;
		
		// These need to be double quotes cause of the \n.
		$stringToSign="GET\n".config_item('amazon_hostname')."\n/onca/xml\n".$canonicalQuery;
		
		$signature = base64_encode(hash_hmac(
			'sha256',
			$stringToSign,
			config_item('amazon_secret_key'),
			true));

		$signature = str_replace(array('+', '='), array('%2B', '%3D'), $signature);

		$signedURL = config_item('amazon_protocol') . config_item('amazon_hostname')
		. '/onca/xml?' . $canonicalQuery . '&Signature=' . $signature;
	
		$result = file_get_contents($signedURL);

		// If there is an error or we couldn't get anything, return FALSE.
		if (!$result || strpos($result, '<Errors>'))
		{
			return FALSE;
		}

		// Loop through each tag.
		foreach ($tags as $tag)
		{
			// Get string positions
			$start_pos = strpos($result, $tag['start']);
			$end_pos = strpos(substr($result, $start_pos), $tag['end']) + $start_pos;

			// If either are not found for whatever reason
			if ($start_pos === FALSE || $end_pos === FALSE)
			{
				// If true, then we bark.
				if (isset($tag['required']))
				{
					$arr = array(
						'status' => array(
							'status' => 'error',
							'message' => 'Could not find tag for ' . $tag['name']
						),
						'data' => array()
					);

					echo json_encode($arr);
					return;
				}
				// Explicitly make it NULL if it will be needed.
				else
				{
					$book[$tag['name']] = NULL;
				}
			}
			else
			{
				// Special cases
				if ($tag['name'] === 'msrp')
				{
					$start_pos += 5;
				}
				else if ($tag['name'] === 'year')
				{
					$end_pos -= 6;
				}

				$start_pos += strlen($tag['start']);

				// Add this into book dictionary.
				$book[$tag['name']] = substr($result, $start_pos, ($end_pos - $start_pos));
			}
		}

		// Get Author(s)
		$author_start_tag = '<Author>';
		$author_end_tag = '</Author>';
		$author_start_tag_length = strlen($author_start_tag);
		$author_end_tag_length = strlen($author_end_tag);
		$author_start_pos = strpos($result, '<ItemAttributes>');
		$author_length = strpos($result, '</ItemAttributes>') - $author_start_pos;

		// Get the first instance of ItemAttributes
		$item_attribute = substr($result, $author_start_pos, $author_length);

		$start_pos = 0;
		$end_pos = 0;

		$author_list = array();

		// Loop through item_attribute, finding all the Authors. Stick them into a string-based list and an array.
		do
		{
			$start_pos = strpos(substr($item_attribute, $end_pos), $author_start_tag);

			if ($start_pos === FALSE)
			{
				break;
			}

			$start_pos += $end_pos;
			$end_pos = strpos(substr($item_attribute, $start_pos), $author_end_tag) + $start_pos;

			$author = substr($item_attribute, $start_pos + $author_start_tag_length, $end_pos - $start_pos - $author_start_tag_length);

			$author_name_pos = strpos($author, ', ');

			// Sppecial case if the author name is in the order of Lastname, Firstname
			if ($author_name_pos !== FALSE)
			{
				$author = substr($author, $author_name_pos + 2) . ' ' . substr($author, 0, $author_name_pos);
			}

			// Add it into the string-based list.
			if (isset($authors))
			{
				$authors .= ', ' . $author;
			}
			else
			{
				$authors = $author;
			}

			// Add it into the author_list
			array_push($author_list, array(
				'isbn_13' => $isbn_13,
				'name' => $author
			));

			$end_pos += $author_end_tag_length;
		} while (1);

		$book['isbn_13'] = $isbn_13;
		$book['authors'] = $authors;

		return array(
			'book' => $book,
			'author_list' => $author_list
		);
	}
}