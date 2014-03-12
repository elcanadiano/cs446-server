<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->load->model('books_m','books');
		$this->load->model('listings_m', 'listings');
		$this->load->model('authors_m', 'authors');
		$this->load->library('isbn_lib');
		$this->load->library('amazon_lib');
	}

	//fallback function, if no function specified
	function index()
	{
		$arr = array(
			'status' => array(
				'status' => 'error',
				'message' => 'Invalid function.'
			),
			'data' => array()
		);
	}

	/**
	 * Gets Unique Book Information
	 *
	 * @param  isbn
	 *         The ISBN
	 */
	function get_book_amazon($isbn)
	{
		// Validate. If ISBN-10, change to ISBN-13
		if (!$this->isbn_lib->is_isbn_13_valid($isbn))
		{
			if($this->isbn_lib->is_isbn_10_valid($isbn))
			{
				$isbn = str_replace(array('x', 'X'), '', $isbn);
				$isbn = '978' . $isbn;
			}
			else
			{
				$arr = array(
					'status' => array(
						'status' => 'error',
						'message' => 'Invalid ISBN'
					),
					'data' => array()
				);

				echo json_encode($arr);
				return;
			}
		}
		
		// Retrieves book title of a given ISBN
		$book = $this->books->retrieve_unique_book($isbn);

		// If there is no such book in our database, retrieve it from a third-party library and then insert it into the database.
		if (!$book)
		{
			$amazon = $this->amazon_lib->get_info_from_amazon($isbn);

			// If there's an error in retrieval.
			if (!$amazon)
			{
				$arr = array(
					'status' => array(
						'status' => 'error',
						'message' => 'An error occured with retrieving books from the Amazon API.'
					),
					'data' => array()
				);

				echo json_encode($arr);
				return;
			}

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

			// Loop through each tag.
			foreach ($tags as $tag)
			{
				// Get string positions
				$start_pos = strpos($amazon, $tag['start']);
				$end_pos = strpos(substr($amazon, $start_pos), $tag['end']) + $start_pos;

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
					$book[$tag['name']] = substr($amazon, $start_pos, ($end_pos - $start_pos));
				}
			}

			// Get Author(s)
			$author_start_tag = '<Author>';
			$author_end_tag = '</Author>';
			$author_start_tag_length = strlen($author_start_tag);
			$author_end_tag_length = strlen($author_end_tag);
			$author_start_pos = strpos($amazon, '<ItemAttributes>');
			$author_length = strpos($amazon, '</ItemAttributes>') - $author_start_pos;

			// Get the first instance of ItemAttributes
			$item_attribute = substr($amazon, $author_start_pos, $author_length);

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
					'isbn_13' => $isbn,
					'name' => $author
				));

				$end_pos += $author_end_tag_length;
			} while (1);

			$book['isbn_13'] = $isbn;
			$book['authors'] = $authors;

			// Insert into DB.
			$this->books->insert_obj($book);
			$this->authors->insert_batch($isbn, $author_list);

			//TODO: Add book to database
		} // end if(!book)

		$arr = array(
			'status' => array(
				'status' => 'success',
				'message' => ''
			),
			'data' => array(
				'book' => $book
			)
		);
		
		echo json_encode($arr);
	}

	function get_book($isbn) 
	{
		// Validate ISBN. If ISBN-10, change to ISBN-13
		if (!$this->isbn_lib->is_isbn_13_valid($isbn))
		{
			if($this->isbn_lib->is_isbn_10_valid($isbn))
			{
				$isbn = str_replace(array('x', 'X'), '', $isbn);
				$isbn = '978' . $isbn;
			}
			else
			{
				$arr = array(
					'status' => array(
						'status' => 'error',
						'message' => 'Invalid ISBN'
					),
					'data' => array()
				);

				echo json_encode($arr);
				return;
			}
		}
		// Retrieves book title of a given ISBN
		$book = $this->books->retrieve_unique_book($isbn);

		// If there is no such book in our database, retrieve it from a third-party
		// library and then insert it into the database.
		if (!$book)
		{
			$key = $this->config->item('isbndb_key');
			$isbndb_book = $this->isbn_lib->get_from_isbndb($isbn, $key)->data[0];
			
			$isbn_13 = $isbndb_book->isbn13;
			$title = $isbndb_book->title;
			$author = 'Pilkey, Dav';
			$publisher = $isbndb_book->publisher_name;

			// TODO: implement the other stuff.

			// And this is the book information!
			$book = array(
				'isbn_13' => $isbn_13,
				'title' => $title,
				'author' => $author,
				'publisher' => $publisher,
				'edition' => NULL,
				'msrp' => NULL,
				'year' => NULL
			);

			// Now let's insert the book.
			$this->books->insert($isbn_13, $title, $author, $publisher);
		}

		$arr = array(
			'status' => array(
				'status' => 'success',
				'message' => ''
			),
			'data' => array(
				'book' => $book
			)
		);

		echo json_encode($arr);
	}

	// Returns a JSON encoded array of books with a given ISBN
	function search_book($isbn, $limit=20, $offset=0)
	{
		// Validate ISBN. If ISBN-10, change to ISBN-13
		if (!$this->isbn_lib->is_isbn_13_valid($isbn))
		{
			if($this->isbn_lib->is_isbn_10_valid($isbn))
			{
				$isbn = str_replace(array('x', 'X'), '', $isbn);

				$isbn = '978' . $isbn;
			}
			else
			{
				$arr = array(
					'status' => array(
						'status' => 'error',
						'message' => 'Invalid ISBN'
					),
					'data' => array()
				);

				echo json_encode($arr);
				return;
			}
		}

		// Retrieves listings with a given ISBN
		$listings = $this->listings->retrieve_listings_by_isbn($isbn, $limit, $offset);

		$arr = array(
			'status' => array(
				'status' => 'success',
				'message' => ''
			),
			'data' => array(
				'listings' => $listings
			)
		);

		echo json_encode($arr);
	}

	// Returns a JSON encoded array of all listings
	function get_all_listings()
	{
		$listings = $this->listings->retrieve_all_listings();
		
		$arr = array(
			'status' => array(
				'status' => 'success',
				'message' => ''
			),
			'data' => array(
				'listings' => $listings
			)
		);

		echo json_encode($arr);
	}

	// Returns a JSON encoded array of all books
	function get_all_books()
	{
		$books = $this->books->retrieve();
		
		$arr = array(
			'status' => array(
				'status' => 'success',
				'message' => ''
			),
			'data' => array(
				'books' => $books
			)
		);
	
		echo json_encode($arr);
	}
}
