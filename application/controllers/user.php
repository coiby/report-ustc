<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('common.php');

class User extends Common {

	function __construct() {
		parent::__construct();
		$this->load->model('user_model');
	}
	
	function index() {
		if(!$this->session->userdata('user') ){
			redirect('user/login');
		}
		redirect('user/subscribe');
		
	}
	
	function profile() {
		if(!$this->session->userdata('user') ){
			redirect('user/login');
		}
		
		$data['header'] = $this->html_header("用户信息");
		$data['footer'] = $this->html_footer();
		
		
		$data['user']=$this->user_model->getUserByEmail($this->session->userdata('user'));
			
		$template = $this->site_template . 'user/profile';
		$this->load->view($template, $data);
		
	}
	
	function login(){
		if($this->session->userdata('user') ){
			redirect('user/index');
		}
		$data['header'] = $this->html_header("用户登录");
		$data['footer'] = $this->html_footer();
		$template = $this->site_template . 'user/login';
		$this->load->view($template, $data);
	}
	
	function register(){
		if($this->session->userdata('user') ){
			redirect('user/index');
		}
		$data['header'] = $this->html_header("用户注册");
		$data['footer'] = $this->html_footer();
		$template = $this->site_template . 'user/register';
		$this->load->view($template, $data);
	}
	
	function logout(){
		$this->session->sess_destroy();
		redirect('user/login');
	}
	
	function subscribe(){
		if(!$this->session->userdata('user') ){
			redirect('user/login');
		}
		$data['header'] = $this->html_header("订阅管理");
		$data['footer'] = $this->html_footer();
		$this->load->model('user_model');
		$data['user']=$this->user_model->getUserByEmail($this->session->userdata('user'));
		$data['subs'] = $this->user_model->getSubs($this->session->userdata('id'));
		$template = $this->site_template . 'user/subscribe';
		$this->load->view($template, $data);
	}
	function reset_pw_code(){
		$data['header'] = $this->html_header("发送重设密码验证码");
		$data['footer'] = $this->html_footer();
		$template = $this->site_template . 'user/reset_pw_code';
		$this->load->view($template, $data);
	}
	
	function reset_pw(){
		$code=$this->input->get('code', TRUE);
		$email=$this->input->get('email', TRUE);
		
		$data['header'] = $this->html_header("重设密码");
		$data['footer'] = $this->html_footer();
		
		 if($this->user_model->reset_pw_check($email,$code)){
			$data['valid'] =true;
			$this->session->set_userdata('resetemail',$email);
		}else{
			$data['valid'] =false;
		}
		$template = $this->site_template . 'user/reset_pw';
		
		$this->load->view($template, $data);
	}
	
	function test(){
		$data['header'] = $this->html_header("订阅管理");
		$data['footer'] = $this->html_footer();
		$template = $this->site_template . 'user/test';
		$this->load->view($template,$data);
	}
	
}
