<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('common.php');

class Admin extends Common {

	function __construct() {
		parent::__construct();
		$this->load->model('admin_model');
		$this->load->model('report_model');
		$this->load->library('simple_html_dom');
		$this->load->library('Snoopy');
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
			if(!empty($bbsuser)&&!empty($bbsuser)){
		
			}
			$id=mysql_insert_id();
			$bbsb="";//mailbody
			//TODO feature: 通知具体改变
			$bbsb="报告人：".$speaker."\n";//mailbody
			//$bbsb="报告人：".$row['speaker']."\n";
			if (! empty ( $institution )) {
				$bbsb = $bbsb . "单位：" . $institution . "\n";
				// $bbsb="单位：".$row['institution']."\n";
			}
			// deal with date + time
			/*
			* $dt=new DateTime($row['starttime']); $date= $date = $dt->format('m/d/Y'); $time = $dt->format('H:i');
			*/
		
			$bbsb = $bbsb . "时间：" . $starttime. "-" . $end . "\n\n";
		
			if (! empty ( $profile ))
			$bbsb = $bbsb . "报告人介绍\n" . $profile . "\n\n";
		
			if (! empty ( $content ))
				$bbsb = $bbsb . "报告摘要\n" . $content ;
				$href=$this->senttobbs($title, $bbsb,$id);
				//if($href!=""){
				echo json_encode(array('status' => 'success','id'=>$id,'href'=>$href));
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
		$where=array('id'=>$id);
		if ($this->report_model->update( $data, $where )) {
			$bbsb = ""; // mailbody
			          // TODO feature: 通知具体改变
			$bbsb = "报告人：" . $speaker . "\n"; // mailbody
			                            // $bbsb="报告人：".$row['speaker']."\n";
			if (! empty ( $institution )) {
				$bbsb = $bbsb . "单位：" . $institution . "\n";
				// $bbsb="单位：".$row['institution']."\n";
			}
			// deal with date + time
			/*
			 * $dt=new DateTime($row['starttime']); $date= $date = $dt->format('m/d/Y'); $time = $dt->format('H:i');
			 */
			
			$bbsb = $bbsb . "时间：" . $starttime . "-" . $end . "\n\n";
			
			if (! empty ( $profile ))
				$bbsb = $bbsb . "报告人介绍\n" . $profile . "\n\n";
			
			if (! empty ( $content ))
				$bbsb = $bbsb . "报告摘要\n" . $content;
			$subject = "[更新]" . $title;
			 
			$row = $this->report_model->fetch(array('id'=>$id));
			$this->updatebbs ( $subject, $bbsb, $row ['bbslink'], $id );
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
	
	protected function updatebbs($subject, $content, $link, $repid){
		$snoopy = new Snoopy ();
		$snoopy->maxredirs = 0;
		$snoopy->agent = "Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1)";
		// $snoopy->maxframes=3;
	
		$action = "http://bbs.ustc.edu.cn/cgi/bbslogin";
		$bbsaccount=$this->config->item('bbs');
		$formvars ["id"] = $bbsaccount['user'];
		$formvars ["pw"] = $bbsaccount['pw'];
		 
		$snoopy->submit ( $action, $formvars );//login
	
		//save cookies
		$res = iconv ( "gb2312", "UTF-8", $snoopy->results );
		preg_match_all ( "/cookie=\'(utm[^=]+)=([^\']+)\'/i", $res, $matches );
		$snoopy->cookies [$matches [1] [0]] = $matches [2] [0];
		$snoopy->cookies [$matches [1] [1]] = $matches [2] [1];
		$snoopy->cookies [$matches [1] [2]] = $matches [2] [2];
	
		$prefix = "http://bbs.ustc.edu.cn/cgi/";
		$action = "http://bbs.ustc.edu.cn/cgi/bbsedit";
		//$title = "test eng";
		$formdata = array ();
		$formdata ['title'] = iconv ( "utf-8", "gbk", $subject );
		$formdata ['text'] =iconv ( "utf-8", "gbk",$content);
		$formdata ['type'] = "1";
		$formdata ['board'] = "test";
		preg_match_all ( "/M.\d+.A/i", $link, $matches );
		$formdata ['file'] = $matches[0][0];
	
		$formdata ['useattach'] = "true";
		$snoopy->submit ( $action, $formdata ); // 提交
	
		//log out
		$logouturl = "http://bbs.ustc.edu.cn/cgi/bbslogout";
		$snoopy->fetch ( $logouturl );
	}
	
	protected function senttobbs($subject, $content,$repid){
	
		$date = date ( "DMj" );
		$snoopy = new Snoopy ();
		$snoopy->maxredirs = 0;
		$snoopy->agent = "Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1)";
		// $snoopy->maxframes=3;
	
		$action = "http://bbs.ustc.edu.cn/cgi/bbslogin";
		$bbsaccount=$this->config->item('bbs');
		$formvars ["id"] = $bbsaccount['user'];
		$formvars ["pw"] = $bbsaccount['pw'];
		$snoopy->submit ( $action, $formvars );//login
	
		//save cookies
		$res = iconv ( "gb2312", "UTF-8", $snoopy->results );
		preg_match_all ( "/cookie=\'(utm[^=]+)=([^\']+)\'/i", $res, $matches );
		$snoopy->cookies [$matches [1] [0]] = $matches [2] [0];
		$snoopy->cookies [$matches [1] [1]] = $matches [2] [1];
		$snoopy->cookies [$matches [1] [2]] = $matches [2] [2];
		// print_r(iconv("gb2312","UTF-8",$snoopy->results));
	
		/* $action1 = "http://bbs.ustc.edu.cn/cgi/bbstdoc?board=ESS"; */
		// $snoopy->fetch ( $action1 );
		// $res = $snoopy->results;
		/* $html = new simple_html_dom ( iconv ( "gb2312", "UTF-8", $res ) ); */
	
		$prefix = "http://bbs.ustc.edu.cn/cgi/";
		$action = "http://bbs.ustc.edu.cn/cgi/bbssnd?board=test&og=1";
		//$title = "test eng";
		$formdata = array ();
		$formdata ['title'] = iconv ( "utf-8", "gbk", $subject );
		$formdata ['allowre'] = "1";
		$formdata ['labelabc'] = "0";
		$formdata ['signature'] = "1";
		//$formdata ['text'] = htmlentities(iconv ( "utf-8", "gbk", "In 2013, a startup called Outbox drew a lot of attention for its ambitious goal: digitizing everybody's snail mail. \n It was a nice dream; no more walking down your driveway six days a week to clear out the useless junk it contained. But less than a year later, Outbox shut down.\nThis article explains how the United States Postal Service swiftly crushed their plan to make mail better. The founders were summoned to a meeting with the Postmaster General, who told them." ));
		$formdata ['text'] =iconv ( "utf-8", "gbk",$content);
		$formdata ['author'] = "**";
		$formdata ['threadid'] = "0";
		$formdata ['useattach'] = "true";
		$snoopy->submit ( $action, $formdata ); // 提交
		// find url
		$action1 = "http://bbs.ustc.edu.cn/cgi/bbstdoc?board=test";
		$snoopy->fetch ( $action1 );
		$res = $snoopy->results;
		$html = new simple_html_dom ( iconv ( "gb2312", "UTF-8//IGNORE", $res ) );
	
		$date = date ( "DMj" );
		$trs = $html->find ( "tr.old" );
		$href="";
	
		foreach ( $trs as $tr ) {
			$td = $tr->find ( "td.datetime", 0 );
			$pdate = preg_replace ( '/\s+/', '', $td->innertext ); // post date
	
			//echo $pdate . " " . $date . "\n";
			if (stripos ( $pdate, $date ) !== false) {
				$a = $tr->find ( "a.o_title", 0 );
				$text = str_replace ( "\xC2\xA0", '', html_entity_decode ( $a->innertext, ENT_QUOTES, 'UTF-8' ) );
				$author = $tr->find ( "td.author a", 0 )->innertext;
				//echo "author: " . $author . "\n";
				//echo "title: " . $text . " " . trim ( $subject ) . "\n";
				if (stripos ( $text, preg_replace ( '/\s+/', '', $subject ) ) !== false && stripos ( $author, 'coibyxu' ) !== false) {
					$href = $prefix . htmlspecialchars_decode ( $a->href );
					break;
				}
			}
		}
		//add href to database
		$query="update `report` set `bbslink`='".$href."' where id=".$repid;
		$result = mysql_query ( $query );
	
		//log out
		$logouturl = "http://bbs.ustc.edu.cn/cgi/bbslogout";
		$snoopy->fetch ( $logouturl );
	
		return $href;
	}
	
	function test(){
		$data['header'] = $this->html_header("订阅管理");
		$data['footer'] = $this->html_footer();
		$template = $this->site_template . 'user/test';
		$this->load->view($template,$data);
	}
	
}
