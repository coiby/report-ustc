<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Report extends CI_Controller {
	 
	function __construct() {
		parent::__construct();
	}
	
	function view(){
		$id=$_POST['id'];
		
		$this->load->model('report_model');
		$data['report']=$this->report_model->fetch(array('id'=>$id));
		$template = 'api/viewreport';
		$this->load->view($template,$data);
	}
}