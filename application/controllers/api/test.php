<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	//fallback function, if no function specified
	function index()
	{
		echo "Index";
	}
	function test2(){
		$arr = array(
			'pet_name' => 'Cat',
			'name' => 'Whiskers'
		);

		echo json_encode($arr);
	}

}
