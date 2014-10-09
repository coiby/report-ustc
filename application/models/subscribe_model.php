<?php
class Subscribe_model extends CI_Model
{
	protected $table_name = 'subscribe';
 
	
	function __construct()
	{
		parent::__construct();
	}

	/*
	 * subscribe
	*/
	function subscribe($data){
		if($this->db->insert($this->table_name, $data))
			return $this->db->insert_id();
		return false;
	}

	/*
	 * unsubscribe
	*/
	function unsubscribe($data){
		return $this->db->delete($this->table_name, $data);
	}   
	 
	/*
	 * fetch setting by uid and cid
	 * for bit field value, check https://stackoverflow.com/questions/2914740/php-reading-mysql-bit-field-returning-weird-character
	 */
	function get_setting($where){
		$this->db->select('*');
		$this->db->where($where);
		$this->db->from($this->table_name);
		return $this->db->get()->row_array();
	}
	
	/*
	 * change setting
	*/
	function update_setting($data,$where){
		return $this->db->update($this->table_name, $data,$where);
	}
	
	/*
	 * get name list of users who subscribe to a collection
	 * return array(emails,phone_numbers)
	 */
	function get_namelist($cid){
		$namelist=array();
		$this->db->select('email');
		$this->db->where(array('cid'=>$cid,'byemail'=>1));
		$this->db->from($this->table_name);
		$this->db->join('user', 'uid = user.id');
	 
		foreach($this->db->get()->result_array() as $temp){
			$namelist['emails'][]=$temp['email'];
		}
		
		$this->db->select('mobile');
		$this->db->where(array('cid'=>$cid,'bymsg'=>1));
		$this->db->from($this->table_name);
		$this->db->join('user', 'uid = user.id');
	 
		foreach($this->db->get()->result_array() as $temp){
			$namelist['mobiles'][]=$temp['mobile'];
		}
		
		return $namelist;
	}
	
	
}
