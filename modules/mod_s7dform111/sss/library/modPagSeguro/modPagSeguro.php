<?php
/**
 * @package     
 * @subpackage  mod Wk contact
 **/

// No direct access.
defined('_JEXEC') or die;

/********
 Classe do PagSeguro.
 Desenvolvido por Carlos (IBS WEB)
********/

class wkModPagSeguro {
  
  protected $token;
  protected $email;
  protected $currency;
  protected $item = array();
  protected $reference;
  protected $sender = array();
  protected $menSuccess;

  public function __construct( $token, $email,$currency,$item,$reference = null,$sender = null,$menSuccess)
  {
    $this->token      = $token;
    $this->email      = $email;
    $this->currency   = $currency;
    $this->item       = $item;
    $this->sender     = $sender;
    $this->reference  = $reference;
    $this->menSuccess = $menSuccess;
  }
  
  public function setData(){
      
    list($itemId,$itemQuantity,$itemDescription,$itemAmount) = $this->item;
    list($senderName,$senderPhone,$senderEmail,$shippingType,$shippingAddressStreet) = $this->sender;

    $data['token']      = $this->token; //'4BFDF68AE7254A34A098202FC29DF249';
    $data['email']      = $this->email; //'contato@pinetreefarm.com.br';
    $data['currency']   = $this->currency; //'BRL';

    $data['itemId1']           = $itemId;
    $data['itemQuantity1']     = $itemQuantity;
    $data['itemDescription1']  = iconv('UTF-8', 'ISO-8859-1',$itemDescription);
    $data['itemAmount1']       = number_format($itemAmount , 2, '.', '');

    /********
      *Dados do comprador
    ********/
    //prefixo
    $pref  = explode(")",$senderPhone);
    $prefix  = str_replace("(","",$pref[0]);

    //Telefone
    $phone = preg_replace("/[^0-9]/", "", $pref[1]);
    
    $data['reference']              = $this->reference;
    $data['senderName']             = iconv('UTF-8', 'ISO-8859-1',$senderName);
    $data['senderAreaCode']         = $prefix;
    $data['senderPhone']            = $phone;
    $data['senderEmail']            = $senderEmail;
    $data['shippingType']           = $shippingType;
    $data['shippingAddressStreet']  = $shippingAddressStreet;

    return $data;
  }

  public function checkout(){

    $url  = 'https://ws.pagseguro.uol.com.br/v2/checkout';
    $data = http_build_query( self::setData() );
    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_ENCODING, "");
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

    $xml = curl_exec($curl);

    if($xml == 'Unauthorized')
    {
      echo 'Não autorizado.<br>Token ou e-mail não são válidos.';
      return false;
    }
      
    curl_close($curl);
    $xml = simplexml_load_string($xml);
      
    $formId = uniqid();
      
    if($xml->error)
    {
      echo $xml->error->message;
      return false;
    }

    $output[] = '<form action="https://pagseguro.uol.com.br/checkout/v2/payment.html" method="post">';
    $output[] = '<input type="hidden" name="code" value="'.$xml->code.'" />';
    $output[] = '<input type="hidden" name="iot" value="button" />';
    $output[] = '<input type="submit" name="sub" id="'.$formId.'" value="" style="display:none" />';
    $output[] = '</form>';
    $output[] = '<p id="wkPTemp">'.$this->menSuccess.'<img src="'.Juri::base(true).'/modules/mod_wkcontact/assets/images/pagLoading.gif" alt="Loadiong"/></p>';
    $output[] = '<script type="text/javascript" src="https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script>';
    $output[] = '<script>
    setTimeout(function(){
      document.getElementById("'.$formId.'").click();
      document.getElementById("wkPTemp").remove();
    },9600);
    </script>';

    echo implode('',$output);
  }

}

