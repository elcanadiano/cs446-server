<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

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

	function test2(){
		$arr = array(
			'users' => $this->User_m->retrieve(),
			'pet_name' => 'Cat',
			'name' => 'Whiskers'
		);

		echo json_encode($arr);
	}

	function dummypost() {
		$cat = $this->input->post('cat');

		$arr = array(
			'cat' => $cat
		);

		echo json_encode($arr);
	}
}
