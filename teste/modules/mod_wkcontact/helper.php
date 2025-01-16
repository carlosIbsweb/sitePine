<?php
/**
 * @subpackage  mod_wkcontact
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */
// no direct access
defined('_JEXEC') or die;
require_once "elements/recaptchalib.php";
class modWkcontactHelper
{
	static function getItems(&$params)
	{
		$doc = JFactory::getDocument();
		/**********
		 Styles
		***********/
		$styleSubmitAlign = $params->get('wkSubmitAlign') == 'left' ? 'float:left' : ($params->get('wkSubmitAlign') == 'right' ? 'float:right' : 'margin: 0 auto !important; display:table');
		$styleSubmitWidth = !empty($params->get('btnwidth')) ? 'width: 100%; max-width:'.str_replace("px","",$params->get('btnwidth')).'px' : null;
		$styles = '.wk-submit button {'.$styleSubmitAlign.';'.$styleSubmitWidth.'} .g-recaptcha {'.$styleSubmitAlign.'}';
		$doc->addStyleDeclaration($styles);
		return true;	
	}


	public static function getAjax()
	{
	
		$input = JFactory::getApplication()->input;
		//inputs
		$iFiles 	= $_FILES;
		$iPosts 	= $_POST;

		return self::setMail($iPosts,$iFiles);

	}

	/*********************************************
	 E-mail Functions
	***********************************************/
	public static function setMail($iPosts,$iFiles)
	{
	  	$app = JFactory::getApplication();
	  	//Carregando variáveis do form.
		foreach($iPosts as $k=> $input)
		{
			if($input != ''){
				$iName  = $k;
				$$iName = $input;
			}
			//Código recaptcha
			if($k == 'g-recaptcha-response'):
				$drecaptcha = $input;
			endif;
		}
		$db = JFactory::getDBO();
		$db->setQuery("SELECT params FROM #__modules WHERE module = 'mod_wkcontact' and published = '1' and id = ".$wkformid);
		$mod = $db->loadObject();
  		
  		$params = new JRegistry();
		$params->loadString($mod->params);
		/**************
		Vars Text Success
		**************/
		preg_match_all("/{(.+?)}/i", $params->get('mensucess'), $textSuccess);
		if(!empty($textSuccess[1]))
		{
		    foreach($textSuccess[1] as $m)
		    {
		    	$mvars .= $$m.',';
			    $resM = explode(",",$mvars);
			    $menSuccess = str_replace($textSuccess[0],$resM,$params->get('mensucess'));
		    }
		}
		else
		{
		    $menSuccess = $params->get('mensucess');
		}
  		//Buscando o id do módulo atual.
  		$itemid = $module->id;
  		//Assunto Admin.
  		$subjectAdmin = !empty($params->get('subject')) ? $params->get('subject') : $subject;
  		//Assunto Admin.
  		$subjectUser = !empty($params->get('subjectuser')) ? $params->get('subjectuser') : $subject;
		$doc = JFactory::getDocument();
		//Get user var.
		$mailUserVar = $params->get('mailuser');
		//recaptcha
		$recaptcha = $params->get('recaptcha') == 1 ? self::getReCaptcha($params->get('sitekey'),$params->get('secretkey'),$drecaptcha) : true;
		//Cadastro dos dados no banco.
		if ($params->get('activDb') == 1) 
		{
			//Cadastrando os dados no banco.
			if(self::getDb($params->get('validFields'),$params->get('db'),$iPosts) == false)
			{
				if ($recaptcha)
				{
					//Set e-mail Admin
					$emailAdmin = self::bodyMail($params->get('bodyadmin'),$subjectAdmin,$params->get('emailsender'),$params->get('namesender'),$params->get('mail'),$iPosts,$iFiles,$params->get('extensions'));
					if (self::setDb($params->get('db'),$params->get('dataDb'),$iPosts) && $emailAdmin) 
					{
						//Set e-mail User
						if($params->get('mailuserativ') == 1 && $emailAdmin && $$mailUserVar != '')
						{
		    				self::bodyMail($params->get('bodyuser'),$subjectUser,$params->get('emailsender'),$params->get('namesender'),$$mailUserVar,$iPosts,$iFiles,$params->get('extensions'));
						}
						
						//Sucess  send.
		    			return '<span class="isWkFormEmptySucess bounceIn animated">'. $menSuccess .'</span>';
					}
					else
					{
						//Error send.
						return '<span class="isWkFormEmptyError tada animated">'. $params->get('menfailure') .'</span>';
					}
				}
				else
				{
					return '<span class="isWkFormEmptyError tada animated">'.JText::_('MOD_WKCONTACT_FALHACAPTCHA').'</div>';
				}
			}
			else
			{
				//Validação de campos que vai para o banco.
				return '<span class="isWkFormEmptyError tada animated">'.self::getDb($params->get('validFields'),$params->get('db'),$iPosts).'</span>';
			}
		}
		else
		{
			if ($recaptcha)
			{
				//Set e-mail Admin
				$emailAdmin = self::bodyMail($params->get('bodyadmin'),$subjectAdmin,$params->get('emailsender'),$params->get('namesender'),$params->get('mail'),$iPosts,$iFiles,$params->get('extensions'));
				if($emailAdmin)
				{
					//Set e-mail User
					if($params->get('mailuserativ') == 1 && $emailAdmin && $$mailUserVar != '')
					{
		    			self::bodyMail($params->get('bodyuser'),$subjectUser,$params->get('emailsender'),$params->get('namesender'),$$mailUserVar,$iPosts,$iFiles,$params->get('extensions'));
					}
					/***********
					 *Redirecionamento
					***********/
					$fullUrl 		= $params->get('redirecturl');
					$timeRedirect 	= $params->get('timeredirect');
					$url1 = explode("http://",$fullUrl);
					$url2 = explode("https://",$fullUrl);
					$redirectUrl = count($url1) == 2 || count($url2) == 2 ? $fullUrl : '/'.$fullUrl;
					$script = '<script>';
					$script .= 'jQuery(function($){';
					$script .= 'setTimeout(function(){window.location.assign("'.$redirectUrl.'");},'.$timeRedirect.')';
					$script .= '});';
					$script .="</script>";
					if($params->get('redirect'))
					{
						echo $script;
					}
					
					
					//Sucess  send.
		    		return '<span class="isWkFormEmptySucess bounceIn animated">'. $menSuccess .'</span>';
				}
				else 
				{
				//Error send.
				return '<span class="isWkFormEmptyError tada animated">'. $params->get('menfailure') .'</span>';
				}
			}
			else
			{
				return '<span class="isWkFormEmptyError tada animated">'.JText::_('MOD_WKCONTACT_FALHACAPTCHA').'</div>';
			}
		}
	}
	
	// E-mail Body =============
	public static function bodyMail($bodyText,$assunto,$mailSender,$nameSender,$recipient,$iPosts,$iFiles,$extensions) {
		$mail = JFactory::getMailer();
		//Carregando variáveis do form.
		foreach($iPosts as $k=> $input)
		{
			if($input != ''){
				$iName  = $k;
				$$iName = $input;
			}
		}

		//Recuperando dados do corpo do e-mail.
		$dadosEmail = $bodyText;
		//Carregando texto do body.
		preg_match_all("/{(.+?)}/i", $dadosEmail, $text);
		//Preparando texo de um array.
		preg_match_all("/\[(.+?)\]/i", $dadosEmail, $array);
		/****************
		 #### Variaveis Globais ####
		*****************/
		$site 		= JUri::base();
		$date 		= date('Y-m-d H:m:s');
		$dateTime 	= date("Y-m-d H:i:s");
		$ip 		= $_SERVER["REMOTE_ADDR"];
		//Carregando imagens
		preg_match_all("/src=\"(.+?)\"/", $dadosEmail, $imgs);
		//Recipient.
		$pMail = explode(",",trim($recipient));
		$recipientMail = count($pMail) == 1 ? $recipient : $pMail;
		$conj = array_merge($text[0],$imgs[1],$array[0]);
		$texto = [];
		foreach($text[1] as $in=> $nms):
			$names = $nms;
			$texto[] .= $$names;
		endforeach;
		/****************
		 #### Preparando Imagens do body. ####
		*****************/
		foreach($imgs[1] as $in=> $nms):
			$imgsN = $nms;
			$texto[] .= Juri::base().$imgsN;
		endforeach;
		/****************
		 #### Preparando Campos em array. Use: [nomecampo] ####
		*****************/
		foreach($array[1] as $k=> $i)
		{
			foreach($iPosts as $key=> $v)
			{
				if($key == $i.'[]')
				{
					$arrV = $v.'; ';
					$$i .= $arrV;
				}
			}

			$texto[] = implode(", ",$$i);
		}
		/****************
		 # Assunto
		*****************/
		$subject = $assunto;
		$messageText = str_replace($conj, $texto, $dadosEmail);
		$message = 	'<!DOCTYPE html>';
		$message .= '<html lang="en">';
		$message .= '<head>';
		$message .= '<meta http-equiv="Content-Type" content="txt/plain; charset=utf-8">';
		$message .= '<title></title>';
		$message .= '</head>';
		$message .= '<body>';
		$message .= $messageText;
		$message .= '</body>';
		$message .= '</html>';
		
		$sender = array($mailSender, $nameSender);
		$mail->setSender($sender);
		$mail->addRecipient($recipientMail);
		$mail->setSubject($subject);
		$mail->isHTML(true);
		$mail->Encoding = 'base64';
		$mail->setBody($message);


		
		
		if(!empty($iFiles)){
			$isFileRe = strlen(count($iFiles, COUNT_RECURSIVE));
			//Extensóes permitidas para anexo.
			$pExtensions = explode(",",$extensions);

			//Carregando Files do Form.
			foreach($iFiles as $k=> $files)
			{  
			    if($isFileRe > 1){
			    	for($i=0;$i<count($files);$i++){
			    		$extension  = strtolower(pathinfo($files['name'][$i],PATHINFO_EXTENSION));
						if(in_array($extension,$pExtensions) && !empty($extensions)) {
			    			$mail->addAttachment($files['tmp_name'][$i],$files['name'][$i]);        
						}
						if(empty($extensions)){
							$mail->addAttachment($files['tmp_name'][$i],$files['name'][$i]);   
						}
			    	}	
			    }else{
			    	$extension  = strtolower(pathinfo($files['name'],PATHINFO_EXTENSION));
			    	if(in_array($extension,$pExtensions) && !empty($extensions) ){
			    		$mail->addAttachment($files['tmp_name'],$files['name']);
			    	}

			    	if(empty($extensions)){
			    		$mail->addAttachment($files['tmp_name'],$files['name']);
			    	}
			    }
			        
			}
		}
		return $mail->Send();
	}

	/****************
	 # Gerar captcha Google reCaptcha.
	*****************/
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

	    return true;
	}
	
	/**************************
	# DB Functions
	***************************/
	public static function setDb($banco,$myData,$iPosts)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		//Carregando variáveis do form.
		foreach($iPosts as $k=> $input)
		{
			if($input != ''){
				$iName  = $k;
				if(is_array($input)){
					$inputsData = implode(',',$input);
				}else{
					$inputsData = $input;
				}
				$$iName = $inputsData;
			}
		}
		//Data names db.
		$cleanData = preg_replace("/\[(.+?)\]/i", "",$myData);
		$dataDb = array_filter(explode(",",$cleanData));
		/***********//////////// Vars GLOBALS \\\\\\\\\\\\\\*************/
		$site 		= JUri::base();
		$date 		= date('Y-m-d H:m:s');
		$ip 		= $_SERVER["REMOTE_ADDR"];
		$fieldValues = array();
		foreach($dataDb as $fields)
		{	
			$getFields = $$fields;
			$fieldValues[] = $getFields;
		}
		//Preparando texo de um array.
		preg_match_all("/\[(.+?)\]/i", $myData, $array);
		/****************
		 #### Preparando Campos em array. Use: [nomecampo] ####
		*****************/
		foreach($array[1] as $k=> $i)
		{
			foreach($iPosts as $key => $v)
			{
				if($key == $i.'[]')
				{
					$arrV = $v.'; ';
					$$i  .= $arrV;
				}
			}
			
			array_unshift($fieldValues, substr($$i,0,-2));
		}
		foreach($dataDb as $fieldsarr)
		{
			array_unshift($array[1],$fieldsarr);
		}

		 
		// Create a new query object.
		$query = $db->getQuery(true);
		
		// Insert columns.
		$columns = $array[1];
		 
		// Insert values.
		$values = array_filter($fieldValues);
		 
		// Prepare the insert query.
		$query
		    ->insert($db->quoteName('#__'.$banco))
		    ->columns($db->quoteName($columns))
		    ->values(implode(",",$db->quote(array_reverse($values))));
		 
		// Set the query using our newly populated query object and execute it.
		$db->setQuery($query);
		
		//Executar.
		$db->execute();
		return true;
	}
	
	/**********
	 Buscando dados da tabela.
	 Validando campos para serem cadastrados somente 1 vez.
	**********/
	public static function getDb($myData,$banco,$iPosts)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		//Carregando variáveis do form.
		$nameForm = [];
		foreach($iPosts as $k=> $input)
		{
			if($input != ''){
				$iName  = $k;
				$$iName = $input;
				$nameForm[] = $input;
			}
		}
		 
		// Create a new query object.
		$query = $db->getQuery(true);
		//Array data ---------------------------------------------------------
		$dataArr = explode(",",$myData);
		preg_match_all("/{.*?}/i", $myData, $dataMatch);
		
		foreach($dataArr as $arrs)
		{
			foreach($dataMatch as $matchs)
			{
				$mArrs .= str_replace($matchs,',',$arrs);
				$labelsArr = array_filter(explode(",",$mArrs));
			}
		}
		$namesArr = str_replace(["{","}"],["",""],array_filter($dataMatch[0]));
		$labelItem = array_combine($labelsArr, $namesArr);
		 
		if(count(array_filter($namesArr)) != 0) 
		{
			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select($db->quoteName($namesArr));
			$query->from($db->quoteName('#__'.$banco));
			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			 
			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadObjectList();
			$count = count($results);
			$dValues 	= [];
			$dKeys 		= [];
			foreach($results as $item)
			{
				foreach($item as $k=> $items)
				{
					if($items===$$k)
					{
						$dValues[$k] 	= $items;
						$dKeys[$items] 	= $k;
					}
				}
			}
			foreach($dValues as $k=> $items)
			{
				if(count($dValues) == 1)
				{
					$dataItemsOne 	=  array_search($k,$labelItem);
					$dataItems 		= JText::sprintf('MOD_WKCONTACT_FALHACAMPODB',$dataItemsOne);
				}
				else
				{
					if($k != end($dKeys))
					{
						$dataItemsTwo .=  array_search($k,$labelItem).', ';
						$rest = substr(trim($dataItemsTwo),0,-1);
					}
					else
					{
						$dataItemsOne 	.=  ' e '.array_search($k,$labelItem);
						$rest	.= $dataItemsOne;
					}
				
					$dataItems = JText::sprintf('MOD_WKCONTACT_FALHACAMPODBMORE',$rest);
				}
			}
		
		}else{
			$dataItems = false;
		}
		return $dataItems;
	}
}