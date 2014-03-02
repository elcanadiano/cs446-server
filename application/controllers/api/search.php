<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->load->model('User_m');
		$this->load->model('listings_m', 'listings');
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

	// BOOKS
	function search_book($isbn, $limit=20, $offset=0)
	{
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
}
