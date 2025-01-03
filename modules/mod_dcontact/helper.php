<?php

/**
 * @subpackage  mod_dcontact
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

require_once "elements/recaptchalib.php";

class modDcontactHelper
{

	static function getItems(&$params)
	{
		
	}
	public function getAjax()
	{
	
		$input = JFactory::getApplication()->input;
		$mail = JFactory::getMailer();

	  	$app = JFactory::getApplication();
  		$module = JModuleHelper::getModule('mod_dcontact');
  		$params = new JRegistry($module->params);

		$doc = JFactory::getDocument();

		$pMail = explode(",",trim($params->get('mail')));

		$recipient = count($pMail) == 1 ? $params->get('mail') : $pMail;

		//inputs
		$inputs = $input->get('data', array(), 'ARRAY');

		foreach($inputs as $input)
		{
			if($input['value'] != ''){
				switch($input['name']){
					case 'name':
						$name = $input['value'];
						break;
					case 'email':
						$email = $input['value'];
						break;
					case 'telefone':
						$telefone = $input['value'];
						break;
					case 'dbanco':
						$dbanco = $input['value'];
						break;
					case 'g-recaptcha-response':
						$drecaptcha = $input['value'];
						break;		
				}
			}

		}

		//Subject
		$subject = !empty($email) ? $doc->getTitle().' (E-mail)' : $doc->getTitle().' (Telefone)';

		$message = "<strong>Nome:</strong> ".$name.'<br />';
		!empty($email)    ? $message .= "<strong>E-mail: </strong> ".$email.'<br />' : null;
		!empty($telefone) ? $message .= "<strong>Telefone: </strong>".$telefone.'<br />' : null;
		if($params->get('dbanco') == 1):
			$message .= '<strong>Cliente do Banco Bradesco: </strong>'.$dbanco. '<br />';
		endif;

		$vrequired = $email || $telefone ? true : false;
		
		$sender = array($email, $name);
		$mail->setSender($sender);
		$mail->addRecipient($recipient);
		$mail->setSubject($subject);
		$mail->isHTML(true);
		$mail->Encoding = 'base64';
		$mail->setBody($message);

		$recaptcha = self::getReCaptcha('6LeT5hkUAAAAAIOTYZmY6z3ZmTwsLTDW2PmfGB2H','6LeT5hkUAAAAAEi-FedLLz2yBOFL9KjDEq1dGMqF',$drecaptcha);

		
		if($recaptcha){
			if ($mail->Send() && $vrequired) {
				return '<span class="sppb-text-success">'. JText::_('Mensagem enviada com sucesso.') .'</span>';
			} else {
				return '<span class="sppb-text-danger">'. JText::_('Falha ao enviar e-mail.') .'</span>';
			}
		}else{
			return '<span class="sppb-text-danger">Código Captcha inválido</div>';
		}

	}

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

}