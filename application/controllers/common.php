<?php
class Common extends CI_Controller{
	var $site_template = '';
	var $token_code = '';

	function __construct()
	{
		parent::__construct();
                //根据域名选择模板
                $domain = $this->input->server('SERVER_NAME');
                $templates = $this->config->item('templates');
                if(isset($templates[$domain])){
                    $this->site_template = $templates[$domain];
                }else{
                    $this->site_template = '';
                }
	}

	//页面头部HTML
	function html_header($title)
	{
		 
		 
		$data['title']=$title; 
		return $this->load->view($this->site_template.'/header', $data, TRUE);
	}

	//页面尾部HTML
	function html_footer()
	{
		return $this->load->view($this->site_template.'/footer', NULL, TRUE);
	}

	//用户中心页面头部HTML
	function html_admin_header()
	{
		$data['user'] = $this->check_token();
		return $this->load->view($this->site_template.'/admin_header', $data, TRUE);
	}

	//用户中心左侧导航
	function html_admin_left()
	{
		$token = $this->is_member();
                $c = (object) array('id'=>$token['uid']);
		$xml = obj2xml($c);
		$url = site_url('api/user/get_user_info');
		$result = $this->xml_post($url, $xml);
		$data['user'] = $result;                
                $o = (object) array('view_state'=>0,
                    'page'=>1,
                    'pagesize'=>10,
                    'token'=>$this->token_code
                    );                
		$xml=obj2xml($o);
		$url=site_url('api/message/inbox');
		$result=$this->xml_post($url,$xml);
		$data['recv_message_cnt']=empty($result['total'])?0:$result['total'];

		$c->page=1;
		$c->page_size=10;
		$c->token=$this->token_code;
		$xml=obj2xml($c);
		$url=site_url('api/userfav/get_page_list');
		$result=$this->xml_post($url,$xml);
		$data['fav_cnt']=empty($result['total'])?0:$result['total'];

		return $this->load->view($this->site_template.'/admin_left', $data, TRUE);
	}

	//用户中心底部
	function html_admin_footer()
	{
		return $this->load->view($this->site_template.'/front/footer', NULL, TRUE);
	}

	function xml_post($url,$xml)
	{
		$header[] = 'Content-type: text/xml;charset=utf-8';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
		if(curl_errno($ch))
		{
			exit(curl_error($ch));
		}
		$data = curl_exec($ch);
		curl_close($ch);
		$data_array = sxml2array($data);
		return $data_array;
	}

	function _user($user_id)
	{
            $c = (object) array();
		$c->id = $user_id;
		$xml = obj2xml($c);
		$url = site_url('api/user/get_user_info');
		$result = $this->xml_post($url,$xml);
		if(isset($result['err'])){
			$this->info($result['err']);
		}
		$user=array();
		if(!isset($result['EMPTY'])){
			$user=$result;
		}
		return $user;
	}

	function _lector($lector_id)
	{
            $c = (object) array();
		$c->id=$lector_id;
		$xml=obj2xml($c);
		$url=site_url('api/lector/get_info');
		$result=$this->xml_post($url,$xml);
		if(isset($result['err'])){
			$this->info($result['err']);
		}
		$lector=array();
		if(!isset($result['EMPTY'])){
			$lector=$result;
		}
		return $lector;
	}

	function _js_unescape($str){
		$ret = '';
		$len = strlen($str);
		for ($i = 0; $i < $len; $i++){
			if ($str[$i] == '%' && $str[$i+1] == 'u'){

						$val = hexdec(substr($str, $i+2, 4));
						if ($val < 0x7f) $ret .= chr($val);
						else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
						else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
						$i += 5;

				}
				else if ($str[$i] == '%'){

						$ret .= urldecode(substr($str, $i, 3));

						$i += 2;

				}
				else $ret .= $str[$i];
		}
		return $ret;

	}
}