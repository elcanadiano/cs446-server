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

	// Send back what message is passed in
	function test2()
	{
		$message = $this->input->get('message', TRUE);
		$arr = array(
			'status' => array(
				'status' => 'success',
				'message' => ''
			),
			'data' => array(
				'message' => $message
			)
		);
		echo json_encode($arr);
	}

	function get_unique_book_title($isbn) 
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
                $book = $this->listings->retrieve_unique_book_title($isbn);

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
