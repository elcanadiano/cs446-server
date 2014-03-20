<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->load->model('books_m','books');
		$this->load->model('listings_m', 'listings');
		$this->load->model('authors_m', 'authors');
		$this->load->library('isbn_lib');
		$this->load->library('uw_bookstore');
	}

	/**
	 * Fallback function if no method is provided. This should always
	 * error out because this should not ever be called.
	 */
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
		// library and then insert it into the database
		if (!$book)
		{
			// Get the Amazon Book Info using the library.
			$data = $this->isbn_lib->get_info_from_amazon($isbn);

			// If there's an error in retrieval, fall back to ISBNdb.
			if (!$data)
			{
				// Get the author list and the book information via. the ISBNdb API.
				$data = $this->isbn_lib->get_from_isbndb($isbn);
				
				// If you can't get ISBNdb info, bark.
				if (!$data)
				{
					$arr = array(
						'status' => array(
							'status' => 'error',
							'message' => 'Could not retrieve from the ISBN API.'
						),
						'data' => array()
					);

					echo json_encode($arr);
					return;
				}
			}			

			// Now let's insert the book and the list of authors into the database.
			$this->books->insert_obj($data['book']);
			$this->authors->insert_batch($data['author_list']);

			$book = $data['book'];
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

	/**
	 * Returns a JSON encoded array of listings with a given ISBN.
	 *
	 * @param  isbn
	 *         The ISBN
	 *
	 * @param  limit
	 *         The amount of listings you want to see.
	 *
	 * @param  offset
	 *         The amount of listings we skip (or offset).
	 */
	function get_listings_by_book($isbn, $limit=20, $offset=0)
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

	/**
	 * Returns a JSON encoded array of listings with a given ISBN.
	 *
	 * @param  subject
	 *         The course subject (ie. CS, MATH, ARTS, ENV, ECE, etc.)
	 *
	 * @param  catalog_number
	 *         The catalog number (ie. 101, 202, 344, 341, etc.)
	 *
	 * @param  limit
	 *         The amount of listings you want to see.
	 *
	 * @param  offset
	 *         The amount of listings we skip (or offset).
	 */
	function get_listings_by_course($subject, $catalog_number, $limit=20, $offset=0)
	{
		// Retrieves listings with a given ISBN
		$listings = $this->listings->retrieve_listings_by_course($subject, $catalog_number, $limit, $offset);

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

	/**
	 * Returns a JSON encoded array of all courses that a book is used in a term
	 *
	 * DO NOT USE, NOT SUPPORTED
	 */
	function get_courses_for_book($isbn, $term) {
		$courses = $this->uw_bookstore->getCoursesForBook($isbn, $term);
		
		$arr = array();
		
		if ($courses == FALSE) {
			$arr = array(
				'status' => array(
					'status' => 'error',
					'message' => 'No courses for this isbn in this term'
				),
				'data' => array()
			);
		} else {
			$arr = array(
				'status' => array(
					'status' => 'success',
					'message' => ''
				),
				'data' => array(
					'courses' => $courses
				)
			);
		}

		echo json_encode($arr);
	}

	/**
	 * Returns a JSON encoded array of all listings
	 *
	 * DO NOT USE, NOT SUPPORTED
	 */ 
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

	/**
	 * Returns a JSON encoded array of all books
	 *
	 * DO NOT USE, NOT SUPPORTED
	 */
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
