<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('common.php');

class Report extends Common {

	function __construct() {
		parent::__construct();
	}
	
	function index(){
		 
			if($this->session->userdata('user') ){
				$this->load->model('user_model');
				$data['user']=$this->user_model->getUserByEmail($this->session->userdata('user'));
			}
		
			$data['header'] = $this->html_header("报告列表");
			$data['footer'] = $this->html_footer();
		
			$this->load->model('report_model');
			$data['reports']=$this->report_model->get_report_list(15,0,false,false);
			 
			$template = $this->site_template . 'report/index';
			$this->load->view($template, $data);
		 
	}
}