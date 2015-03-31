<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('common.php');

class Index extends Common {

	function __construct() {
		parent::__construct();
	}
	
	function index() {
		if($this->session->userdata('user') ){
			$this->load->model('user_model');
			$data['user']=$this->user_model->getUserByEmail($this->session->userdata('user'));
		}
		
		$data['header'] = $this->html_header("首页");
		$data['footer'] = $this->html_footer();
	 
		$this->load->model('report_model');
		$data['reports']=$this->report_model->get_report_list(15,0,true,false);
		 
		$template = $this->site_template . 'index/index';
		$this->load->view($template, $data);
	}
}
