<?php
class Report extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->library ( 'email' );
		$this->load->library('simple_html_dom');//http://nithin2889.blogspot.jp/2013/02/php-web-page-scraping-in-codeigniter.html
		$this->load->model('subscribe_model');
		$this->load->model('report_model');
		$this->load->library('BBS');
	}
	public function index() {
		if (! $this->input->is_cli_request ()) {
			echo "This script can only be accessed via the command line" . PHP_EOL;
			return;
		}
		$this->get_micro();
		
	}
	
	/**
	 * case1(two titles): http://ess.ustc.edu.cn/xwxx/xsbg/201411/t20141121_205708.html
	 * 如果有两条同个人的报告，会忽略
	 * Requirements: 报告人 单位 报告题目 报告时间 地点  报告人简介; 一个报告只有一个时间（做两个报告？） 单位不能有空格
	 * 
	 *  
	 */
	public function ess_solid(){
		if (! $this->input->is_cli_request ()) {
			echo "This script can only be accessed via the command line" . PHP_EOL;
			return;
		}
		$url = 'http://ess.ustc.edu.cn/xwxx/xsbg/';
		$cid = 3;
		
		$html = file_get_html( $url );
		
		$prefix = 'http://ess.ustc.edu.cn/xwxx/xsbg/';
		$mtext = '固体地球物理学术报告'; // match text
		
		$lis = $html->find ( "div.page2 ul li" );
		
		$rep_struct=array("cid","bbslink","state","title","speaker","institution","starttime","place","organizer","content","profile","school");
		//get the latest 5 report's speakers
		
		$latest_speakers = array();
		foreach($this->report_model->latest($cid) as $rep){
			$latest_speakers[] = preg_replace('/\s+/','',$rep['speaker']);
			 
		}
		 
		
		$board = 'ess';
		
		mb_internal_encoding("UTF-8");
		
		foreach ( $lis as $li ) {
			$data=array();
			$data['cid']=$cid;
			$data['state']=1;
			$data['school']=2;
			
			$a = $li->find ( "a", 0 );
			
			$text=$a->plaintext;
			
			if(stripos ( $text, $mtext ) === false){
			   continue;
			}
			
			$ptext = preg_replace ( '/\s+/', '', $text ); // post date
		
			//echo $ptext."\n";
			//echo $latest_title."\n";
			//exit;
			//if the report is already fetched, exit
			if(!empty($latest_speakers)){
				foreach($latest_speakers as $latest_speaker){
					//echo mb_strlen($latest_speaker).$latest_speaker."a b\n";
					//echo $ptext."aa\n";
					//$tpos=mb_strpos ( $ptext, $latest_speaker );
					 
					 
					if (mb_strpos ( $ptext, $latest_speaker ) !== false) {
						return false;
					}
					//exit;
				}
			}
				
				
			$href = $prefix . htmlspecialchars_decode ( $a->href );
			
			//$href="http://ess.ustc.edu.cn/xwxx/xsbg/201503/t20150317_212599.html";	
			$html2 = file_get_html ( $href );
			$tab = $html2->find ( "table.middlebg", 0 );
			$contents = $tab->find ( "div.ejcontent td.cc",0 );
				
			$data ['bbslink'] = $href;
				
			$i = 0;

			
			
			$rep = str_replace(array( "\r"), '',html_entity_decode ( $contents->plaintext));
			$rep = rtrim($rep,"\n");
			$rep = preg_replace('/^[(\xc2\xa0)|\s]+/', '',$rep);
			$rep = str_replace(' ', '',$rep);
			$rep = str_replace('单 位', '单位',$rep);
			 
			 
			//continue;
			$m_texts=array('speaker'=>'报告人','institution'=>'单位','title'=>'报告题目','timestr'=>'报告时间','place'=>'地点','profile'=>'报告人简介','content'=>'报告摘要');
			
			
			$positions=array();
			$pos_keys=array();
			
			$i=0;
			
			foreach($m_texts as $key=>$value){
				if(($position=mb_strpos ( $rep, $value))!==false){
					$positions[$i]=$position;
					$pos_keys[$i]=$key;
					$i++;
				}
			}
			if(count($positions)<3)
				continue;
			$size=$i;
		
			//@TODO Do we need to sort these things
			//print_r($pos_keys);
			//print_r($positions);
			//exit;
			//print_r($rep); 
			 
			
			$positions[$size]=mb_strlen($rep);
			$pos_keys[$size]='end';
			$m_texts['end']='';
			 
			for($i=0;$i<$size;$i++){
				$data[$pos_keys[$i]] = rtrim(mb_substr( $rep,$positions[$i]+mb_strlen($m_texts[$pos_keys[$i]])+1, $positions[$i+1]-$positions[$i]-mb_strlen($m_texts[$pos_keys[$i]])-1),"\n");
			}
			//echo $data['timestr']."timetime \n";
			$reg='/(\d{2,4}).*?(\d{1,2}).*?(\d{1,2}).*?(\d{1,2}):(\d{2})-(\d{1,2}):(\d{2})/';//2015年3月27日(星期五) 下午3:00-4:30
			preg_match($reg,$data['timestr'],$result);
			
			$data['length']=((int)$result[6]-(int)$result[4])*60+(int)$result[7]-(int)$result[5];
			
			if(strlen($result[1])==2)
				$result[1]='20'.$result[1];
			
			if(mb_strpos ( $rep, "下午" ))
				$result[4]=(int)$result[4]+12;
			
			$data['starttime'] = $result[1].'-'.$result[2].'-'.$result[3].' '.$result[4].':'.$result[5];
			
			unset($data['timestr']);
			$this->report_model->add($data);
			$id=$this->db->insert_id();
			print_r($data);
			 
			$bbs= new BBS();
			//sent to bbs
		 
			$href = $bbs->post ($data, $id, $board,'[固物学术报告]' );
			
			//update bbslink
			//add href to database
			 
			//print_r($rep);
			//exit;
			
		}
		
	} 
	
	
	protected  function get_math(){
		
	}
	
	protected  function get_micro(){
		//only fetch newest reports
		//$timemk=mktime(0,0,0,date("m"),date("d")-5,date("Y"));
		//$date = date ( "DMj",$timemk );
		//$date = date ( "Y-m-d",$timemk);
		
		$url = "http://www.hfnl.ustc.edu.cn/hfnlnews/xsbg/";
		$cid=2;
		//$content = file_get_html($url);
		//$res=iconv ( 'gbk', 'UTF-8',$content);
		
		$html = file_get_html( $url );
		
		$prefix = "http://www.hfnl.ustc.edu.cn/";
		$mtext = "报告"; // match text
		
		$lis = $html->find ( "ul.text_list li" );
		
		$rep_struct=array("cid","bbslink","state","title","speaker","institution","starttime","place","organizer","content","profile","school");
		//get the latest 5 report's title
		$latest_titles = array();
		foreach($this->report_model->latest($cid) as $rep){
			$latest_titles[] = preg_replace('/\s+/','',$rep['title']);
		}
		
		$data=array();
		$data['cid']=$cid;
		$data['state']=1;
		$data['school']=2;
		foreach ( $lis as $li ) {
			
			$a = $li->find ( "a", 0 );
			$text = iconv ( 'gbk', 'UTF-8', $a->title );
			$ptext = preg_replace ( '/\s+/', '', $text ); // post date
			 
			//echo $ptext."\n";
			//echo $latest_title."\n";
			//if the report is already fetched, exit
			if(!empty($latest_titles)){
				foreach($latest_titles as $latest_title){
					if (stripos ( $ptext, $latest_title ) !== false) {
						return false;
					}
				}
			}
			
			
			$href = $prefix . htmlspecialchars_decode ( $a->href );
			
			$html2 = file_get_html ( $href );
			$tab = $html2->find ( "table.hei", 0 );
			$tds = $tab->find ( "td[align=left]" );
			
			$data ['bbslink'] = $href;
			
			$i = 0;
			foreach ( $tds as $td ) {
				$i ++;
				if ($i != 4) {
					$data [$rep_struct [$i + 2]] = html_entity_decode ( iconv ( 'gbk', 'UTF-8', str_replace ( "&nbsp;", "", $td->plaintext ) ) );
					// html_entity_decode($td->plaintext);
				} else {
					$data [$rep_struct [$i + 2]] = html_entity_decode ( iconv ( 'gbk', 'UTF-8', str_replace ( '&nbsp;&nbsp;', '', str_replace ( '&nbsp;&nbsp;&nbsp;', ' ', $td->plaintext ) ) ) );
				}
			}
			
			$content_profile = $this->parse_rep ( $data ['content'] );
			
			$data ['content'] = $content_profile [1];
			$data ['profile'] = $content_profile [0];
			//print_r ( $data );
			$this->report_model->add($data);
		}
	}
	
	
	
	protected function parse_rep($rep){
		//example1 http://www.hfnl.ustc.edu.cn/2013/1220/4465.html
		//example2 http://www.hfnl.ustc.edu.cn/2013/1231/4494.html
		 
		if($begin=mb_strpos ( $rep, "告摘要" )){
			$begin+=4;
			
		}else{
			$begin=stripos ( $rep, "abstract" ) + 9;
		}
		$len=strlen($rep);
		$end=mb_strpos ( $rep, "人简介" );
		//echo "line is: ".$end." ".strlen($rep)."\n";
		if($end){
			$length=$end-$begin;
			$length2=$len-$end;
			$content = mb_substr ( $rep,$begin , $length-2,'utf-8' );
			$profile = mb_substr ( $rep,$end+4 , $length2,'utf-8' );
		}else{
			$profile="";
			$content = mb_substr ( $rep,$begin , $len,'utf-8' );
		}
		
		$length=$end-$begin;
		$length2=$len-$end;
		
		//echo $rep."\n";
		//echo $profile."\n";
		//echo $content."\n";
		return array($profile,$content);
	}
}
