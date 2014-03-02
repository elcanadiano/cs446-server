<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
Class Books_m extends CI_Model
{
	/**
	 * Attempt to retrieve a book by ISBN.
	 *
	 * @return  object
	 */
	function retrieve_by_isbn()
	{
		$query = $this->db->select('isbn_13, title, author, edition, msrp, year')
			->from('books')
			->limit(1);

		return $query->get()->result();
	}

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
	 *			The author(s) (optional)
	 * 
	 * @param   edition
	 *			The edition of the book (if applicable)
	 * 
	 * @param   msrp
	 *			The retail price (optional)
	 * 
	 * @param   year
	 *			The year the book was published (optional)
	 *
	 * @return  boolean
	 */
	function insert($isbn_13, $title, $author='', $edition=0, $msrp=0, $year=0)
	{
		$obj = array(
			'isbn_13' => $isbn_13,
			'title' => $title,
			'date' => $author,
			'edition' => $edition,
			'msrp' => $msrp,
			'year' => $year
		);

		$this->db->insert('books', $obj);

		return TRUE;
	}
}