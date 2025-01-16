<?php
/**
 * @package     
 * @subpackage  com_s7dpayments
**/

// No direct access.
defined('_JEXEC') or die;

$nCode = preg_replace('/[^[:alnum:]-]/','',$_POST["notificationCode"]);

$data['token']      = '4BFDF68AE7254A34A098202FC29DF249';
$data['email']      = 'contato@pinetreefarm.com.br';

$data = http_build_query($data);

$url  = 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications/'.$nCode.'?'.$data;

$curl = curl_init();

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, $url);

$xml = curl_exec($curl);

curl_close($curl);

$xml = simplexml_load_string($xml);

$reference = $xml->reference;
$status = $xml->status;

if($reference && $status){
	if(!empty( array_shift(s7dPayments::getPagments('id','ref',$reference))->id )){
		s7dPayments::setCoursesUpdate($reference,$status);
	}
}

