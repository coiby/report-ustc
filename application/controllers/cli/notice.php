<?php
class Notice extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->library ( 'input' );
		$this->load->library ( 'email' );
		$this->load->model('subscribe_model');
		$this->load->model('report_model');
	}
	public function index() {
		if (! $this->input->is_cli_request ()) {
			echo "This script can only be accessed via the command line" . PHP_EOL;
			return;
		}
		
		
		
		//fetch reports, group by cid(collection)
		
		$cids=$this->report_model->get_cids(true,"(state='1' OR `state`='2')");
		$this->load->helper('msg');
		$client=getWSDLClient();
		foreach($cids as $cid){
			$reports=$this->report_model->get_report_list("","",true,true,array("cid"=>$cid['cid']));
			//get the name list who should receive notification of this report
			$namelist=$this->subscribe_model->get_namelist($cid['cid']);
			echo "namelist:\n";
			print_r($namelist);
			$sendmail=false;
			if(!empty($namelist['emails'])){
				$this->email->from('coiby@mail.ustc.edu.cn','Coiby');
				$this->email->reply_to('coibyxqx@qq.com', 'Coiby');
				$sendmail=true;
			}
			
			foreach ( $reports as $report ) {
				// preparing mail and msg content
				$email = $this->generate_mailbody ( $report );
				if ($sendmail) {
					$this->email->bcc ( $namelist ['emails'] );
					$this->email->subject ( $email ['title'] );
					$this->email->message ( $email ['mb'] );
					$this->email->send ();
					// echo $this->email->print_debugger();
				}
				if (! empty ( $namelist ['mobiles'] )) {
					//remove seconds from $report ['starttime']
					$messageContent = $email ['title'] . "\n" . "时间：" . $report ['starttime'] . "\n" . "地点：" . $report ['place']. "\n" . "报告人：" . $report ['speaker'];
					$messageId = $client->wsCreateMessage ( "", $messageContent, "", "", "plaintext" );
					
					foreach ( $namelist ['mobiles'] as $mobile ) {
						if (trim ( $mobile ) !== "") {
							$client->wsMessageAddReceiver ( $messageId, 'mobile', $mobile, 'sms', 1 );
							// wsMessageAddReceiver(messageId,’mobile,’1234567890,’sms’,messagePriority=1)
						}
					}
					$client->wsMessageSend ( $messageId );
					$client->wsMessageClose ( $messageId );
				}
				$this->report_model->set_state ( $report ['id'], 3 );
			}
		}
		
	
		 
	}
	
	protected function generate_mailbody($report){
		$mb = ""; // mailbody
		        // TODO feature: 通知具体改变
		$mb = "<p>报告人：" . $report ['speaker'] . "</p>"; // mailbody
		                                         // $bbsb="报告人：".$report['speaker']."\n";
		if (! empty ( $report ['institution'] )) {
			$mb = $mb . "<p>单位：" . $report ['institution'] . "</p>";
			// $bbsb="单位：".$report['institution']."\n";
		}
		// deal with date + time
		/*
		 * $dt=new DateTime($report['starttime']); $date= $date = $dt->format('m/d/Y'); $time = $dt->format('H:i');
		 */
		
		$enditme = ($report ['starttime'] . " " . $report ['length'] . " minute");
		$dt = new DateTime ( $enditme );
		$end = $dt->format ( 'H:i' );
		
		$mb = $mb . "<p>时间：" . $report ['starttime'] . "-" . $end . "</p>";
		$mb = $mb . "<p>地点：" . $report ['place'] . "</p><p></p>";
		
		if (! empty ( $report ['profile'] ))
			$mb = $mb . "<h2>报告人介绍</h2>" . "<p>" . nl2br($report ['profile']) . "</p><p></p>";
		
		if (! empty ( $report ['content'] ))
			$mb = $mb . "<h2>报告摘要</h2>" . "<p>" . nl2br($report ['content']) . "</p>";
		$mb .=  nl2br($this->config->item('promote_msg'));
			/* $patterns = array (); */
			// $patterns [0] = "/<p>([\w\W]*?)<\/p>/";
			// $patterns [1] = "/<h2>([\w\W]*?)<\/h2>/";
			
		// $replacement = "\${1}\n";
			
		/* $bbsb = preg_replace ( $patterns, $replacement, $mb ); */
							/* $bbsb = str_replace ( "<p>","", $mb );
							$bbsb = str_replace ( "</p>","\n", $bbsb );
							$bbsb = str_replace ( "<h2>","", $bbsb );
							$bbsb = str_replace ( "</h2>","：", $bbsb ); */
		
		if ($report ['state'] == 1) {
			$title = $report ['title'];
			
			// senttobbs ( $title, $bbsb, $report['id'] );
		} else if ($report ['state'] == 2) {
			$title = "[更新]" . $report ['title'];
			// updatebbs( $title, $bbsb, $report['bbslink'], $report['id'] );
		}
		// sendmail ($title , $mb );
		return array('title'=>$title,'mb'=>$mb);
		//sendmsg ( $title, $report ['place'], $report ['starttime'] );
		// change state
		//$query = "update `report` set `state`='3' where id=" . $report ['id'];
		//$result2 = mysql_query ( $query );
	}
}
