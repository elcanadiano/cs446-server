<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
Class Books_m extends CI_Model
{
	/**
	 * Retrieve all books.
	 *
	 * @return  object
	 */
	function retrieve()
	{
		$query = $this->db->select('isbn_13, title, authors, publisher, edition, msrp, year, '
			. 'amazon_detail_url, amazon_small_image, amazon_medium_image, amazon_large_image')
			->from('books')
			->order_by('isbn_13');

		return $query->get()->result();
	}

	/**
	 * Attempt to retrieve a book by ISBN.
	 *
	 * @return  object
	 */
	function retrieve_by_isbn($isbn_13)
	{
		$where = array(
			'isbn_13' => $isbn_13
		);

		$query = $this->db->select('isbn_13, title, authors, publisher, edition, msrp, year, '
			. 'amazon_detail_url, amazon_small_image, amazon_medium_image, amazon_large_image')
			->from('books')
			->where($where)
			->limit(1);
		
		$result = $query->get()->result();

		if ($result)
		{
			return $result[0];
		}

		return NULL;
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
	 * @param   authors
	 *			The author(s) (optional)
	 * 
	 * @param   publisher
	 *			The publisher (optional)
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
	 * @param   amazon_detail_url
	 *			The Amazon detail URL (optional)
	 * 
	 * @param   amazon_small_image
	 *			The Amazon Small-sized Image (optional)
	 * 
	 * @param   amazon_medium_image
	 *			The Amazon Medium-sized Image(optional)
	 * 
	 * @param   amazon_large_image
	 *			The Amazon Large-sized Image (optional)
	 *
	 * @return  boolean
	 */
	function insert($isbn_13, $title, $authors=NULL, $publisher=NULL, $edition=NULL,
		$msrp=NULL, $year=NULL, $amazon_detail_url=NULL, $amazon_small_image=NULL,
		$amazon_medium_image=NULL, $amazon_large_image=NULL)
	{
		$obj = array(
			'isbn_13' => $isbn_13,
			'title' => $title,
			'authors' => $authors,
			'publisher' => $publisher,
			'edition' => $edition,
			'msrp' => $msrp,
			'year' => $year
		);

		$this->db->insert('books', $obj);

		return TRUE;
	}

	/**
	 * Inserts a new book into the database. However, the book
	 * object is passed in. If you do not have certain columns,
	 * do not include it in the object.
	 *
	 * @param   obj
	 *			The Book Object.
	 *
	 * @return  boolean
	 */
	function insert_obj($obj)
	{
		$this->db->insert('books', $obj);

		return TRUE;
	}
}