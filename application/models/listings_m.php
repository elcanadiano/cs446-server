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

		$query = $this->db->select('l.id, l.isbn_13, b.title, b.author, l.listing_price, l.condition, l.is_active')
			->from('listings l')
			->join('books b', 'b.isbn_13 = l.isbn_13', 'inner')
			->where($where)
			->limit($lim, $offset);

		return $query->get()->result();
	}

	// Retrieves only 1 listing for a given ISBN
	function retrieve_unique_book_title($isbn) 
	{
		$where = array(
			'l.isbn_13' => $isbn
		);

		$query = $this->db->select('b.title')
			->from('books b')
			->join('listings l', 'l.isbn_13 = b.isbn_13', 'inner')
			->where($where)
			->group_by('b.title');
		
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
	function insert($isbn_13, $listing_price, $condition=0, $is_active=FALSE)
	{
		$obj = array(
			'isbn_13' => $isbn_13,
			'listing_price' => $listing_price,
			'condition' => $condition,
			'is_active' => $is_active
		);

		$this->db->insert('listings', $obj);

		return TRUE;
	}
}
