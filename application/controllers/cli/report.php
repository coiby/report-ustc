<?php
class Report extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->library ( 'input' );
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
		//每天检查当天的报告
		$timemk=mktime(0,0,0,date("m"),date("d")+3,date("Y"));
		//$date = date ( "DMj",$timemk );
		$date = date ( "Y-m-d",$timemk);
		
		$url = "http://www.hfnl.ustc.edu.cn/hfnlnews/xsbg/";
		
		//$content = file_get_html($url);
		//$res=iconv ( 'gbk', 'UTF-8',$content);
		
		$html = file_get_html( $url );
		
		$prefix = "http://www.hfnl.ustc.edu.cn/";
		$mtext = "报告"; // match text
		
		$lis = $html->find ( "ul.text_list li" );
		
		$rep_struct=array("cid","bbslink","state","title","speaker","institution","starttime","place","organizer","content","profile","school");
		
		foreach ( $lis as $li ) {
		
			$a = $li->find ( "a", 0 );
			$text=$a->innertext;
			$pdate = preg_replace('/\s+/','',$text); // post date
			 
			if (stripos ( $pdate, $date ) !== false) {

				//echo html_entity_decode($text)."\n";
				$href = $prefix . htmlspecialchars_decode ( $a->href );
				//echo $href."\n";
				//$rep =file_get_html($href);
				//$res2=iconv ( 'gbk', 'UTF-8',$rep);
				$html2 = file_get_html($href);
				$tab=$html2->find ( "table.hei",0);
				$tds = $tab->find ( "td[align=left]");
				//var_dump($tab->plaintext);
				$data=array();
				$data['cid']=2;
				$data['bbslink']=$href;
				$data['state']=1;
				$i=0;
				foreach ( $tds as $td ) {
					$i++;
					if($i!=4){
						$data[$rep_struct[$i+2]]=html_entity_decode(iconv ( 'gbk', 'UTF-8',str_replace("&nbsp;","",$td->plaintext)));
						//html_entity_decode($td->plaintext);
					}else{
						$data[$rep_struct[$i+2]]= html_entity_decode(iconv ( 'gbk', 'UTF-8',str_replace('&nbsp;&nbsp;','',str_replace('&nbsp;&nbsp;&nbsp;',' ',$td->plaintext))));
					}
				}
				//print_r($data);
				$content_profile=$this->parse_rep($data['content']);
				//print_r($data['content']);
				echo "\n";
				//print_r($content_profile);
				echo "\n";
				$data['content']=$content_profile[0];
				$data['profile']=$content_profile[1];
				$data['school']=2;
				
				$this->report_model->add_report($data);
		
			}
		}
	}
	
	
	
	protected function parse_rep($rep){
		//example1 http://www.hfnl.ustc.edu.cn/2013/1220/4465.html
		//example2 http://www.hfnl.ustc.edu.cn/2013/1231/4494.html
		 
		if($begin=mb_strpos ( $rep, "告摘要" )){
			$begin+=7;
			
		}else{
			$begin=stripos ( $rep, "abstract" ) + 9;
		}
		$end=mb_strpos ( $rep, "告人简介" );
		//echo "line is: ".$end." ".strlen($rep)."\n";
		$len=strlen($rep);
		$length=$end-$begin;
		$length2=$len-$end;
		$profile = mb_substr ( $rep,$begin , $length-2,'utf-8' );
		$content = mb_substr ( $rep,$end+9 , $length2,'utf-8' );
		//echo $rep."\n";
		//echo $profile."\n";
		//echo $content."\n";
		return array($profile,$content);
	}
}
