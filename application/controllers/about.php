<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('common.php');

class About extends Common {
	function __construct() {
		parent::__construct();
	}
	
	function index() {
		 
		$data['header'] = $this->html_header("关于");
		$data['footer'] = $this->html_footer();
	
		 
		$template = $this->site_template . 'about/index';
		$this->load->view($template, $data);
	}
}