<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
Class Listings_m extends CI_Model
{
	/**
	 * Attempt to retrieve all listings by book, and get $lim values offsetting from $offset.
	 *
	 * @param   isbn
	 *			The ISBN
	 *
	 * @param   lim
	 *			The limit (for SQL). That is, get $lim records.
	 * 
	 * @param   offset
	 *			The offset (for SQL)
	 *
	 * @return  object
	 */
	function retrieve_listings_by_isbn($isbn, $lim=20, $offset=0)
	{
		$where = array(
			'l.isbn_13' => $isbn
		);

		$query = $this->db->select('l.id, l.isbn_13, b.title book_title, b.authors,'
			. ' l.listing_price, c.subject, c.catalog_number, c.title course_title,'
			. ' l.comments, l.condition, l.is_active')
			->from('listings l')
			->join('books b', 'b.isbn_13 = l.isbn_13', 'inner')
			->join('courses c', 'l.course_id = c.course_id', 'left')
			->where($where)
			->limit($lim, $offset);

		return $query->get()->result();
	}

	/**
	 * Attempt to retrieve all listings by course, and get $lim values offsetting from $offset.
	 *
	 * @param   subject
	 *			The ISBN
	 *
	 * @param   catalog_number
	 *			The ISBN
	 *
	 * @param   lim
	 *			The limit (for SQL). That is, get $lim records.
	 * 
	 * @param   offset
	 *			The offset (for SQL)
	 *
	 * @return  object
	 */
	function retrieve_listings_by_course($subject, $catalog_number, $lim=20, $offset=0)
	{
		$where = array(
			'subject' => $subject,
			'catalog_number' => $catalog_number
		);

		$query = $this->db->select('l.id, l.isbn_13, b.title book_title, b.authors,'
			. ' l.listing_price, c.subject, c.catalog_number, c.title course_title,'
			. ' l.comments, l.condition, l.is_active')
			->from('listings l')
			->join('books b', 'b.isbn_13 = l.isbn_13', 'inner')
			->join('courses c', 'l.course_id = c.course_id', 'inner')
			->where($where)
			->limit($lim, $offset);

		return $query->get()->result();
	}

	/**
	 * Retrieves all listings from the database
 	 */
	function retrieve_all_listings() {
		$query = $this->db->get('listings');

		return $query->result();
	}

	/**
	 * Inserts a new listing into the database.
	 */
	function insert($isbn_13, $listing_price, $course_id=NULL, $comments=NULL, $condition=0, $is_active=FALSE)
	{
		$obj = array(
			'isbn_13' => $isbn_13,
			'listing_price' => $listing_price,
			'course_id' => $course_id,
			'comments' => $comments,
			'condition' => $condition,
			'is_active' => $is_active
		);

		$this->db->insert('listings', $obj);

		return $this->db->insert_id();
	}

	/**
	 * Inserts a new listing object into the database.
	 */
	function insert_obj($obj)
	{
		$this->db->insert('listings', $obj);

		return $this->db->insert_id();
	}
}
