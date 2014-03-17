<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Listings extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->load->model('books_m','books');
		$this->load->model('listings_m', 'listings');
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
	 * Post a listing to the database.
	 */
	function post()
	{
		$isbn_13 = $this->input->post('isbn_13');
		$listing_price = $this->input->post('listing_price');
		$condition = $this->input->post('condition');
		$is_active = $this->input->post('is_active'); // Pass in as 1 or 0.

		if (!$isbn_13)
		{
			$arr = array(
				'status' => array(
					'status' => 'error',
					'message' => 'You must pass in the ISBN.'
				),
				'data' => array()
			);

			echo json_encode($arr);
			return;
		}

		if (!$listing_price)
		{
			$arr = array(
				'status' => array(
					'status' => 'error',
					'message' => 'You must pass in the listing price.'
				),
				'data' => array()
			);

			echo json_encode($arr);
			return;
		}

		// Attempt to insert.
		$this->listings->insert($isbn_13, $listing_price, $condition, $is_active);

		$arr = array(
			'status' => array(
				'status' => 'success',
				'message' => 'Record inserted successfully!'
			),
			'data' => array()
		);

		echo json_encode($arr);
	}
}
