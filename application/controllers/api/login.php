<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->load->model('User_m');
		$this->load->model('fb_users_m');
		$this->load->library('facebook');
	}

	/**
	 * Fallback function if no method is provided. This should always
	 * error out because this should not ever be called.
	 */
	function index()
	{
		$arr = array(
			'status' => array(
				'status' => 'error',
				'message' => 'Invalid function.'
			),
			'data' => array()
		);

		echo json_encode($arr);
	}

	/**
	 * Function for the app to login with the server. Passed in the
	 * POST request is the user_id and the access token that the app has
	 * to have been verified by Facebook at one point.
	 *
	 * If the user's User ID exists, then just provide the login infomation
	 * to the user. Otherwise, then do an FQL query to the user for the users
	 * name, email address, small and square pictures, and the phone number,
	 * and do not verify the user. Send a message to the user to verify the
	 * account, and ask them to provide information.
	 */
	function login_user($user_id)
	{
		// php error - request is not defined
		//$user_id = $this->request->post('');
	
		$this->retrieve_user

		$arr = array(
			'users' => $this->User_m->retrieve(),
			'pet_name' => 'Cat',
			'name' => 'Whiskers'
		);

		echo json_encode($arr);
	}

	/**
	 * Function to verify the user. TODO: Figure out what information is being
	 * sent back.
	 */
	function verify()
	{

	}
}
