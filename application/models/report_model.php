<?php
class Report_model extends CI_Model
{
	protected $table_name = 'report';

	function __construct()
	{
		parent::__construct();
	}

	 
	
	/*
	 * get report list
	 */
	function get_report_list($limit, $offset=0,$thisweek,$state, $condition=""){
		$this->db->select('id,cid,speaker,title,content,place,institution,profile,starttime,length,state,bbslink');
		$this->db->from($this->table_name);
		if($condition!='')
			$this->db->where($condition);
		if($state){
			$this->db->where("(state='1' OR `state`='2')");
		}
		if($thisweek){
			$this->db->where("yearweek(`starttime`, 1)=yearweek(curdate(), 1)");
		}
		if($limit!='' && $offset!=''){
			$this->db->limit($limit, $offset);
		}
		 
		$query = $this->db->get();
		return $query->result_array();
	}
	
	function set_state($id,$state){
		$this->db->where('id', $id);
		$this->db->update($this->table_name, array('state'=>$state));
		 
	}
	
	
	function get_cids($thisweek,$condition)
	{
		$this->db->select('cid')
				 ->group_by('cid');	
		$this->db->where($condition);
		if($thisweek){
			$this->db->where("yearweek(`starttime`, 1)=yearweek(curdate(), 1)");
		}
		$this->db->from($this->table_name);
		$query = $this->db->get();
		return $query->result_array();
	}
	
 	function add($data){
 		return $this->db->insert($this->table_name, $data);
 	}
 	
 	function update($data,$where){
 		return $this->db->update($this->table_name, $data);
 	}
 	

 	function fetch($where){
 		$this->db->select('*');
 		$this->db->where($where);
 		$this->db->from($this->table_name);
 		return $this->db->get()->row_array();
 	}
 

  
}
