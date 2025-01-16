<?php
/**
 * @package     
 * @subpackage  com_s7dpayments
 **/
//financeiro@depaulanegocios.com.br - email para o pagseguro da Depaula
// No direct access.
defined('_JEXEC') or die;

/********
 Dados do PagSeguro
********/

$data['token']      = '4BFDF68AE7254A34A098202FC29DF249';
$data['email']      = 'contato@pinetreefarm.com.br';
$data['currency']   = 'BRL';

$i = 1;
if(is_array(json_decode(paymentsCart::getCart($cartid,'cartid','products'),true)))
{
  foreach(json_decode(paymentsCart::getCart($cartid,'cartid','products')) as $k => $items)
  {
    $title = $items->course;
    $quantity = 0; 
    foreach($items->criancas as $qnt) {
      $quantity = $quantity + count($qnt);
    }
    
    $data['itemId'.$i] = $items->id;
    $data['itemQuantity'.$i] = $quantity;
    $data['itemDescription'.$i] = '.:: '.s7dPayments::ec($mTitle).' ::. '.s7dPayments::ec(s7dPayments::ec($title));
    $data['itemAmount'.$i] = number_format($items->price , 2, '.', '');
    $i++;
  }
}


//prefixo
$pref  = explode(")",$user-> telefone);
$prefix  = str_replace("(","",$pref[0]);

//Telefone
$phone = preg_replace("/[^0-9]/", "", $pref[1]);

/********
 *Dados do comprador
********/
$data['reference']              = 'REF-'.paymentsCart::getCart($cartid,'cartid','id').'PTF';
$data['senderName']             = s7dPayments::ec($user->name);
$data['senderAreaCode']         = $prefix;
$data['senderPhone']            = $phone;
$data['senderEmail']            = $user->email;
$data['shippingType']           = '1';
$data['shippingAddressStreet']  = s7dPayments::ec($user->endereco);

$url  = 'https://ws.pagseguro.uol.com.br/v2/checkout';
$data = http_build_query($data);
$curl = curl_init($url);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_ENCODING, "");
curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

$xml = curl_exec($curl);

curl_close($curl);

$xml = simplexml_load_string($xml);

?>

<form action="https://pagseguro.uol.com.br/checkout/v2/payment.html" method="post" name="checkout">
  <input type="hidden" name="code" value="<?= $xml->code;?>" />
  <input type="hidden" name="iot" value="button" />
</form>


