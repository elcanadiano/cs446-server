<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Listings extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->load->model('books_m','books');
		$this->load->model('listings_m', 'listings');
		$this->load->model('courses_m', 'courses');
		$this->load->library('open_data_api');
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
		$isbn_13 = $this->input->get_post('isbn_13');
		$listing_price = $this->input->get_post('listing_price');
		$condition = $this->input->get_post('condition');
		$is_active = $this->input->get_post('is_active'); // Pass in as 1 or 0.
		$catalog_number = trim($this->input->get_post('catalog_number')); // Pass in as 1 or 0.
		$subject = trim($this->input->get_post('subject')); // Pass in as 1 or 0.
		$comments = $this->input->get_post('comments');

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

		// Make comments NULL if nothing was passed in.
		if (!$comments)
		{
			$comments = NULL;
		}

		if (!$catalog_number || !$subject)
		{
			$course_id = NULL;
		}
		else
		{
			// Attempt to retrieve the course.
			$course = $this->courses->get_course_by_subject_catalog($subject, $catalog_number);

			// If that course does not exist in the database, go into the Open Data API.
			// If a result exists, then insert it to the object. If not, disregard it.
			if (!$course)
			{
				$course = $this->open_data_api->get_course($subject, $catalog_number);

				if ($course)
				{
					$course_id = $course['course_id'];
					$this->courses->insert_obj($course);
				}
				else
				{
					$course_id = NULL;

					$status = array(
						'status' => 'warning',
						'message' => 'Course not found. Record inserted succesfully otherwise.'
					);
				}
			}
			else
			{
				$course_id = $course[0]->course_id;
			}
		}

		// Attempt to insert.
		$insert_obj = array(
			'isbn_13' => $isbn_13,
			'listing_price' => $listing_price,
			'course_id' => $course_id,
			'comments' => $comments,
			'condition' => $condition,
			'is_active' => $is_active
		);

		$id = $this->listings->insert_obj($insert_obj);

		$insert_obj['id'] = $id;

		if (!isset($status))
		{
			$status = array(
				'status' => 'success',
				'message' => 'Record inserted successfully!'
			);
		}

		$arr = array(
			'status' => $status,
			'data' => array(
				'insert_obj' => $insert_obj
			)
		);

		echo json_encode($arr);
	}

	/**
	 * Post a listing to the database.
	 */
	function delete()
	{
		$lid = $this->input->get_post('lid');

		$this->listings->delete($lid);

		$arr = array(
			'status' => array(
				'status' => 'success',
				'message' => 'Listing Succesfully Deleted!'
			),
			'data' => array()
		);
	
		echo json_encode($arr);
	}
}
