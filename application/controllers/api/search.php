<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->load->model('User_m');
		$this->load->model('books_m','books');
		$this->load->model('listings_m', 'listings');
		$this->load->library('isbn_lib');
	}

	//fallback function, if no function specified
	function index()
	{
		echo "Index";
	}

	/**
	 * Gets Unique Book Information
	 *
	 * @param  isbn
	 *         The ISBN
	 */
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
