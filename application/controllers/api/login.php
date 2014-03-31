<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->load->model('User_m', 'users');
		$this->load->model('Fb_users_m', 'fb_users');
		$this->load->library('password_generator');
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
	function fb_login()
	{
		$user_id = $this->input->get_post('user_id');
		$access_token = $this->input->get_post('access_token');
		// No User ID or Access Token.
		if (!$user_id || !$access_token)
		{
			$arr = array(
				'status' => array(
					'status' => 'error',
					'message' => 'Please provide a User ID and an Access Token.'
				),
				'data' => array()
			);

			echo json_encode($arr);
			return;
		}

		// Query FB for the first name, middle name, last name, square picture, and small picture.
		try {
			$fql = 'SELECT first_name, middle_name, last_name, pic_square, pic_small from user where uid=' . $user_id;

			$fb_user = $this->facebook->api(array(
				'method' => 'fql.query',
				'query' => $fql,
				'access_token' => $access_token
			));
		} catch (FacebookApiException $e) {
			$arr = array(
				'status' => array(
					'status' => 'error',
					'message' => 'Invalid Access Token or FQL Error.'
				),
				'data' => array(
					'FacebookApiException' => array(
						'result' => $e->getResult(),
						'type' => $e->getType()
					)
				)
			);

			echo json_encode($arr);
			return;
		}

		//echo json_encode($fb_user), "\n\n";

		if (!isset($fb_user[0]))
		{
			$arr = array(
				'status' => array(
					'status' => 'error',
					'message' => 'Invalid Facebook User ID.'
				),
				'data' => array()
			);

			echo json_encode($arr);
			return;
		}

		$user_data = $fb_user[0];

		// Retrieve the user.
		$user = $this->fb_users->retrieve_by_user_id($user_id);

		$new_access_code = $this->password_generator->getAlphaNumericPassword(20);


		if (!$user)
		{
			// Insert it to the DB. At this time, the user is verified.
			$this->fb_users->insert($user_id, $access_token, $user_data['first_name'],
				$user_data['middle_name'], $user_data['last_name'],
				$user_data['pic_square'], $user_data['pic_small'], $new_access_code);


			$arr = array(
				'status' => array(
					'status' => 'success',
					'message' => 'User added, but verification is needed.',
					'code' => 2
				),
				'data' => array(
					'access_code' => $new_access_code
				)
			);

			echo json_encode($arr);
			return;
		}

		// Update the user's name
		$user_data['access_code'] = $new_access_code;
		$this->fb_users->update_user($user_id, $user_data);

		// If the user is verified, then we give them the user data.
		if ($user->is_verified)
		{
			$arr = array(
				'status' => array(
					'status' => 'success',
					'message' => 'User logged in successfully!',
					'code' => 1
				),
				'data' => array(
					'user' => $user,
					'access_code' => $new_access_code
				)
			);

			echo json_encode($arr);
			return;			
		}

		// Otherwise, the user is not verified, and thus tell them so.
		$arr = array(
			'status' => array(
				'status' => 'success',
				'message' => 'User has logged in before, but verification is needed.',
				'code' => 3
			),
			'data' => array(
				'access_code' => $new_access_code
			)
		);

		echo json_encode($arr);
	}

	/**
	 * Function to verify the user. TODO: Figure out what information is being
	 * sent back.
	 */
	function verify()
	{
		$user_id = $this->input->get_post('user_id');
		$email = $this->input->get_post('email');
		$phone_number = $this->input->get_post('phone_number');
		$access_code = $this->input->get_post('access_code');

		// TODO: Convert the phone number to NANP format, error out
		// if in invalid form.

		// Verify if the access code is valid.
		if (!$this->fb_users->verify_access_code($user_id, $access_code))
		{
			$arr = array(
				'status' => array(
					'status' => 'error',
					'message' => 'Invalid Access Code.',
					'code' => -1
				),
				'data' => array()
			);

			echo json_encode($arr);
			return;
		}

		// Retrieve the user.
		$user = $this->fb_users->retrieve_by_user_id($user_id);

		// If there was not one, then something screwed up.
		if (!$user)
		{
			$arr = array(
				'status' => array(
					'status' => 'error',
					'message' => 'The user does not exist.',
					'code' => -2
				),
				'data' => array()
			);

			echo json_encode($arr);
			return;
		}

		// Update.
		$update_obj = array(
			'email' => $email,
			'phone_number' => $phone_number,
			'is_verified' => TRUE
		);

		$this->fb_users->update_user($user_id, $update_obj);

		// Update the user object that is passed to the user.
		$user->email = $email;
		$user->phone_number = $phone_number;
		$user->is_verified = TRUE;

		$arr = array(
			'status' => array(
				'status' => 'success',
				'message' => 'User verified successfully!',
				'code' => 0
			),
			'data' => array(
				'user' => $user
			)
		);

		echo json_encode($arr);
	}
}
