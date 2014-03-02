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
			'isbn_13' => $isbn
		);

		$query = $this->db->select('id, isbn_13, listing_price, condition, is_active')
			->from('listings')
			->where($where)
			->limit($lim, $offset);

		return $query->get()->result();
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