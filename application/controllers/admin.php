<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('common.php');

class Admin extends Common {

	function __construct() {
		parent::__construct();
		$this->load->model('admin_model');
		$this->load->model('report_model');
		$this->load->library('BBS');
	}
	
	function index() {
		if(!$this->session->userdata('admin') ){
			redirect('admin/login');
		}
		$data['header'] = $this->html_header("管理面板");
		$data['footer'] = $this->html_footer();
	 
		
		$data['admin']=$this->admin_model->getUserByName($this->session->userdata('admin'));
		
		$data['reports']=$this->report_model->get_report_list(15,0,true,false,array('cid'=>$this->session->userdata('cid')));
		
		$template = $this->site_template . 'admin/index';
		$this->load->view($template, $data);
	}
	
	function login(){
		if($this->session->userdata('admin') ){
			redirect('admin/index');
		}
		$data['header'] = $this->html_header("后台登录");
		$data['footer'] = $this->html_footer();
		$template = $this->site_template . 'admin/login';
		$this->load->view($template, $data);
	}
	
	function onlogin(){
		$name=$_POST['name'];
		$pw=md5($_POST['pass']);
		$row=$this->admin_model->checkUser($name,$pw);
		if($row){
			$sessiondata=array('admin'=>$name,'cid'=>$row['cid'],'adminid'=>$row['id']);
			$this->session->set_userdata($sessiondata);
			echo "OK";
		}else{
			echo "error";
		}
	}
	
	 
	function addrep(){
		if(!$this->session->userdata('admin') ){
			redirect('admin/index');
		}
		$data=array();
		$template = $this->site_template . 'admin/addrep';
		$this->load->view($template, $data);
	}
	
	function onaddrep(){
		//check if user has the admission
		if(!$this->session->userdata('admin') ){
			redirect('admin/index');
		}
		
		$err="";
		$speaker=$_POST['speaker'];
		$title=$_POST['title'];
		$date=$_POST['date'];
		$begin=$_POST['begin'];
		$profile=$_POST['profile'];
		$institution=$_POST['institution'];
		$end=$_POST['end'];
		$content=$_POST['content'];
		$place=$_POST['place'];
		$poster=$this->session->userdata ( 'adminid' );;//get user from session
		$cid=$this->session->userdata ( 'cid' );
		//$bbsuser=$_POST['bbsuser'];
		//$bbspass=$_POST['bbspass'];
		
		if(empty($speaker)||empty($title)||empty($profile)||empty($date)||empty($begin)||empty($end)||empty($institution)||empty($place)){
			echo "error";
			return;
		}
		//get time lengh of report
		$begindt=new DateTime($begin);
		$enddt=new DateTime($end);
		$interval = $begindt->diff($enddt);
		$hours   = $interval->format('%h');
		$minutes = $interval->format('%i');
		$length=$hours * 60 + $minutes;
		
		$starttime = $date." ".$begin;// TODO need to be tested
		
		$data = array (
				'speaker' => $speaker,
				'title' => $title,
				'starttime' => $starttime,
				'length' => $length,
				'content' => $content,
				'cid'	=>$cid,
				'institution' => $institution,
				'profile' => $profile,
				'place'=>$place,
				'state'=>1
		);
		
		if($this->report_model->add($data)){
			if (! empty ( $bbsuser ) && ! empty ( $bbsuser )) {
			}
			$id = mysql_insert_id ();
			$board = $this->report_model->bbs_board ( $cid );
			//$board='test';
			if ($board != '') {
			 
				$href = $this->bbs->post ( $data, $id, $board );
				 
			}else{
				$href='';
			}
			// if($href!=""){
			echo json_encode ( array (
					'status' => 'success',
					'id' => $id,
					'href' => $href 
			) );
				//}
		
	}
	}
	
	function editrep(){
		if(!$this->session->userdata('admin') ){
			redirect('admin/index');
		}
		$id=$_POST['id'];
	 
		$data['report'] = $this->report_model->fetch(array('id'=>$id));
		$dt=new DateTime($data['report']['starttime']);
		$date=$dt->format('Y-m-d');
		$starttime=$dt->format('H:i');
		$dt->modify('+'.$data['report']['length'].' minutes');//TODO
		$endtime=$dt->format('H:i');
		$data['report']['starttime']=$starttime;
		$data['report']['date']=$date;
		$data['report']['endtime']=$endtime;
		$template = $this->site_template . 'admin/edit_rep';
		$this->load->view($template, $data);
	}
	
	function updaterep() {
		if (! $this->session->userdata ( 'admin' )) {
			redirect ( 'admin/index' );
		}
		$err = "";
		$id = $_POST ['id'];
		$speaker = $_POST ['speaker'];
		$title = $_POST ['title'];
		$date = $_POST ['date'];
		$begin = $_POST ['begin'];
		$profile = $_POST ['profile'];
		$institution = $_POST ['institution'];
		$end = $_POST ['end'];
		$content = $_POST ['content'];
		$place = $_POST ['place'];
		$poster = $this->session->userdata ( 'adminid' ); // get user from session
		$cid=$this->session->userdata ( 'cid' );
		/*
		 * $bbsuser=$_POST['bbsuser']; $bbspass=$_POST['bbspass'];
		 */
		
		if (empty ( $speaker ) || empty ( $title ) || empty ( $profile ) || empty ( $date ) || empty ( $begin ) || empty ( $end ) || empty ( $institution ) || empty ( $place )) {
			echo "error";
			return;
		}
		// get time lengh of report
		$begindt = new DateTime ( $begin );
		$enddt = new DateTime ( $end );
		$interval = $begindt->diff ( $enddt );
		$hours = $interval->format ( '%h' );
		$minutes = $interval->format ( '%i' );
		$length = $hours * 60 + $minutes;
		
		$starttime = $date . " " . $begin; // TODO need to be tested
		
		$data = array (
				'speaker' => $speaker,
				'title' => $title,
				'starttime' => $starttime,
				'length' => $length,
				'content' => $content,
				'institution' => $institution,
				'profile' => $profile,
				'place'=>$place
		);
		$oldrep=$this->report_model->fetch(array('id'=>$id));
		if($oldrep['state']==3)
			$data['state']=2;//get state of report, if state=3(notices are already sent), set the state to 2
		$where=array('id'=>$id);
		if ($this->report_model->update( $data, $where )) {
			 
			 
			$row = $this->report_model->fetch(array('id'=>$id));
			$board = $this->report_model->bbs_board ( $cid );
			//$board='test';
			 
			$this->bbs->update( $data, $row ['bbslink'],$board, $id );
			
			echo json_encode ( array (
					'status' => 'success',
					'id' => $id,
					'href' => $row ['bbslink'] 
			) );
		} else {
			echo json_encode ( array (
					'status' => 'error',
					'message' => '更新报告错误' 
			) );
		}
		 
	}
	
	function logout(){
		$this->session->sess_destroy();
		redirect('user/login');
	}
	
	
	function test(){
		$data['header'] = $this->html_header("订阅管理");
		$data['footer'] = $this->html_footer();
		$template = $this->site_template . 'user/test';
		$this->load->view($template,$data);
	}
	
}
