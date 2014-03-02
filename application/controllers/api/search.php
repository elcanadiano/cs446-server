<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->load->model('User_m');
	}

	//fallback function, if no function specified
	function index()
	{
		echo "Index";
	}

	// Send back what message is passed in
	function test2($message)
	{
		$arr = array(
			'status' => array(
				'status' => 'success',
				'message' => ''
			),
			'data' = array(
				'message' => $message
			)
		);
		echo json_encode($arr);
	}
}
