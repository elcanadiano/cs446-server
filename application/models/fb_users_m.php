<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// If we are not using PHP 5.5 or greater, we need a
// supplementary file for all things bcrypt.
if (PHP_VERSION_ID < 50500)
{
	require('application/libraries/password.php');
}

Class Fb_users_m extends CI_Model
{
	/**
	 * Retrieve all books.
	 *
	 * @return  object
	 */
	function retrieve_by_user_id($user_id)
	{
		$where = array(
			'user_id' => $user_id
		);

		$query = $this->db->select('user_id, access_token, first_name, middle_name, last_name,'
			. ' email, pic_square, pic_small, phone_number, is_verified')
			->from('fb_users')
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
	 * Inserts a new, unverified Facebook user into the database.
	 *
	 * @param   user_id
	 *			The User ID.
	 *
	 * @param   access_token
	 *			The access token.
	 * 
	 * @param   first_name
	 *			The first name.
	 * 
	 * @param   middle_name
	 *			The middle name.
	 * 
	 * @param   last_name
	 *			The last name.
	 * 
	 * @param   pic_square
	 *			The user's Square-sized Facebook image (50x50)
	 * 
	 * @param   pic_small
	 *			The user's Small-sized Facebook image max(50x150)
	 *
	 * @return  boolean
	 */
	function insert($user_id, $access_token, $first_name,
		$middle_name, $last_name, $pic_square, $pic_small, $access_code)
	{
		$obj = array(
			'user_id' => $user_id,
			'access_token' => $access_token,
			'first_name' => $first_name,
			'middle_name' => $middle_name,
			'last_name' => $last_name,
			'pic_square' => $pic_square,
			'pic_small' => $pic_small,
			'access_code' => $this->encr($access_code)
		);

		$this->db->insert('fb_users', $obj);

		return TRUE;
	}

	/**
	 * Checks for the user's access code is correct.
	 *
	 * @param   user_id
	 *			The User ID.
	 *
	 * @param   access_code
	 *			The Access Code.
	 *
	 * @return  boolean
	 */
	function verify_access_code($user_id, $access_code)
	{
		$where = array(
			'user_id' => $user_id
		);

		$this->db->select('user_id')
			->from('fb_users')
			->where($where)
			->limit(1);

		$res = $query->get->result();

		if ($res)
		{
			$row = $res[0];
			$hash = $result[0]->access_code;

			return password_verify($access_code, $hash);
		}

		return FALSE;
	}

	/**
	 * Updates a user's name and pictures.
	 *
	 * @param   user_id
	 *			The User ID.
	 *
	 * @param   obj
	 *			The Book Object.
	 *
	 * @return  boolean
	 */
	function update_user($user_id, $obj)
	{
		$where = array(
			'user_id' => $user_id
		);

		$this->db->where($where)->update('fb_users', $obj);

		return TRUE;
	}

	/**
	 * Encrypts a password.
	 *
	 * @param	string $password
	 *			The password
	 *
	 * @return	string
	 *			The encrypted version of the password.
	 */
	private function encr($password)
	{
		return password_hash($password, PASSWORD_BCRYPT);
		//return hash('sha256', hash('sha256', $password));
	}
}