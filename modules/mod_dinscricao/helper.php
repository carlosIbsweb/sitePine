<?php

/**
 * @subpackage  mod_dinscricao
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

require_once "elements/recaptchalib.php";

class modDinscricaoHelper
{

	static function getItems(&$params)
	{
		
	}
	public function getAjax()
	{
	
		$input = JFactory::getApplication()->input;

		//inputs
		$inputs = $input->get('data', array(), 'ARRAY');

		return self::setMail($inputs);


	}

	/*
	 *Função responsável por enviar o email para finalizar a compra do curso.
	 * By Carlos
	*/
	public static function setMail($inputs)
	{
		$mail = JFactory::getMailer();

	  	$app = JFactory::getApplication();
  		$module = JModuleHelper::getModule('mod_dinscricao');
  		$params = new JRegistry($module->params);

  		$itemid = $module->id;

		$doc = JFactory::getDocument();

		$pMail = explode(",",trim($params->get('mail')));

		$recipient = count($pMail) == 1 ? $params->get('mail') : $pMail;

		foreach($inputs as $input)
		{
			if($input['value'] != ''){
				$$input['name'] = $input['value'];
			}

			//Código recaptcha
			if($input['name'] == 'g-recaptcha-response'):
				$drecaptcha = $input['value'];
			endif;

		}

		preg_match_all("/{(.+?)}/i", $params->get('text'), $text);

		//Carregando imagens
		preg_match_all("/src=\"(.+?)\"/", $params->get('text'), $imgs);

		$conj = array_merge($text[0],$imgs[1]);

		$texto = [];
		foreach($text[0] as $in=> $nms):
			$names = $nms;
			$valNames = str_replace(array("}","{"),array("",""),$names);

			$texto[] .= $$valNames;
		endforeach;

		$images = [];
		foreach($imgs[1] as $in=> $nms):
			$imgs = $nms;
			$imgsNames = str_replace(array("}","{"),array("",""),$imgs);

			$texto[] .= Juri::base().$imgs;
		endforeach;


		//Subject
		$subject = $params->get('subject');

		$message = str_replace($conj, $texto,$params->get('text'));
		
		$sender = array($params->get('emailsender'), $params->get('namesender'));
		$mail->setSender($sender);
		$mail->addRecipient($recipient);
		$mail->setSubject($subject);
		$mail->isHTML(true);
		$mail->Encoding = 'base64';
		$mail->setBody($message);

		//recaptcha
		$recaptcha = self::getReCaptcha('6LeT5hkUAAAAAIOTYZmY6z3ZmTwsLTDW2PmfGB2H','6LeT5hkUAAAAAEi-FedLLz2yBOFL9KjDEq1dGMqF',$drecaptcha);

		if($recaptcha){
			if ($mail->Send()) {
				//return '<span class="sppb-text-success">'. JText::_('Mensagem enviada com sucesso.') .'</span>';
				//Pagseguro
				return self::getPagseguro($inputs,1);
				
			} else {
				return '<span class="sppb-text-danger">'. JText::_('Falha ao enviar e-mail.') .'</span>';
			}
		}else{
			return '<span class="sppb-text-danger">Código Captcha inválido</div>';
		}
	}

	/*
	 *Função responsáve por gerar o captcha do google.
	 *By Carlos
	*/
	public static function getReCaptcha($dkey,$dsecret,$response)
	{

	// Register API keys at https://www.google.com/recaptcha/admin
	    $siteKey = $dkey;//Chave Publica
	    $secret = $dsecret; //Chave Privada
	// reCAPTCHA supported 40+ languages listed here: https://developers.google.com/recaptcha/docs/language
	    $lang = "pt-BR";

	// The response from reCAPTCHA
	    $resp = null;
	// The error code from reCAPTCHA, if any
	    $error = null;

	    $reCaptcha = new ReCaptcha($secret);

	// Was there a reCAPTCHA response?
	    if ($response) 
	    {
	        $resp = $reCaptcha->verifyResponse(
	        $_SERVER["REMOTE_ADDR"],
	        $response
	        );
	    }
	    if ($resp != null && $resp->success) 
	    {
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	}


	/*
	 *Função responsável por buscar usuários.
     *By Carlos
	*
	*/
	protected function getUsers($inputs)
     {

     	foreach($inputs as $input)
		{
			if($input['value'] != ''){
				switch($input['name']){
					case 'name':
						$name = $input['value'];
						break;	
				}
			}
		}

        $db =& JFactory::getDBO();
        $pname = '"'.$name.'"';
        $db->setQuery('SELECT #__users.username FROM #__users WHERE  username ='. $pname);
        return count($db->loadResult());

     }

	
	/*
	 *Função responsável por fazer registro no site.
	 *By Carlos
	*/
	protected function register($inputs)
     {
       	 foreach($inputs as $names):
            if($names['name'] != '' and $names['name'] != 'password' and $names['name'] != 'password2' and $names['name'] != 'block' and $names['name'] != 'activation' and $names['name'] != 'username'):
                $nm .= "`".$names['name']."`,";
                $ps .= "'".$names['value']."',";
            endif;
        endforeach;

        //Verificando senhas
        foreach($inputs as $npass):
           if($npass['name'] == 'password'):
           		$pass1 = $npass['value'];
           	endif;
           	if($npass['name'] == 'password2'):
           		$pass2 = $npass['value'];
           	endif;
        endforeach;

        $nms = substr($nm, 0,-1);
        $pstss = substr($ps,0,-1);

        $curses = self::getUsers($inputs);
        //$cemail = $this->getUsers('email');

        //Capturar os dados do E-mail/Usuário
        $emLogin = "'".$_POST['email']."'";


        //Gerando senha formato joomla.
        $salt = JUserHelper::genRandomPassword(32);
        $crypt = JUserHelper::getCryptedPassword($pass1, $salt);
        $password = "'".$crypt . ':' . $salt."'";

        //Validando campo senhas iguais.
        $passok = ($pass1 == $pass2 && (!empty($pass1) && !empty($pass2))) ? true : false;

        $activation = "'".md5(uniqid(rand(),true))."'";

        $tokenmail = str_replace("'",'',$activation);

        //pdfToken
        $idPdfToken = "'".md5(uniqid(rand(), true))."'";


        //Inserindo os dados do usúario;
        $db =& JFactory::getDBO();

        $query = "INSERT INTO `#__users` ($nms,`password`,`activation`,`block`,`username`)
        VALUES ($pstss,$password,$activation,'1','viadinho');";
        $db->setQuery( $query );

        
        if($curses != 0):

            echo '<span class="maviso">Nome de usuário já está em uso</span>';
            echo '<a href="javascript:window.history.back()" class="btcontinue">Continuar</a>';

        elseif($cemail != 0):

            echo '<span class="maviso">Email já está em uso</span>';
            echo '<a href="javascript:window.history.back()" class="btcontinue">Continuar</a>';

        elseif($fType != 'image/png' and $fType != 'image/gif' and $fType != 'image/jpeg' and $fType != 'image/bmp' && ($_POST['perfil'] == 'instituicao' && !empty($_FILES['img']['type']))):

            echo '<span class="maviso"> Foto com formato inválido<br /> Somente arquivos (png,gif,jpeg,bmp)</span>';
            echo '<a href="javascript:window.history.back()" class="btcontinue">Continuar</a>';

        elseif(!$passok):
            
            echo '<span class="maviso">Senha inválida</span>';
            echo '<a href="javascript:window.history.back()" class="btcontinue">Continuar</a>';

        else:
            $insert = $db->query();

            $this-> completEmail($_POST['email'],'carlos@ibsweb.com.br',$_POST['name'],$_POST['email'],$_POST['password'],$tokenmail);
            //$this-> completEmailAdmin('carlos@ibsweb.com.br','Carlos',$_POST['name'],$_POST['email'],$_POST['email']);
            
            echo '<span class="mok">Conta cadastrada com sucesso.<br>Um link de confirmação foi enviado para o seu email.</span>';
        endif;

        if($insert):
             //Upload da foto do estilista.
            //Criando a pasta para a foto do estilista
            $pastEmail = "'".$_POST['email']."'";
            $diretorioFotos = JPATH_SITE.'/images/guappa/estilistas/';
            if(!is_dir($diretorioFotos.$this-> getDados('users','id','email',$pastEmail))):
                mkdir($diretorioFotos.$this-> getDados('users','id','email',$pastEmail),0777, true);
            endif;
            $diretorio = $diretorioFotos.$this-> getDados('users','id','email',$pastEmail).'/';
            
            $this->uploadFotos($diretorio,'img');
        endif;

        return false;
     }

     protected function getPagseguro($inputs,$itemid)
     {
			
		foreach($inputs as $input)
		{
			if($input['value'] != ''){
				$$input['name'] = $input['value'];
			}
		}

		//Prefixo
		$prefix = explode(" ", $celular);

		$html = [];
		$html[] = '<form  method="post" action="https://pagseguro.uol.com.br/checkout/checkout.jhtml" name="checkout" class="formdinsc">';
  		$html[] = '<input type="hidden" name="email_cobranca" value="financeiro@depaulanegocios.com.br">';
  		$html[] = '<input type="hidden" name="tipo" value="CP">';
  		$html[] = '<input type="hidden" name="moeda" value="BRL">';
  		$html[] = '<input type="hidden" name="encoding" value="UTF-8">';

  		$html[] = '<input type="hidden" name="item_id_'.$itemid.'" value="'.$itemid.'">';
  		$html[] = '<input type="hidden" name="item_descr_'.$itemid.'" value="'.$categoria.' - '.$curse.'">';
  		$html[] = '<input type="hidden" name="item_quant_'.$itemid.'" value="1">';
  		$html[] = '<input type="hidden" class="pagprice" name="item_valor_'.$itemid.'" value="" >';
  		$html[] = '<input type="hidden" name="item_frete_'.$itemid.'" value="0">';
  		$html[] = '<input type="hidden" name="item_peso_'.$itemid.'" value="0">';


		$html[] = '<input type="hidden" name="cliente_nome" value="'.$name.'">';
	  	$html[] = '<input type="hidden" name="cliente_cep" value="'.$cep.'">';
	  	$html[] = '<input type="hidden" name="cliente_end" value="'.$endereco.'">';
	  	$html[] = '<input type="hidden" name="cliente_num" value="'.$itemid.'">';
	  	$html[] = '<input type="hidden" name="cliente_bairro" value="'.$bairro.'">';
	  	$html[] = '<input type="hidden" name="cliente_cidade" value="'.$cidade.'">';
	  	$html[] = '<input type="hidden" name="cliente_uf" value="'.$estado.'">';
	  	$html[] = '<input type="hidden" name="cliente_pais" value="BRA">';
	  	$html[] = '<input type="hidden" name="cliente_ddd" value="'.$prefix[0].'">';
	  	$html[] = '<input type="hidden" name="cliente_tel" value="'.$prefix[1].'">';
	  	$html[] = '<input type="hidden" name="cliente_email" value="'.$email.'">';


		$html[] = '<input type="image" style="display:none">';
		$html[] = '<input type="submit" class="dEnC" name="submit" style="display:none" alt="Pague com PagSeguro - é rápido, grátis e seguro!" value="Encerrar compra">';
		$html[] = '</form>';

		$html[] = '<form action="" class="filho" >';
		$html[] = '</form>';

		$html[] = '<div id="redirectpagseguro"></div>';
		$html[] = '<div id="dfinalizarpag" style="display:none">';
		$html[] = '<h2>Seu pedido foi recebido</h2>';
		$html[] = '<p>Obrigado por comprar conosco!</p>';
		$html[] = '<p><img src="../modules/mod_dinscricao/assets/image/dinsloading.gif" alt="Loading..." /></p>';
		$html[] = '<h3>Aguarde!.</h3>';
		$html[] = '<h5>Você será redirecionado para o site do PagSeguro.</h5>';
		$html[] = '</div>';

		return implode("",$html);

     }

}