<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
Class Authors_m extends CI_Model
{
	/**
	 * Inserts a new book into the database.
	 *
	 * @param   isbn_13
	 *			The ISBN
	 *
	 * @param   title
	 *			The book's title.
	 * 
	 * @param   author
	 *			The author
	 *
	 * @return  boolean
	 */
	function insert($isbn_13, $name)
	{
		$obj = array(
			'isbn_13' => $isbn_13,
			'name' => $name
		);

		$this->db->insert('authors', $obj);

		return TRUE;
	}

	/**
	 * Inserts a batch of authors into the database.
	 *
	 * @param   obj
	 *			The list of authors.
	 *
	 * @return  boolean
	 */
	function insert_batch($isbn_13, $obj)
	{
		$this->db->insert_batch('authors', $obj);

		return TRUE;
	}
}