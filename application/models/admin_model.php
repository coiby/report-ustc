<?php
class Admin_model extends CI_Model
{
	protected $table_name = 'admin';
	 
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
	
	/*
	 * create user
	 */
	function createUser($data){
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
	 * get user by name
	 *
	 * @param int name
	 */
	function getUserByName($name){
		$rst = $this->db->select('id,name,cid')->where(array('name'=>$name))->get($this->table_name);
		return $rst->row_array();
	}
	
	/**
	 * check user by name and password
	 *
	 * @param string name
	 */
	function checkUser($name,$pw){
		$query = $this->db->select('id,name,cid,pass')->where(array('name'=>$name))->get($this->table_name);
		if ($query->num_rows() === 1) {
			$user = $query->row_array();
			if ($this->bcrypt->verify ( $pw, $user['pass'] )){
				return $user;
			}
			return false;
		} 
		return false;
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
}
