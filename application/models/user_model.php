<?php
class User_model extends CI_Model
{
	protected $table_name = 'user';
	protected $subcribe_name = 'subscribe';
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('bcrypt');
	}

	 

	function set_state($id)
	{
		$state = $this->getMax('state', array('id'=>$id));
		$data['state'] = ($state == 1) ? 0 : 1;
		return $this->edit($data, array('id'=>$id));
	}

	
	
	function add_login_log($arr)
	{
		if((array_key_exists('name', $arr) && array_key_exists('pass', $arr)) == false)
		{
			return false;
		}

		$name = $arr['name'];
		$pass = $arr['pass'];

		$ip = ip2long($this->input->ip_address());
		$this->db->set('login_count', 'login_count+1', false);
		$this->db->set('last_login_time', time());
		$this->db->set('last_login_ip', $ip);
		$this->db->where('name', $name);
		$this->db->update($this->table_name);

		$data = array(
			'name' => $name,
			'pass' => $pass,
			'login_time' => time(),
			'login_ip' => $ip,
			'state' => 1
		);
		return $this->db->insert('user_login', $data);
	}
	
	/**
	 * create user
	 */
	function createUser($data){
		$data['pass']=$this->bcrypt->hash($data['pass']);
		return $this->db->insert($this->table_name, $data);
	}
	
	/**
	 * get user by uid 
	 * 
	 * @param int userid
	 */
	function getUserByUid($uid){
		$rst = $this->db->select('name')->where(array('id'=>$uid))->get($this->table_name);
		return $rst->row_array();
	}	

	/**
	 * get user by Email
	 *
	 * @param int Email
	 */
	function getUserByEmail($email){
		$rst = $this->db->select('id,email,mobile')->where(array('email'=>$email))->get($this->table_name);
		return $rst->row_array();
	}
	
	/**
	 * check user by email and password
	 *
	 * @param string email
	 */
	function checkUser($email,$pw){
		if (empty ( $email ) || empty ( $pw )) {
			return false;
		}
		$query = $this->db->select ( 'id,pass' )->where ( array (
				'email' => $email 
		) )->get ( $this->table_name );
		if ($query->num_rows () === 1) {
			$user = $query->row ();
			if ($this->bcrypt->verify ( $pw, $user->pass )){
				$sessiondata=array('user'=>$email,'id'=>$user->id);
				$this->session->set_userdata($sessiondata);
				return true;
			}
		}
		return false;
	}
	
	
	
	function user_exist($email){
		return $this->db->select('id,email')->where(array('email'=>$email))->count_all_results($this->table_name)>0;
	}
	
	/**
	 * get user's subscription via uid
	 * @param int uid
	 */
	function getSubs($uid){
		//$query="select id,name,scode from school";
		$this->db->select('collection.id as cid, collection.name as cname, intro,school.scode as scode');
		$this->db->from('collection');
		$this->db->join('school', 'school.id = collection.sid');
		$res=$this->db->get();
		
		$subs=array();
		foreach ( $res->result_array() as $row ) {
			$query="select uid,cid from ".$this->subcribe_name." where cid=".$row['cid']." and uid=".$uid;
			$sub=array('cid'=>$row['cid'],'name'=>$row['cname'],'scode'=>$row['scode'],'intro'=>$row['intro']);
		
			$result2=$this->db->query($query);
		 
			if($result2->num_rows()!=0){
				$sub['yes']=1;
			}else{
				$sub['yes']=0;
			}
			$subs[]=$sub;
		}
		return $subs;
	}
	
	function change_password($email,$pw){
		$data = array(
               'pass' => $this->bcrypt->hash($pw)
            );

		$this->db->where('email', $email);
		return $this->db->update($this->table_name, $data); 
	}
	
	public function clear_reset_password_code($email) {
	
		if (empty($email))
		{
			return FALSE;
		}
	
		$this->db->where(array('email'=>$email));
	
		 
		$data = array(
					'forgotten_password_code' => NULL,
					'forgotten_password_time' => NULL
			);
	
		$this->db->update($this->table_name, $data);
	
		return false;
	}
	
	function reset_pw_code($email){
		$this->load->helper('string');
		$resetpwcode=random_string('sha1',10);
		$resetpwtime=time();
		$data=array('forgotten_password_code'=>$resetpwcode,'forgotten_password_time'=>$resetpwtime);
		
		if($this->db->update($this->table_name,$data,array('email'=>$email))){
			$this->load->library ( 'email' );
			$this->email->from ( "coiby@mail.ustc.edu.cn" );
			$this->email->bcc ( $email );
			$this->email->subject ( "密码重置" );
			$link=config_item('base_url')."user/reset_pw?code=".$resetpwcode."&email=".$email;
			$this->email->message ("请点击下面的链接（一小时内有效）重置您的密码 \n"."<a href='".$link."'>".$link."</a>" );
			$this->email->send ();
			 
		}
	}
	function reset_pw_check($email,$code){
		 
		$rst=$this->db->select('forgotten_password_time')->where(array('email'=>$email,'forgotten_password_code'=>$code))->get($this->table_name);
		
		if ($rst->num_rows()>0)
		{
			$profile=$rst->row_array();
			$codetime = $profile['forgotten_password_time'];
			if (time() - $codetime <= 3600) {
				//it hasn't expired	  
				return true;
			} 
		}
		return false;
		 
	}
}
