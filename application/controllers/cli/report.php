<?php
class Report extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->library ( 'email' );
		$this->load->library('simple_html_dom');//http://nithin2889.blogspot.jp/2013/02/php-web-page-scraping-in-codeigniter.html
		$this->load->model('subscribe_model');
		$this->load->model('report_model');
	}
	public function index() {
		if (! $this->input->is_cli_request ()) {
			echo "This script can only be accessed via the command line" . PHP_EOL;
			return;
		}
		$this->get_micro();
		
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
