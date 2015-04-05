<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class BBS {
	private $ci;
	private $snoopy;
	public function __construct() {
		$this->ci = & get_instance ();
		$this->ci->load->library ( 'simple_html_dom' );
		$this->ci->load->library ( 'Snoopy' );
		
	}
	
	function login(){
		//log in
		$this->snoopy = new Snoopy ();
		$this->snoopy->maxredirs = 0;
		$this->snoopy->agent = "Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1)";
		// $this->snoopy->maxframes=3;
		
		$action = "http://bbs.ustc.edu.cn/cgi/bbslogin";
		$bbsaccount = $this->ci->config->item ( 'bbs' );
		$formvars ["id"] = $bbsaccount ['user'];
		$formvars ["pw"] = $bbsaccount ['pw'];
		$this->snoopy->submit ( $action, $formvars ); // login
		
		// save cookies
		$res = iconv ( "gb2312", "UTF-8", $this->snoopy->results );
			
		preg_match_all ( "/cookie=\'(utm[^=]+)=([^\']+)\'/i", $res, $matches );
		$this->snoopy->cookies [$matches [1] [0]] = $matches [2] [0];
		$this->snoopy->cookies [$matches [1] [1]] = $matches [2] [1];
		$this->snoopy->cookies [$matches [1] [2]] = $matches [2] [2];
		//print_r($matches);
		//exit;
	}
	
	function bbs_body($report) {
		$bbsb = ""; // mailbody
		            // @TODO feature: 通知具体改变
		$bbsb = "报告人：" . $report['speaker'] . "\n"; // mailbody
		if (! empty ( $report['institution'] )) {
			$bbsb = $bbsb . "单位：" . $report['institution'] . "\n";
			// $bbsb="单位：".$row['institution']."\n";
		}
		// deal with date + time
		/*
		 * $dt=new DateTime($row['starttime']); $date= $date = $dt->format('m/d/Y'); $time = $dt->format('H:i');
		 */
		$enditme = ($report ['starttime'] . " " . $report ['length'] . " minute");
		$dt = new DateTime ( $enditme );
		$end = $dt->format ( 'H:i' );
		
		$bbsb = $bbsb . "时间：" . $report ['starttime'] . "-" . $end . "\n\n";
		
		if (! empty ( $report ['profile'] ))
			$bbsb = $bbsb . "报告人介绍\n" . $report ['profile'] . "\n\n";
		
		if (! empty ( $report ['content'] ))
			$bbsb = $bbsb . "报告摘要\n" . $report ['content'];
		$bbsb .= $bbsaccount = $this->ci->config->item('promote_msg');
		return $bbsb;
	}
	function post( $report, $repid, $board,$title_prefix='') {
		$this->login();
		$date = date ( "DMj" );
		
		// print_r(iconv("gb2312","UTF-8",$snoopy->results));
		
		/* $action1 = "http://bbs.ustc.edu.cn/cgi/bbstdoc?board=ESS"; */
		// $snoopy->fetch ( $action1 );
		// $res = $snoopy->results;
		/* $html = new simple_html_dom ( iconv ( "gb2312", "UTF-8", $res ) ); */
		
		$prefix = "http://bbs.ustc.edu.cn/cgi/";
		$action = 'http://bbs.ustc.edu.cn/cgi/bbssnd?board=' . $board . '&og=1';
		// $title = "test eng";
		$formdata = array ();
		$formdata ['title'] = iconv ( "utf-8", "gb2312", $title_prefix.$report['title'] );
		$formdata ['allowre'] = "1";
		$formdata ['labelabc'] = "0";
		$formdata ['signature'] = "1";
		// $formdata ['text'] = htmlentities(iconv ( "utf-8", "gbk", "In 2013, a startup called Outbox drew a lot of attention for its ambitious goal: digitizing everybody's snail mail. \n It was a nice dream; no more walking down your driveway six days a week to clear out the useless junk it contained. But less than a year later, Outbox shut down.\nThis article explains how the United States Postal Service swiftly crushed their plan to make mail better. The founders were summoned to a meeting with the Postmaster General, who told them." ));
		 
		$formdata ['text'] = iconv ( "utf-8", "gb2312", $this->bbs_body($report));
		 
		
		
		$formdata ['author'] = "**";
		$formdata ['threadid'] = "0";
		$formdata ['useattach'] = "true";
		$this->snoopy->submit ( $action, $formdata ); // 提交
		                                        // find url
		$action1 = 'http://bbs.ustc.edu.cn/cgi/bbstdoc?board=' . $board;
		$this->snoopy->fetch ( $action1 );
		$res = $this->snoopy->results;
		$html = new simple_html_dom ( iconv ( "gb2312", "UTF-8//IGNORE", $res ) );
		
		$date = date ( "DMj" );
		$trs = $html->find ( "tr.old" );
		$href = "";
		
		foreach ( $trs as $tr ) {
			$td = $tr->find ( "td.datetime", 0 );
			$pdate = preg_replace ( '/\s+/', '', $td->innertext ); // post date
			                                                       
			// echo $pdate . " " . $date . "\n";
			if (stripos ( $pdate, $date ) !== false) {
				$a = $tr->find ( "a.o_title", 0 );
				$text = str_replace ( "\xC2\xA0", '', html_entity_decode ( $a->innertext, ENT_QUOTES, 'UTF-8' ) );
				$author = $tr->find ( "td.author a", 0 )->innertext;
				// echo "author: " . $author . "\n";
				// echo "title: " . $text . " " . trim ( $subject ) . "\n";
				if (stripos ( $text, preg_replace ( '/\s+/', '', $report['title'] ) ) !== false && stripos ( $author, 'coibyxu' ) !== false) {
					$href = $prefix . htmlspecialchars_decode ( $a->href );
					break;
				}
			}
		}
		// add href to database
		$query = "update `report` set `bbslink`='" . $href . "' where id=" . $repid;
		$result = mysql_query ( $query );
		
		// log out
		$this->logout();
		//exit($href."aabbcc\n");
		
		return $href;
	}
	function update($report, $link,$board, $repid) {
		$this->login();
		
		$prefix = "http://bbs.ustc.edu.cn/cgi/";
		$action = "http://bbs.ustc.edu.cn/cgi/bbsedit";
		// $title = "test eng";
		$formdata = array ();
		$formdata ['title'] = iconv ( "utf-8", "gbk", '[更新]'.$report['title'] );
		$formdata ['text'] = iconv ( "utf-8", "gbk", $this->bbs_body($report) );
		$formdata ['type'] = "1";
		$formdata ['board'] = $board;
		preg_match_all ( "/M.\d+.A/i", $link, $matches );
		
		$formdata ['file'] = $matches [0] [0];
	
		$formdata ['useattach'] = "true";
		$this->snoopy->submit ( $action, $formdata ); // 提交
		                                        
		// log out
		$this->logout();
	}
	
	function __destruct() {
		$this->logout();
	}
	
	function logout(){
		$logouturl = "http://bbs.ustc.edu.cn/cgi/bbslogout";
		$this->snoopy->fetch ( $logouturl );
	}
}