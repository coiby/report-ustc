<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class User extends CI_Controller {
	 
	function __construct() {
		parent::__construct();
	}
	
	function index() {
		$data['header'] = $this->html_header("用户登录");
		$data['footer'] = $this->html_footer();
	 
		$this->load->model('report_model');
		$data['reports']=$this->report_model->get_report_list(15,0,"yearweek(`starttime`, 1) = yearweek(curdate(), 1)");
		 
		$template = $this->site_template . 'user/index';
		$this->load->view($template, $data);
	}
	
	function register(){
		$email=$_POST['email'];
		$mobile=$_POST['mobile'];
		$pw1=$_POST['password'];
		$pw2=$_POST['password2'];
		
		$error=false;
		$errormsg="";
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$error=true;
			$errormsg="邮箱格式错误\n";
		}
		
		if($pw1!==$pw2){
			$error=true;
			$errormsg=$errormsg."两次密码不匹配\n";
		}else{
			$pw1=md5($pw1);
		}
		
		if(!preg_match('/^\d{11}$/',$mobile)){
			$error=true;
			$errormsg=$errormsg."手机号有误\n";
		}
		
		if($error){
			echo json_encode(array('status' => 'error','message'=>$errormsg));
		}else{
			$this->load->model('user_model');
			if($this->user_model->createUser(array('email'=>$email,'mobile'=>$mobile,'pass'=>$pw1))){
				$id = $this->db->insert_id();
				echo json_encode(array('status' => 'success','id'=>$id,'message'=>"注册成功！"));
				$this->load->library('email');
				$this->email->subject('成功注册报告订阅系统')
							->message('恭喜！')
							->to($email)
							->from('coibyxqx@qq.com')
							->send();
			}else{
				echo json_encode(array('status' => 'error','message'=>"您的邮箱或手机号已经在注册用户中！"));
			}
		
		
		}
	}
	function login(){
		$email=$_POST['email'];
		$pw=md5($_POST['pass']);
		
		$where=" where email='".$email."' and pass='".$pw."'";
		$query="select id from user".$where;
		
		$res = $this->db->query($query);
		 
		if($res->num_rows() > 0){
			$row = $res->row_array();
			$sessiondata=array('user'=>$email,'id'=>$row['id']);
			$this->session->set_userdata($sessiondata);
		
			echo "OK";
		}else{
			echo "error";
		}
	}
	
	function update(){
		$email=$_POST['email'];
		$mobile=$_POST['mobile'];
		
		$updatesql="";
		
		if(isset($_POST['password'])){
			$pw1=$_POST['password'];
			$pw2=$_POST['password2'];
			if($pw1!==$pw2){
				$error=true;
				$errormsg=$errormsg."两次密码不匹配\n";
			}else{
				$pw1=md5($pw1);
				$updatesql=",pass=".$pw1;
			}
		}
		
		$error=false;
		$errormsg="";
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$error=true;
			$errormsg="邮箱格式错误\n";
		}
		
		if(!preg_match('/^\d{11}$/',$mobile)){
			$error=true;
			$errormsg=$errormsg."手机号有误\n";
		}
		
		if($error){
			echo json_encode(array('status' => 'error','message'=>$errormsg));
		}else{
			$query="update user set email='".$email."',mobile='".$mobile."'".$updatesql." where id=".$this->session->userdata('id');
			 
			if($this->db->query($query)){
				echo json_encode(array('status' => 'success','message'=>"更新信息成功"));
			}else{
				echo json_encode(array('status' => 'error','message'=>"更新信息出错！"));
			}
		}
	}
	
	/**
	 * setting dialogue for subscription
	 */
	function sub_setting(){
		if(!$this->session->userdata('user') ){
			redirect('user/login');
		}
		$uid=$this->session->userdata('id');
		$cid=$_POST['cid'];
		
		$this->load->model('subscribe_model');
		$data['sub']=$this->subscribe_model->get_setting(array('cid'=>$cid,'uid'=>$uid));
		$template = 'api/sub_setting';
		$this->load->view($template,$data);
	}
	
	/**
	 *  subscribe
	 */
	function subscribe(){
		if(!$this->session->userdata('user') ){
			redirect('user/login');
		}
		$uid=$this->session->userdata('id');
		$cid=$_POST['cid'];
		$this->load->model('subscribe_model');
		if(($id=$this->subscribe_model->subscribe(array('uid'=>$uid,'cid'=>$cid)))!=false){
			echo json_encode(array('id'=>$id,'status' => 'success','message'=>"订阅成功！"));
		}else{
			echo json_encode(array('status' => 'error','message'=>"订阅失败！"));
		}
		
	}
	
	/**
	 *   unsubscribe
	 */
	function unsubscribe(){
		if(!$this->session->userdata('user') ){
			redirect('user/login');
		}
		$uid=$this->session->userdata('id');
		$cid=$_POST['cid'];
		$this->load->model('subscribe_model');
		if($this->subscribe_model->unsubscribe(array('uid'=>$uid,'cid'=>$cid))){
			echo json_encode(array('status' => 'success','message'=>"退订成功！"));
		}else{
			echo json_encode(array('status' => 'error','message'=>"退订失败！"));
		}
	}
	
	/**
	 * 
	 */
	function update_subsetting(){
		if(!$this->session->userdata('user') ){
			redirect('user/login');
		}
		$uid=$this->session->userdata('id');
		$cid=$_POST['cid'];
		
		$data=array('byemail'=>$this->input->post('byemail'),'bymsg'=>$this->input->post('bymsg'));
		$where=array('uid'=>$uid,'cid'=>$cid);
		$this->load->model('subscribe_model');
		if($this->subscribe_model->update_setting($data,$where)){
			echo json_encode(array('status' => 'success','message'=>"退订成功！"));
		}else{
			echo json_encode(array('status' => 'error','message'=>"退订失败！"));
		}
	}
}