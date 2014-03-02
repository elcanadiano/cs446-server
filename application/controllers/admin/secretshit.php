<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// We need C_admin
require_once('c_admin.php');

class Secretshit extends C_Admin {
	static private $user_links = array(
		'title' => 'User Functions',
		'links' => array(
			array(
				'url' => '/admin/user/create',
				'desc' => 'Create User'
			)
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->load->model('books_m','books');
		$this->load->model('listings_m','listings');
		$this->load->library('isbn_lib');
	}

	/**
	 * The default function in User simply just lists out all the admins.
	 */
	function index()
	{
		echo 'Kittens'
	}

	/**
	 * Adds a bunch of random books from a file.
	 */
	function add_books()
	{
		$handle = @fopen('resources/isbn.txt', 'r');
		if ($handle) {
			$key = $this->config->item('isbndb_key');

			while (($buffer = fgets($handle, 4096)) !== false) {
				$buffer = trim($buffer);

				// ASSUMPTION: $buffer is a valid ISBN
				if ($this->isbn_lib->is_isbn_13_valid($buffer))
				{
					if ($this->books->retrieve_by_isbn($buffer))
					{
						echo $buffer, ' already exists.<br />';
					}
					else
					{
						echo $buffer, ' does not exist.<br />';

						$book_info = $this->isbn_lib->get_from_isbndb($buffer, $key);

						print_r($book_info);

						if (isset($book_info->data))
						{
							foreach ($book_info->data as $book)
							{
								$isbn_13 = $book->isbn13;
								$title = $book->title;
								$author = 'Pilkey, Dav';
								$publisher = $book->publisher_name;

								// Insert
								$this->books->insert($isbn_13, $title, $author, $publisher);

								echo $isbn_13, ' ', $title, ' ', $author, ' ', $publisher, '<br />';
							}
						}
						else
						{
							echo $book_info->error, '<br />';
						}
					}
				}
				else
				{
					echo $buffer, ' is invalid.<br />';
				}
			}
			if (!feof($handle)) {
				echo "Error: unexpected fgets() fail\n";
			}
			fclose($handle);
		}
		else
		{
			echo 'Nope.';
		}
	}

	/**
	 * Adds up to (but can be none) ten random listings for each book.
	 */
	function add_random_listings_for_books()
	{
		// Retrieve all books.
		$books = $this->books->retrieve();

		foreach ($books as $book)
		{
			// Get a random number from 10 to 15.
			$num = rand(10, 15);
			$isbn_13 = $book->isbn_13;

			for ($i = 0; $i < $num; $i++)
			{
				$price = rand(1, 150) . '.' . rand(0, 99);
				$condition = rand(0, 5);
				$is_active = rand(0, 1);

				$this->listings->insert($isbn_13, $price, $condition, $is_active);

				echo 'Book ', $book->isbn_13, ' has been added to the database with a price of $',
				$price, ', condition of ', $condition, ' and is_active of ', $is_active, '<br />';
			}
		}		
	}

	/**
	 * Adds Introduction to Algorithms and three records.
	 */
	function add_algorithms()
	{
		$key = $this->config->item('isbndb_key');
		// Insert exactly three records for Introduction to Algorithms.
		$book_info = $this->isbn_lib->get_from_isbndb('9780262033848', $key);

		if (isset($book_info->data))
		{
			foreach ($book_info->data as $book)
			{
				$isbn_13 = $book->isbn13;
				$title = $book->title;
				$author = 'Pilkey, Dav';
				$publisher = $book->publisher_name;

				// Insert exactly three
				$this->books->insert($isbn_13, $title, $author, $publisher);
				$this->listings->insert($isbn_13, rand(1, 150) . '.' . rand(0, 99), rand(0, 5), rand(0, 1));
				$this->listings->insert($isbn_13, rand(1, 150) . '.' . rand(0, 99), rand(0, 5), rand(0, 1));
				$this->listings->insert($isbn_13, rand(1, 150) . '.' . rand(0, 99), rand(0, 5), rand(0, 1));

				echo $isbn_13, ' ', $title, ' ', $author, ' ', $publisher, '<br />';
			}
		}
		else
		{
			echo $book_info->error, '<br />';
		}
	}
}
