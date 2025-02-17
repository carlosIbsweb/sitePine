<?php

/**
 * @subpackage  mod_wkcontact
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

spl_autoload_register(function ($class) {
    @include dirname(__FILE__) . '/library/classes/'.$class.'.php';
});


class modS7dformHelper
{

	public static function getItems(&$params)
	{

	}

	public function getAjax()
	{
		$input = JFactory::getApplication()->input;
		//inputs
		$iFiles 	= $_FILES;
		$iPosts 	= $_POST;

		$report = array();

		/********************
		 *SET Vars Data
		********************/
		parse_str(http_build_query($iPosts));

		$moduleId = explode('-',$moduleId);
		$moduleId = $moduleId[1];
		$awCaptcha = 'awCaptcha-'.$moduleId;
		$awCaptcha = $$awCaptcha;

		/********************
		 *GET Params
		********************/
		$db = JFactory::getDBO();
		$db->setQuery("SELECT params FROM #__modules WHERE module = 'mod_s7dform' and published = '1' and id =".$moduleId);
		
		try {
			$module = $db->loadObject();
		} catch (Exception $e) {
			JError::raiseWarning(500, $e->getMessage());
		}
		
		$params = new JRegistry();
		$params->loadString($module->params);

		//self::setTableRelated($params);
		//return false;

		/********************
		 *SET Vars Globais
		********************/
		$menSuccess = $params->get('mensucess');
		preg_match_all("/{success\[(.+)\]}/U", $menSuccess,$menS);

		$menSs = '<a href="#" class="aw-new">'.$menS[1][0].'</a>';
		$menSuccess = str_replace($menS[0][0],$menSs,$menSuccess);

		//Token de Acesso.
		$_SESSION['awToken'] = md5(uniqid(rand(), true));
		$_SESSION['tokenEmail'] = md5(uniqid(rand(), true));


		/*echo 'aqui'.self::upload($iFiles,$moduleId);
		return false; 999999999
		exit();*/
	
		//Valid Captcha
		if(!self::awCaptchaAjax($awCaptcha,false,null,$awCaptchaRest,$params) && $params->get('awcaptcha'))
		{
			return false;
			exit();
		}

		//Valid Campos
		if(!self::awValid($iPosts,$params))
		{
			return false;
			exit();
		}

		//Campos Unicos
		if(($params->get('activDb') && $params->get('db') && !empty($params->get('validFields'))))
		{
			if(!awLogin::validFields($params,$iPosts))
			{
				return false;
				exit();
			}
		}

		//Set Limite.
		if(($params->get('activDb') && $params->get('db') && $params->get('setLimit')))
		{
			if(!awLogin::setLimit($params))
			{
				return false;
				exit();
			}
		}

		//Verificação de Datas.
		if(($params->get('activDb') && $params->get('db') && !empty($params->get('sDate'))))
		{
			if(!self::dateVerific($params,$iPosts))
			{
				return false;
				exit();
			}
		}

		//Upload de arquivos
		if(!self::uploadFail($iFiles,$params->get('exMedia'))){
			return false;
			exit();
		}

		//Set DB
		$setDb = '';
		if(($params->get('activDb') && $params->get('db')))
		{
			//Set DB
			$setDb = self::setDb($iPosts,$params,$_FILES,$moduleId);	
			if($setDb)
			{
				
			}else{
				return false;
				exit();
			}
		}
		
		//Set Email User
		if($params->get('mailuserativ'))
		{
			//Capturar email de usuário
			$mailUser = $params->get('mailuser');
			if(self::setEmail($iPosts,$_FILES,$params,$params->get('bodyuser'),$params->get('subjectuser'),$$mailUser,$setDb->fileUpload,$setDb->upload) === false)
			{
				return false;
				exit();
			}
		}

		//Set Email Admin
		if($params->get('activeEmail'))
		{
			if(self::setEmail($iPosts,$_FILES,$params,$params->get('bodyadmin'),$params->get('subject'),$params->get('mail'),$setDb->fileUpload,$setDb->upload))
			{
				$report['success'] = true;
			}
		}
		else
		{
			$report['success'] = true;
		}

		if($report['success'])
		{	
			if($params->get('payment')){
				$menSuccess .= self::modPagSeguro($params,$moduleId,$iPosts);
			}
			$report['mSuccess'] = self::awMessages($menSuccess,'success');

			/***********
			 *Redirecionamento
			***********/
			$fullUrl 		= $params->get('redirectUrl');
			$timeRedirect 	= $params->get('timeredirect');

			$url1 = explode("http://",$fullUrl);
			$url2 = explode("https://",$fullUrl);

			$redirectUrl = count($url1) == 2 || count($url2) == 2 ? $fullUrl : '/'.$fullUrl;


			$script = 'setTimeout(function(){window.location.assign("'.$redirectUrl.'");},'.$timeRedirect.')';
			$report['redirect'] = false;

			if($params->get('redirect'))
			{
				$report['menredirect'] = $params->get('menredirect');
				if($params->get('redirectType') == 'url')
				{
					$report['redirect'] = true;
					$report['redirectUrl'] = $redirectUrl;
					$report['redirectTime'] = $timeRedirect;
					echo json_encode($report);
					exit();
				}

				if($params->get('redirectType') == 'download')
				{

					$file = $params->get('redirectDownload');
					$file = array_filter(explode('/',$file));
					$file = implode('/',$file);

					$arquivo = basename($file);
					$caminho_download = JPATH_ROOT.'/'.$file;

					$report['redirectDownloadError'] = false;
					// Verificação da existência do arquivo
					if (!file_exists($caminho_download)){

						$report['redirectDownloadError'] = true;
						echo self::awMessages('Arquivo ou diretório inexistente','danger');
						return false;
					}

					$_SESSION['redirectDownload'] = md5(uniqid(rand(), true));
					$report['redirect'] = true;
					$report['redirectDownload'] = true;
					$report['redirectUrl'] = $_SERVER['HTTP_REFERER'].'?download&redirect='.$_SESSION['redirectDownload'];
					$report['redirectTime'] = $timeRedirect;

					//testar calma aquiecho $_SERVER['HTTP_REFERER'].'?download&redirect='.$_SESSION['redirectDownload'];
					//return;
					echo json_encode($report);
					exit();
				}
				
			}

			echo json_encode($report);
		}		
	}

	public function awCaptchaAjax($g = null,$jResult = true,$idModule = '',$gValue = null)
	{
		$moduleId = explode('-',$_POST['moduleId']);
		$moduleId = empty($moduleId[1]) ? $idModule : $moduleId[1];

		/********************
		 *GET Params
		********************/
		$db = JFactory::getDBO();
		$db->setQuery("SELECT params FROM #__modules WHERE module = 'mod_s7dform' and published = '1' and id =".$moduleId);
		
		try {
			$module = $db->loadObject();
		} catch (Exception $e) {
			JError::raiseWarning(500, $e->getMessage());
		}
		
		$params = new JRegistry();
		$params->loadString($module->params);

		if($params->get('awcaptcha'))
		{
			return awCaptcha::getAwCaptcha($g,$jResult,$idModule,$gValue,$moduleId,$params);
		}
	}

	public static function awMessages($msg,$alert)
	{
		$alert 		= !empty($alert) ? $alert : 'success'; 
		$message 	= [];
		$message[] 	= '<div class="alert alert-'.$alert.'" style="text-align:center;">';
  		$message[] 	= $msg;
		$message[] 	= '</div>';

		return implode('',$message);
	}

	public static function setDb($iPosts,&$params,$iFiles,$moduleId)
	{
		$dbData = explode(',',$params->get('dataDb'));
		$results = new stdClass();
		$results->upload = false;
		$results->fileUpload = false;

		$dbColumn = array();
		$dbValues = array();

		//Vars Inputs
		parse_str(http_build_query($iPosts));

		if(!$params->get('activDb'))
		{
			return false;
			exit();
		}

		//Vars globais.
		$awToken = $_SESSION['awToken'];
		$tokenEmail = $_SESSION['tokenEmail'];
		date_default_timezone_set('America/Sao_Paulo');
		$date = date('Y-m-d H:m:s');

		/*
		 Enviar arquivos para o servidor
		 pasta s7dforms/moduloId/registroId/nomeDaMatriz/arquivos

		 chamar var : jsonUpload -- Variavel para ser chamada no cadastro do banco de dados.
		*/

		if(count($iFiles) > 0 && $params->get('upload'))
		{
			$rid = uniqid();
			$files = array();
			$fls = json_decode(self::upload($iFiles,$moduleId,$rid),true);
			array_push($files,['files' => $fls,'fid' => $moduleId,'rid'=>$rid]);

			$jsonUpload = json_encode($files);

			$results->upload = $jsonUpload;
			$results->fileUpload = true;

		}else{
			$jsonUpload = '{}';
		}
		

		// Initialiase variables.
		$db = JFactory::getDbo();

		foreach($dbData as $d)
		{
			$dN = $d;
			$dV = $$dN;

			//tratando campos array
			//$dV = is_array($dV) ? json_encode($dV,JSON_UNESCAPED_UNICODE) : $dV;

			if(is_array($dV))
			{
				if(count(array_filter($dV)) > 0)
				{
					$dV = json_encode($dV,JSON_UNESCAPED_UNICODE);
				}else{
					$dV = '{}';
				}
			}else{
				$dV = $dV;
			}
			

			//getVars
			$gV = explode(':',$d);
			if(count($gV) > 1)
			{
				list($nN,$nV) = $gV;
				$dN = $nN;
				$dV = isset($$nV) ? $$nV : $nV;
			}

			//Joomla
			if($params->get('awJoomla'))
			{
				$dV = $d == $params->get('awJCampo') ? awValid::validJoomla($params->get('awJoomla'),$dV) : $dV;
			}

			array_push($dbColumn,$db->quoteName($dN));
			array_push($dbValues,$db->quote($dV));
		}

		
		$query = $db->getQuery(true);
		
		// Create the base insert statement.
		$query->insert($db->quoteName($params->get('db')))
			->columns($dbColumn)
			->values(implode(",",$dbValues));
		
		// Set the query and execute the insert.
		$db->setQuery($query);
		
		try
		{
			$db->execute();
			if($params->get('dbRelated'))
			{	
				if(!self::setTableRelated($params,$iPosts,$db->insertid()))
				{
					return false;
				}
			}
		}
		catch (RuntimeException $e)
		{
			echo self::awMessages(JError::raiseWarning(500, $e->getMessage()),'danger');
			return false;
		}

		return $results;
	}

	public static function setEmail($iPosts,$iFiles,&$params,$bodyText,$subject,$recipient,$fileUpload = false,$files = false)
	{
		$mail = JFactory::getMailer();

		//Recuperando dados do corpo do e-mail.
		$emailData = $bodyText;

		//Recipiente
		$recipient = explode(',',$recipient);
		$recipient = count($recipient) == 1 ? $recipient[0] : $recipient;


		//BCC
		$bcc = $params->get('mailbcc');
		$bcc = explode(',',$bcc);
		$bcc = count($bcc) == 1 ? $bcc[0] : $bcc; 

		//Vars Inputs
		parse_str(http_build_query($iPosts));

		//Vars Files
		parse_str(http_build_query($iFiles));

		/********************
		 *Var Globais
		********************/
		$globModId = explode('-',$moduleId);
		$globModId = $globModId[1];

		$awToken 	= $_SESSION['awToken'];
		$awTokenAdm = $awCurrent.'?awEdit&awId='.$globModId.'&awToken='.$awToken;
		$awGPdf 	= $awCurrent.'?pdf&awId='.$globModId.'&awToken='.$awToken;
		$confirmEmail = $awCurrent.'?confirmarEmail='.$_SESSION['tokenEmail'];
		$confirmEmail = '<a href="'.$confirmEmail.'" target="_blank">'.$confirmEmail.'</a>';

		$date = date('Y-m-d H:m:s');

		if($params->get('teste')){
			echo $confirmEmail;
			return false;
		}
		

		
		$awToken 	= '<a style="width: auto!important;
		color: #fff;
		padding: 10px 14px;
		display: inline-block;
		line-height: 1;
		font-size: 13px;
		font-weight: 700;
		text-decoration: none;
		background: #2196f3;
		border: 0 solid silver;
		cursor: pointer;
		border-radius: 4px;" href="'.$awTokenAdm.'" target="_blank">Editar os Dados</a>';

	    $awPdf 	= '<a style="width: auto!important;
	    color: #fff;
	    padding: 10px 14px;
	    display: inline-block;
	    line-height: 1;
	    font-size: 13px;
	    font-weight: 700;
	    text-decoration: none;
	    background: #4ba55f;
	    border: 0 solid silver;
	    cursor: pointer;
	    border-radius: 4px;" href="'.$awGPdf.'" target="_blank">Gerar PDF</a>';

		//Carregando texto do body.
		preg_match_all("/{(.+)}/U", $emailData, $text);

		/********************
		 * Capturando as imagens e alterando o caminho para o caminho real.
		********************/
		preg_match_all("/src=\"(.+?)\"/", $emailData, $imgsData);
		$setImgs = array();

		foreach($imgsData[1] as $img)
		{
			array_push($setImgs,JUri::base().$img);
		}

		//Vars Body email
		$varBody = array();
		$varText = array();
		foreach($text[1] as $k=> $var)
		{

			$bodyVars = $$var;
			if(is_array($bodyVars))
			{
				if(count(array_filter($bodyVars)) > 0)
				{
					if (count($bodyVars) == count($bodyVars, COUNT_RECURSIVE)) 
					{
						$bodyVars = implode(', ',$bodyVars);
					}
					else
					{
						$newBody = [];
				  		foreach($bodyVars as $k=> $b){
				      		$keys = array_keys($b);    
				  		}

				  		$newBody[] = '<table cellspacing="4" cellpadding="6" rules="rows" style="border-collapse:collapse;color:#1f2240; min-width:600px;background-color:#ffffff">';
				  		$newBody[] = '<thead><tr><th scope="col" style="background-color:#1f2240; color:#fff;font-weight:bold;">'.implode('</th><th scope="col" style="background-color:#1f2240; color:#fff;font-weight:bold;">',$keys).'</th></tr></thead>';
				  		$newBody[] = '<tbody>';
						  foreach($bodyVars as $k=> $b)
						  {
						      $newBody[] = '<tr>';
						      foreach($b as $bb)
						      {
						        $newBody[] = '<td>'.$bb.'</td>';
						      }
						      $newBody[] = '</tr>';
						  }
						  $newBody[] = '</tbody>';
						  $newBody[] = '</table>';

						  $bodyVars = implode('',$newBody);
					}
				}else{
					$bodyVars = '-';
				}
			}
			else
			{
				$bodyVars = $bodyVars;
			}
			array_push($varBody,$bodyVars);
		}


		$bodyEmail = str_replace($text[0],$varBody,$emailData);
		
		//Alterando as imagens.
		if(!empty(count(array_filter($imgsData))))
		{
			$bodyEmail = str_replace($imgsData[1],$setImgs,$bodyEmail);
		}
		
		$message = 	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$message .= '<html xmlns="http://www.w3.org/1999/xhtml">';
		$message .= '<head>';
		$message .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
		$message .= '<title>'.$subject.'</title>';
		$message .= '</head>';
		$message .= '<body>';
		$message .= $bodyEmail;
		$message .= '</body>';
		$message .= '</html>';
		
		$sender = array($params->get('emailsender'), $params->get('namesender'));
		$mail->setSender($sender);
		$mail->AddBCC($bcc);
		$mail->addRecipient($recipient);
		$mail->setSubject($subject);
		$mail->isHTML(true);
		$mail->Encoding = 'base64';
		$mail->setBody($message);
		

		if($fileUpload)
		{	
			foreach(json_decode($files) as $fileU)
			{
				foreach($fileU->files as $fil)
				{	
					foreach($fil as $pa => $fill)
					{
						foreach($fill as $k=> $filp)
						{
							$path = 's7dforms/'.$fileU->fid.'/'.$fileU->rid.'/'.$pa.'/';
							$path = JPATH_ROOT.'/'.$path.$filp;
							$mail->addAttachment($path,$filp);
						}
					}
				}
			}
		}else {
			//files
			foreach($iFiles as $fk=> $file){
				$mail->addAttachment($file['tmp_name'],$file['name']);
			}
		}
		
		if($mail->Send())
		{	
			return true;
		}
		else
		{
			echo self::awMessages('Falha ao enviar e-mail','danger');
			return false;
		}
	}

	public static function awValid($inputs,&$params)
	{
		$vNames  = [];
		$vValues = [];
		$status = true;
		foreach(json_decode($params->get('s7dform'))->fields as $n => $v)
		{
			$val = $v->attrs->{'valid-type'};

			if(isset($val))
			{
				foreach($val as $vlds)
				{
					if($vlds->selected)
					{
						array_push($vNames,$v->attrs->name);
						array_push($vValues,$vlds->value);
					}
				}
			}
		}

		$vC = array_combine($vNames,$vValues);

		foreach($inputs as $n=> $v)
		{
			if(in_array($n,$vNames))
			{
				if(!awValid::awV($vC[$n],$v))
				{
					$mError .= $n.' é inválido, por favor digite um '.$vC[$n]. ' válido<br />';
					
					$status = false;
				}
			}
		}

		if($status)
		{
			return true;
		}
		else
		{
			echo self::awMessages($mError,'danger');
			return false;
		}

	}


	public function awLoginAjax()
	{

		$iPosts 	= $_POST;

		/********************
		 *SET Vars Data
		********************/
		parse_str(http_build_query($iPosts));

		$moduleId = explode('-',$moduleId);
		$moduleId = $moduleId[1];


		/********************
		 *GET Params
		********************/
		$db = JFactory::getDBO();
		$db->setQuery("SELECT params FROM #__modules WHERE module = 'mod_s7dform' and published = '1' and id =".$moduleId);
		
		try {
			$module = $db->loadObject();
		} catch (Exception $e) {
			JError::raiseWarning(500, $e->getMessage());
		}
		
		$params = new JRegistry();
		$params->loadString($module->params);

		//Valid Campos
		if(!self::awValid($iPosts,$params))
		{
			return false;
			exit();
		}
		
		if(!empty($params->get('validFields')))
		{
			if(!awLogin::validFields($params,$iPosts,$awEditToken))
			{
				return false;
				exit();
			}
		}
		

		if(awLogin::setUp($params->get('db'),$iPosts,$awEditToken,$params))
		{	
			echo self::awMessages('Dados atualizados com sucesso','success');
		}	
	}

	/**********
	 *Tabela relacionada
	**********/
	public static function setTableRelated(&$params,$iPosts,$insertId)
	{
		// Initialiase variables.
		$db    = JFactory::getDbo();

		//Vars Inputs
		parse_str(http_build_query($iPosts));

		//Vars globais.
		$awToken = $_SESSION['awToken'];
		$date = date('Y-m-d H:m:s');

		$tableRelated = $params->get('tableRelated');
		$campoRelated = $params->get('campoRelated');
		$camposRelated = $params->get('camposRelated');

		$camposRelated = explode(',',$camposRelated);
		//Transformar campos em var relacionados.

		//Dados de inserção
		$cols = array();
		$vls = array();

		foreach($camposRelated as $item)
		{
			$cmps = explode(':',$item);
			list($cData,$cVal) = $cmps;

			$cVal = isset($$cVal) ? $$cVal : $cVal;

			//is Array
			$cVal = is_array($cVal) ? implode(", ",$cVal) : $cVal;

			array_push($cols,$db->quoteName($cData));
			array_push($vls,$db->quote($cVal));
		}

		array_push($cols,$campoRelated);
		array_push($vls, $insertId);

		$query = $db->getQuery(true);
		
		// Create the base insert statement.
		$query->insert($db->quoteName($tableRelated))
			->columns(array(implode(',',$cols)))
			->values(implode(',',$vls));
		
		// Set the query and execute the insert.
		$db->setQuery($query);
		
		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			echo self::awMessages(JError::raiseWarning(500, $e->getMessage()),'danger');
			return false;
		}

		return true;
	}


	/******************
	 *Usar {var[texto qualquer]}
	******************/
	public static function varText($varText,$textVar,$tag = array())
	{
		$textVar = '{'.$textVar.'}';
		$varsArr = array();
		preg_match_all("/{ *".$varText." *\[(.+)\] *}/U", $textVar,$menS);

		$menSs = $tag[0].$menS[1][0].$tag[1];
		$textVar = str_replace($menS[0][0],$menSs,$textVar);

		array_push($varsArr,$menS[0][0],$menSs);

		return array_filter($varsArr);
	}

	public static function awDAjax()
	{
		$iPosts 	= $_POST;

		/********************
		 *SET Vars Data
		********************/
		parse_str(http_build_query($iPosts));

		$moduleId = explode('-',$moduleId);
		$moduleId = $moduleId[1];


		/********************
		 *GET Params
		********************/
		$db = JFactory::getDBO();
		$db->setQuery("SELECT params FROM #__modules WHERE module = 'mod_s7dform' and published = '1' and id =".$moduleId);
		
		try {
			$module = $db->loadObject();
		} catch (Exception $e) {
			JError::raiseWarning(500, $e->getMessage());
		}
		
		$params = new JRegistry();
		$params->loadString($module->params);

		
		$menSuccess = self::getMenRex($params->get('awUpDsuccess'),$params->get('db'),$awUEToken);

		if(awLogin::awUpDado($params->get('db'),$params->get('awUpDName'),$params->get('awUpDValue'),$awUEToken,$params->get('awUpDex'),$params))
		{
			echo self::awMessages($menSuccess,'success');
		}
	}

	public static function getMenRex($men,$db,$token)
    {
        //vars {name}
        preg_match_all("/{(.+)}/U", $men, $menSuccs);

        $menSuccsR = array();
        foreach($menSuccs[1] as $n)
        {
            $menS = $n;
            array_push($menSuccsR,awLogin::getDado($n,$db,$token));
        }
  
        $menSuccess = str_replace($menSuccs[0],$menSuccsR,$men);

        return $menSuccess;
    }

    //Verificar dados de data.
    public static function dateVerific(&$params,$iPosts)
    { 
    	//Vars Inputs
		extract($iPosts);

    	$sDate = $params->get('sDate');

    	$gr = explode(',',$sDate);
		list($texto,$name,$ano) = $gr;

		$sName = trim($name);

    	//Data que vem do form.
		$dV = str_replace('/','-',$$sName);
		$dV = new DateTime($dV);
		$dV = $dV->format('Y-m-d');

		//Data Atual;
		$dA = date('Y-m-d');

		//Cauculando anos.
		$anos = intval($ano / 365);

		$dat1 = new DateTime($dV);
		$dat2 = new DateTime($dA);
		$interval = $dat1->diff($dat2);
		$dF = $interval->format('%a');

		if($dF > $ano){
			echo self::awMessages($texto.' '.$anos.' anos','danger');
			return false;
		}

		return true;
	}

	//Upload
	public static function upload($files,$modId,$rid)
	{

		/*
			Usando o Json
		$a = self::upload($iFiles);

		foreach(json_decode($a,true) as $k=> $fs){
			foreach($fs as $k=> $foto)
			{
				echo $k;
				foreach($foto as $fi)
				{
					echo $fi;
				}
			}
		}*/
		$fJson = array();
		$i = 0;


		//print_r($files);
		foreach($files as $k=> $file)
		{	
			$path = JPATH_ROOT.'/s7dforms/'.$modId.'/'.$rid.'/'.$k;
			$folder = $path.'/';
			if(!is_dir($path)){
      			mkdir($path,0777, true);
      			chmod($path, 0777);
    		}

			$fNames = array();
			for($if = 0;$if <= count($file['name']); $if++)
			{	
				$fn = $file['name'][$if];
				$fid = uniqid();
				$ex = strtolower(pathinfo($fn,PATHINFO_EXTENSION));
				$fname = strtolower(pathinfo($fn,PATHINFO_FILENAME));
				$fileName = self::awStr($fname).'.'.$ex;

				if(JFile::upload($file['tmp_name'][$if],$folder.$fileName)){
					array_push($fNames, $fileName);
				}
			}


			
			array_push($fJson, [$k=>$fNames]);
		
			$i++;
		}

		return json_encode($fJson);
	}

	public static function uploadFail($files,$exPer)
	{
		//Extensions
		$exPer = $exPer != '' ? explode(',',$exPer) : ['jpg','png'];
		foreach($files as $k=> $file){
			for($if = 0;$if < count($file['name']); $if++)
			{
				$fn = $file['name'][$if];
				$ex = strtolower(pathinfo($fn,PATHINFO_EXTENSION));
				$fileName = $fid.'.'.$ex;

				if(!in_array($ex, $exPer))
				{
					echo self::awMessages('Upload de arquivos não permitidos. Só são permitidos arquivos <strong>'.implode(',',$exPer).'</strong>','danger');
					return false;
				}	
			}
		}
		
		return true;
	}

	public function modPagSeguro(&$params,$modId,$iPosts)
	{

		extract($iPosts);

		//Vars Sender.
		$payName 		= $params->get('payName');
		$payAreaCode 	= $params->get('payAreaCode');
		$paySenderPhone = $params->get('paySenderPhone');
		$paySenderEmail = $params->get('paySenderEmail');


		//Products
		$products =  json_decode($params->get('payProducts'),true);
		$quantity = isset($payQuantity) ? $payQuantity : 1;

		if(isset($payVariation)){
			$a = array_filter($products,function($i) use($payVariation){
				return $i['id'] == trim($payVariation);
			});

			$prod = array_values($a);
		}else{
			$prod = array_values($products);
		}

		//Mod PagSeguro
		$checkout = new awModPagSeguro(
			$params->get('payToken'),
			$params->get('payEmail'),
			$params->get('payCurrency'),
			array(
				$prod[0]['id'],
				(int) $quantity,
				$prod[0]['name'],
				trim($prod[0]['price'])
			),
			$prod[0]['ref'],
			array(
				$$payName,
				$$paySenderPhone,
				$$paySenderEmail,
			),
			$params->get('payMenSuccess'),
			$params->get('payType')
		);
		
		return $checkout->checkout();
	}

	public static function confirmEmail($sDb,$tokenEmail){


		if(!self::getConfirm($sDb,$tokenEmail)){
			return false;
		}

		// Initialiase variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base update statement.
		$query->update($db->quoteName($sDb))
		    ->set($db->quoteName('confirmEmail') . ' = ' . $db->quote(1))
		    ->set($db->quoteName('tokenEmail') . ' = ' . $db->quote(''))
		    ->where($db->quoteName('tokenEmail') . ' = ' . $db->quote($tokenEmail));
		
		// Set the query and execute the update.
		$db->setQuery($query);
		
		try
		{
		    $db->execute();
		    return true;
		}
		catch (RuntimeException $e)
		{
		    echo JError::raiseWarning(500, $e->getMessage());
		    return false;
		}

		return true;
	}

	public static function getConfirm($sDb,$token)
	{

		if(empty($token))
		{
			return false;
		}

		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('tokenEmail')));
		$query->from($db->quoteName($sDb));
		$query->where($db->quoteName('tokenEmail') . ' = ' . $db->quote($token));

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return count($results);
	}

	/*
		Remover caracteres especiais e acentos.
	*/
	public static function awStr($string)
	{
		$string = strtolower($string);

		$str  = 'á,à,â,ã,å,ä,é,è,ê,ë,í,î,ì,ï,ó,ô,ò,ø,õ,ö,ú,û,ù,ü,ç,ñ,ý';
		$repl = 'a,a,a,a,a,a,e,e,e,e,i,i,i,i,o,o,o,o,o,o,u,u,u,u,c,n,y';
		$repl = explode(',',$repl);
		$str  = explode(',',$str);
		$str  = str_replace($str,$repl,$string);
		$str  = preg_replace('/(\W+)/','-',$str);

		return $str;
	}

	public static function getList(){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('nome_completo', 'Nome'));
		$query->select($db->quoteName('telefone_celular', 'Telefone'));
		$query->select($db->quoteName('data_nascimento', 'Data de Nascimento'));
		$query->select($db->quoteName('tipo_associacao', 'Tipo de Associação'));
		$query->select($db->quoteName('tipo_camiseta', 'Tipo de Camiseta'));
		$query->select($db->quoteName('tamanho_camiseta', 'Tamanho da Camiseta'));
		$query->select($db->quoteName('data', 'Data'));
		$query->from($db->quoteName('#__torneio2022'));
		$query->order('data DESC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}

	public static function getListBeach($form,$params){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		/*Campos a serem jogados na planilha*/
		$campos = explode(',',$params->get('dadosLista'));

		$dados = array_map(function($a){
			return explode(':',$a)[1];
		},$campos);

		$labels = array_map(function($a){
			return explode(':',$a)[0];
		},$campos);
			
			
		for($i = 0; $i < count($dados); $i++){
			$query->select($db->quoteName($dados[$i], $labels[$i]));
		}

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		/*$query->select($db->quoteName('nome_completo', 'Nome'));
		$query->select($db->quoteName('tipo', 'Tipo'));
		$query->select($db->quoteName('payVariation', 'Modelo'));
		$query->select($db->quoteName('tamanho', 'Tamanho'));
		$query->select($db->quoteName('telefone', 'Telefone'));
		$query->select($db->quoteName('payQuantity', 'Quantidade'));
		$query->select($db->quoteName('data', 'Data'));
		$query->select($db->quoteName('cor', 'Cor'));*/
		$query->from($db->quoteName('#__uniformes_beach'));
		$query->where($db->quoteName('form').' = ' .$db->quote($form));
		$query->order('data DESC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}

}