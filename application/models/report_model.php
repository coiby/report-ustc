<?php
class Report_model extends CI_Model
{
	protected $table_name = 'report';
    
	protected $table_collec = 'collection';
	
	function __construct()
	{
		parent::__construct();
	}

	 
	
	/*
	 * get report list
	 */
	function get_report_list($limit, $offset=0,$thisweek,$state, $condition=""){
		$this->db->select("id,cid,speaker,title,content,place,institution,profile ,length,state,bbslink");
		$this->db->select("DATE_FORMAT(starttime,'%y-%m-%d %H:%i') AS starttime",FALSE);
		$this->db->order_by('starttime','desc');
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
	
	function bbs_board($cid){
		
		$this->db->select('board');
		$this->db->where('id', $cid);
		$this->db->from($this->table_collec);
		$res= $this->db->get()->row_array();
		return $res['board'];
		
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
 		return $this->db->update($this->table_name, $data,$where);
 	}
 	

 	function fetch($where){
 		$this->db->select('*');
 		$this->db->where($where);
 		$this->db->from($this->table_name);
 		return $this->db->get()->row_array();
 	}
 
 	function latest($cid,$limit=4){
 		$this->db->select('title,speaker');
 		$this->db->limit($limit);
 		$this->db->where("cid",$cid);
 		$this->db->order_by("starttime", "desc");
 		$this->db->from($this->table_name);
 		return $this->db->get()->result_array();
 	}

  
}
