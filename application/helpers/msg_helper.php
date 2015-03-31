<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *   Message Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Coiby
 */
if ( ! function_exists('getWSDLClient'))
{
function getWSDLClient(){
	//http://forums.devnetwork.net/viewtopic.php?f=1&t=65543
	$msgwsdl=config_item('msgwsdl');
	$client = new SoapClient($msgwsdl['url']);
	$client->soap_defencoding = 'utf-8';
	$client->xml_encoding = 'utf-8';
	$client->wsClientSetCharset('UTF-8','UTF-8');
	
	$dologin=true;
	//is soapcookies already set? (must be already logged in)
	if (isset($_SESSION['soapcookies']))
	{
		// just set the cookies
		foreach ( $_SESSION ['soapcookies'] as $cookiename => $value ) {
			$client->__setCookie ( $cookiename, $value [0]);
		}
		 
		if($client->wsCsCheckLogin()){
			$dologin=false;
		}
		
	}
	
	
	if($dologin){
		// dologin($client); // do whatever needed to initiate the connection.
		$result = $client->wsCsLogin ( $msgwsdl['user'], $msgwsdl['pw'] );
		// save the _cookies
		
		$_SESSION ['soapcookies'] = $client->_cookies;
	} 
	
		return $client;
	 
}
}