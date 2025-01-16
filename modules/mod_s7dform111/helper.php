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
jimport('joomla.factory');
jimport('joomla.database.table');
jimport('joomla.error.error');

use Joomla\CMS\Factory;

spl_autoload_register(function ($class) {
    @include dirname(__FILE__) . '/library/classes/'.$class.'.php';
});


class modS7dformHelper
{

	public static function getItems(&$params)
	{

	}

	public function setFormAjax()
	{

		$input = Factory::getApplication()->input;
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
		$db = Factory::getDBO();
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
		$_SESSION['awToken'] = uniqid('', true) . bin2hex(random_bytes(16));

		/*
			Novas saidas
		*/
		$report['edicaoUsuario'] = $params->get('editUser');
		$report['redirect'] = $params->get('redirect');
		$report['redirecturl'] = $params->get('redirecturl');
		$report['timeredirect'] = $params->get('timeredirect');


		//echo self::upload($iFiles,$moduleId);
		//return false;
		//exit();

		//Valid Campos
		/*if(!self::awRequired($iPosts,$params,$iFiles))
		{
			return false;
		}*/



		if(!self::awValid($iPosts,$params,$iFiles))
		{
			return false;
		}

		//Compara senhas se existir o equalto vindo do javascript
		if($iPosts['equalto']){
			echo awUtilitario::awMessages($iPosts['equalto'],'danger');
			exit();
		}

		//Valid Captcha
		if(!self::awCaptchaAjax($awCaptcha,false,null,$awCaptchaRest,$params) && $params->get('awcaptcha'))
		{
			return false;
			exit();
		}
		
		//Upload de arquivos
		if(!awFileUploader::uploadFail($iFiles,$params->get('exMedia'))){
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
		
		//Set Email User
		if($params->get('mailuserativ'))
		{
			//Capturar email de usuário
			$mailUser = $params->get('mailuser');
			if(awEmails::setEmail($iPosts,$_FILES,$params,$params->get('bodyuser'),$params->get('subjectuser'),$$mailUser) === false)
			{
				return false;
				exit();
			}
		}

		//Set Email Admin
		if($params->get('activeEmail'))
		{
			if(awEmails::setEmail($iPosts,$_FILES,$params,$params->get('bodyadmin'),$params->get('subject'),$params->get('mail')))
			{
				$report['success'] = true;
			}
		}
		else
		{
			$report['success'] = true;
		}

		//Set DB
		if(($params->get('activDb') && $params->get('db')))
		{	
			//Set DB	
			if(!awDbController::setDb($iPosts,$params,$_FILES,$moduleId))
			{
				return false;
				exit();
			}
		}


		if($report['success'])
		{	
			if($params->get('payment')){
				$menSuccess .= self::modPagSeguro($params,$moduleId,$iPosts);
			}
			$report['mSuccess'] = awUtilitario::awMessages($menSuccess,'success');
			echo json_encode($report);
		}
		exit();	
	}

	public function awCaptchaAjax($g = null,$jResult = true,$idModule = '',$gValue = null)
	{
		$moduleId = explode('-',$_POST['moduleId']);
		$moduleId = empty($moduleId[1]) ? $idModule : $moduleId[1];

		/********************
		 *GET Params
		********************/
		$db = Factory::getDBO();
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
			if(!awCaptcha::getAwCaptcha($g,$jResult,$idModule,$gValue,$moduleId,$params)){
				exit();
			}else{
				return awCaptcha::getAwCaptcha($g,$jResult,$idModule,$gValue,$moduleId,$params);
			}
		}
	}


	public static function awRequired($inputs,&$params,$iFiles = [])
	{
		if(!self::awValidador($inputs,$params,$iFiles,'obrigatorio','valid')){
			exit();
		}

		return true;
		
	}

	public static function awValid($inputs,$params,$iFiles)
	{
		if($params->get('awJoomla')){
			//Vars Inputs
		extract($inputs);

		if($params->get('awJCampo') && $params->get('awJCampoExpressao')){
			$awCampo = $params->get('awJCampo');
			list($awCampoName,$awCampoText) = explode(':',$awCampo); 
			$awExpressao = $params->get('awJCampoExpressao');
			
			if(!self::regex($$awCampoName,$awExpressao)){
				echo awUtilitario::awMessages($awCampoText,'danger');
				exit();
			}
		}

		}
		if(!self::awValidador($inputs,$params,$iFiles, false,'valid-type')){
			exit();
		}

		return true;
	}


	public function awLoginAjax(){

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
		$db = Factory::getDBO();
		$db->setQuery("SELECT params FROM #__modules WHERE module = 'mod_s7dform' and published = '1' and id =".$moduleId);
		
		try {
			$module = $db->loadObject();
		} catch (Exception $e) {
			JError::raiseWarning(500, $e->getMessage());
		}
		
		$params = new JRegistry();
		$params->loadString($module->params);


		//Valid Campos

		if(!self::awValid($iPosts,$params,$iFiles))
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
			echo awUtilitario::awMessages('Dados atualizados com sucesso','success');
		}	
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
		$db = Factory::getDBO();
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
			echo awUtilitario::awMessages($menSuccess,'success');
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

    //Verificar dados de data.tipoDeDado
    public static function dateVerific(&$params,$iPosts){ 

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
			echo awUtilitario::awMessages($texto.' '.$anos.' anos','danger');
			return false;
		}

		return true;
	}

	

	public function modPagSeguro(&$params,$modId,$iPosts)
	{

		//Vars Sender.
		$payName 		= $params->get('payName');
		$payAreaCode 	= $params->get('payAreaCode');
		$paySenderPhone = $params->get('paySenderPhone');
		$paySenderEmail = $params->get('paySenderEmail');


		//Mod PagSeguro
		$checkout = new awModPagSeguro(
			$params->get('payToken'),
			$params->get('payEmail'),
			$params->get('payCurrency'),
			array(
				$modId,
				(int)1,
				$params->get('payDescription'),
				trim($params->get('payPrice'))
			),
			$params->get('payReference'),
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


    public function dadosTabelaRelacaoUsuariosAjax($p = false, $params = '{}'){

    	$user = Factory::getUser();

    

    	if($p == false){
    		$formData = $_POST;

        	//Params
        	$params = self::getParams($formData['formId']);

        	if($params->get('editUser') != 1){
    			echo json_encode(['user' => false]);
    			exit();
    		}
    		echo json_encode(awDbController::getDados($params->get('db'),$user->id,$params));
      	
    		Factory::getApplication()->close();
    	}else{
    		return awDbController::getDados($params->get('db'),$user->id,$params);
    	}
        

        
    }


    //Get params modulo ajax
    public function getParams($moduleId){
    	/********************
    	 *GET Params
    	********************/
    	$db = Factory::getDBO();
    	$db->setQuery("SELECT params FROM #__modules WHERE module = 'mod_s7dform' and published = '1' and id =".$moduleId);
    	
    	try {
    		$module = $db->loadObject();
    	} catch (Exception $e) {
    		JError::raiseWarning(500, $e->getMessage());
    	}
    	
    	$params = new JRegistry();
    	$params->loadString($module->params);

    	return $params;
    }

    //Validador
    public static function awValidador($inputs,&$params,$iFiles = [], $validador = 'obrigatorio', $tipoDeValidador = 'valid')
    {
 
    	$vNames  = [];
    	$vValues = [];
    	$vLabels = [];
    	$vValids = [];
    	$status = true;


    	foreach(json_decode($params->get('s7dform'))->fields as $n => $v)
    	{
    		$reqs = $v->attrs->{$tipoDeValidador};

    		if(isset($reqs))
    		{
    			foreach($reqs as $req)
    			{
    				if($req->selected)
    				{
    					array_push($vNames,$v->attrs->name);
    					array_push($vValues,$validador ?: $req->value);
    					array_push($vLabels,$v->config->label);
    				}
    			}
    		}
    	}
    	

    	$dados = array_merge($inputs, $iFiles);

    	$keysWithNonEmptyValues = array();
    	$validValues = array();
    	foreach ($dados as $key => $value) {
    		if(is_array($value)){
    			foreach($value as $vkey => $val){
    				if(is_array($val))
    				{
    					foreach($val as $vkeyv => $val2)
    					{
    						if(is_array($val2)){
    							foreach($val2 as $v2key => $val3)
    							{
    								$inputCampo = $key.'[]['.$vkeyv.'][]';
    								if (!empty($val3)) {
    	        						$keysWithNonEmptyValues[] = $inputCampo;
    	    						}
    	    						$validValues[$inputCampo] = $val3;
    							}
    						}else{
    							$inputCampo = $vkeyv ? $key.'[]['.$vkeyv.']' : $key.'[]';
    							if (!empty($val2)) {

    	        					$keysWithNonEmptyValues[] = $inputCampo;
    	    					}
    	    					$validValues[$inputCampo] = $val2;
    						}
    					}
    				}else{
    					$inputCampo = $key.'[]';
    					if (!empty($val)) {
    	        			$keysWithNonEmptyValues[] = $inputCampo;
    	    			}
    	    			$validValues[$inputCampo] = $val;
    				}
    			}
    		}else{
    			$inputCampo = $key;
    			if (!empty($value)) {
    	        	$keysWithNonEmptyValues[] = $inputCampo;
    	    	}
    	    	$validValues[$inputCampo] = $value;
    		}
    	}

    	//Tirando a obrigatoriedade de anexos para edição de dados caso existam no banco.
    	$dadosAnexos = json_decode(self::dadosTabelaRelacaoUsuariosAjax(true,$params)[0]->anexos,true);
    	$dadosAnexosKeys = array_keys($dadosAnexos);

    	$dadosAnexosAll = $dadosAnexos;
    	$dadosAnexosPost = $inputs['aw_anexos'];

    	$dadosAnexos = array_map(function($dado){
    		return $dado.'[]';
    	}, $dadosAnexosKeys);


    	$camposObrigatorios = array_diff($vNames,$keysWithNonEmptyValues);
		
		//removendo obrigatoriedade de anexos ou não.
		$camposObrigatorios = $dadosAnexos ? array_diff($camposObrigatorios,$dadosAnexos) : $camposObrigatorios;

    	$camposParaValidar = $vNames;




    	/*$iFilesValidacao = array_map(function($file){
    		return $file.'[]';
    	},array_keys($iFiles));

    	//Se conter um arquivo file, então valide ele.
    	$dadosAnexos = array_diff($dadosAnexos,$iFilesValidacao);*/

		// Calcule a diferença usando array_udiff

		// Reformate $array2 para corresponder à estrutura de $array1
		$dadosAnexosPostFormatado = [];
		foreach ($dadosAnexosPost as $elemento) {
		    foreach ($elemento as $chave => $valor) {
		        $dadosAnexosPostFormatado[$chave][] = $valor;
		    }
		}

		$diferenca = array_udiff($dadosAnexosPostFormatado,$dadosAnexosAll,'array_diff_assoc');

		if($diferenca){

			echo awUtilitario::awMessages('Você não pode modificar os anexos de edição dessa forma.','danger');
			return false;
		}
		

    	//Mesma coisa aqui
    	$camposParaValidar = $dadosAnexos  ? array_diff($camposParaValidar,$dadosAnexos) : $camposParaValidar;


    	if($validador == 'obrigatorio'){
    		$campos = $camposObrigatorios;
    	}else{
    		$campos = $camposParaValidar;
    	}


    	$vC = array_combine($vNames,$vValues);
    	$vL = array_combine($vNames,$vLabels);

    	foreach($campos as $vn => $campo){

    		//Tipo de dado, campo ou valor;
    		$tipoDeDado = $vC[$campo] != 'file' ? $validValues[$campo] : str_replace('[]','',$vNames[$vn]);
    		if(!awValid::awV($vC[$campo],$tipoDeDado,$vL[$campo],$vNames[$vn]))
    		{
    			$status = false;
    		}
    	}

    	if($status)
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }

    public static function regex($valor,$regex){

    	// Teste se a string corresponde à expressão regular
    	if (preg_match('/'.$regex.'/', $valor)) {
    	    return true;
    	} else {

    	    return false;
    	}
    }

}