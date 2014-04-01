<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
Class Temp_contact_info_m extends CI_Model
{
	/**
	 * Inserts a new listing into the database.
	 */
	function insert($lid, $first_name, $middle_name, $last_name, $phone_number, $email)
	{
		$obj = array(
			'lid' => $lid,
			'first_name' => $first_name,
			'middle_name' => $middle_name,
			'last_name' => $last_name,
			'phone_number' => $phone_number,
			'email' => $email
		);

		$this->db->insert('temp_contact_info', $obj);

		return TRUE;
	}
}
