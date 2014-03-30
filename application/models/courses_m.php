<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
Class Courses_m extends CI_Model
{
	/**
	 * Attempt to retrieve all listings by book, and get $lim values offsetting from $offset.
	 *
	 * @param   subject
	 *			The Subject (ie. CS, MATH, ECON, ECE, etc.)
	 *
	 * @param   catalog_number
	 *			The course number (ie. 300, 370, 341, 101)
	 */
	function get_course_by_subject_catalog($subject, $catalog_number)
	{
		$where = array(
			'subject' => $subject,
			'catalog_number' => $catalog_number
		);

		$query = $this->db->select('course_id, subject, catalog_number, title')
			->from('courses')
			->where($where)
			->limit(1);

		return $query->get()->result();
	}

	/**
	 * Retrieves all listings from the database
 	 */
	function retrieve_all_courses() {
		$query = $this->db->select('course_id, subject, catalog_number, title')
			->from('courses')
			->order_by('subject, catalog_number');

		return $query->result();
	}

	/**
	 * Constructs and inserts a new course object into the database.
	 */
	function insert($course_id, $subject, $catalog_number, $title)
	{
		$obj = array(
			'course_id' => $course_id,
			'subject' => $subject,
			'catalog_number' => $catalog_number,
			'title' => $title
		);

		$this->db->insert('courses', $obj);

		return TRUE;
	}

	/**
	 * Inserts a new course object into the database.
	 */
	function insert_obj($obj)
	{
		$this->db->insert('courses', $obj);

		return TRUE;
	}
}
