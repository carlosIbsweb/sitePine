<?php
/**
 * @version     1.0.0
 * @package     com_s7dpayments
 * @copyright   Copyright (C) 2016. Todos os direitos reservados.
 * @license     GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 * @author      Carlos <carlosnaluta@gmail.com> - http://site7dias.com.br
 */

// No direct access
defined('_JEXEC') or die;

class s7dPayments
{
	public static function getItens()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('title','alias','videos','image','price','discount','id','description','videoslink','videosdescription','arquivos','catid','type','package','categorias')));
		$query->from($db->quoteName('#__s7dpayments_courses'));
		isset($_GET['courseId']) ? $query->where($db->quoteName('id') . ' = '. $db->quote($_GET['courseId'])) : null;
		$query->where($db->quoteName('state') . ' = '. $db->quote(1));
		

		$app = JFactory::getApplication();
    	$menu = $app->getMenu()->getActive();
    	$params = $menu->getParams();

		$getOrder = $params->get('feccotinho');

		$gOrder = !empty($getOrder) ? $getOrder : 'price ASC';
		
		$query->order($gOrder);
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}

	public static function setItens($vToken,$courseId)
	{
		$user = JFactory::getUser();
        $Pid = $user->id; 

        $db = JFactory::getDbo();

        $query = "UPDATE #__s7dpayments_courses
        SET videoslink = '".$vToken."'
        WHERE id ='".$courseId."'
        ";
        $db->setQuery($query);

        return $db->query();
	}

	public static function getCourses()
	{
		$user = JFactory::getUser();
        $userid = $user->id;

        $listCourses = new stdClass();

		// Get a db connection.
		$db = JFactory::getDbo();
		 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('name','userid','items')));
		$query->from($db->quoteName('#__s7dpayments'));
		$query->where($db->quoteName('state') . ' = '. $db->quote(1));
		$query->where($db->quoteName('userid') . ' = '. $db->quote($userid));
		$query->order('id ASC');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		$list = array();
		foreach($results as $k=> $items):
			if(is_array(json_decode($items->coursesid,true))):
			foreach(json_decode($items->coursesid) as $k=> $courses):
				$list[$k] = $courses;
			endforeach;
			endif;
		endforeach;

		$listCourses->list = $list;
		$listCourses->items = $results;

		return $listCourses;
	}

	public static function getCat()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('title','id','alias','params')));
		$query->from($db->quoteName('#__categories'));
		$query->where($db->quoteName('published') . ' = '. $db->quote(1));
		$query->where($db->quoteName('extension') . ' = '. $db->quote('com_s7dpayments'));
		isset($_GET['cat']) ? $query->where($db->quoteName('id') . ' = '. $db->quote($_GET['cat'])) : null;
		isset($_REQUEST['catid']) ? $query->where($db->quoteName('parent_id') . ' = '. $db->quote($_REQUEST['catid'])) : null;
		$query->order('lft ASC');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}

	public static function setCourses($cartid)
	{
		$db =& JFactory::getDBO();

		$user = JFactory::getUser();
		$username = "'".$user->name."'";
		$userid = "'".$user->id."'";
		$date = "'".date('Y/m/d H:i:s')."'";
		$total = $db->quote($_POST['priceTotal']);

		/*******************
		 Forma de Pagamento
		*******************/
		if(isset($_POST['formPag']) && $_POST['formPag'] == 'PagSeguro')
		{
			$formPag = 'PagSeguro';
			$status  = $db->quote(0);
		}

		if(isset($_POST['ftransf'])) {
			$formPag = 'Transf';
			$status  = $db->quote(1);
		}

		$formPagInsert = $formPag == 'PagSeguro' ? 'PagSeguro' : ($formPag == 'Transf' ? 'Transferência Bancária' : 'Outro');
		$formPagInsert = $db->quote($formPagInsert);


		//finalização
		$jsonCourses = paymentsCart::getCart($cartid,'cartid','products');
		$cartId = paymentsCart::getCart($cartid,'cartid','id');
		$reference  = $db->quote('REF-'.$cartId.'PTF');

		$idcourse = uniqid();
		$list = array();
		
		
		foreach(json_decode($jsonCourses) as $k => $items):
			$list[$k] = $items;
		endforeach;

		$mycourses = "'".json_encode($list,JSON_UNESCAPED_UNICODE)."'";

		//Inserindo os dados do usúario;
        $query = "INSERT INTO `#__s7dpayments` (`name`,`userid`,`ref`,`items`,`state`,`date`,`status`,`form`,`total`)
        VALUES ($username,$userid,$reference,$mycourses,'1',$date,$status,$formPagInsert,$total);";
        $db->setQuery( $query );

        if($formPag == 'PagSeguro')
		{
			//criando a sessão fcourse
			$_SESSION['fcourse'] = '';

			$dados = [
				"forma" 	=> "PagSeguro",
				"total" 	=> $_POST['priceTotal'],
				"nome" 		=> $user->name,
				"username" 	=> $user->username,
				"telefone" 	=> $user->telefone,
				"endereco" 	=> $user->endereco,
				"produtos" 	=> $_SESSION['prodF']
			];

			//Notificação de compra Administrador.
            self::sendEmail(JPATH_SITE.'/components/com_s7dpayments/tmpl/default_emailAdminCompra.php','NOVA COMPRA - PagSeguro','contato@pinetreefarm.com.br','Pine Tree Farm','coloniapinetreefarm@gmail.com,carlos@ibsweb.com.br',$dados);

			
            //Removendo prodF
			unset($_SESSION['prodF']);

			//setando os dados
		  	$db->query();
		}

		if($formPag == 'Transf')
		{	
			$_SESSION['ftransf'] = '';

			$dados = [
				"forma" 	=> "Transferência Bancária",
				"total" 	=> $_POST['priceTotal'],
				"nome" 		=> $user->name,
				"username" 	=> $user->username,
				"telefone" 	=> $user->telefone,
				"endereco" 	=> $user->endereco,
				"produtos" 	=> $_SESSION['prodF']
			];

			//Notificação de compra Administrador.
            self::sendEmail(JPATH_SITE.'/components/com_s7dpayments/tmpl/default_emailAdminCompra.php','NOVA COMPRA - Transferência Bancária','contato@pinetreefarm.com.br','Pine Tree Farm','coloniapinetreefarm@gmail.com,carlos@ibsweb.com.br',$dados);

            //Notificação de compra Usuário.
            self::notCli($user->name,$user->username);
			$db->query();
            
            header('Location: '.JUri::base(true).'/informacoes-importantes/143-informacoes-importantes-t.html');

            unset($_SESSION['prodF']);
		}

		return true;

	}

	public static function notCli($mynome,$myemail)
	{

		$nome = $mynome;
		$nome = explode(' ',$nome)[0];
		$dados = [
				"nome" 		=> $nome,
			];
					//Notificação de compra Administrador.
		            self::sendEmail(JPATH_SITE.'/components/com_s7dpayments/tmpl/default_emailNotification.php','Inscrição Pine Tree Farm','contato@pinetreefarm.com.br','Pine Tree Farm',$myemail,$dados);

		            return true;
	}

	public static function setCoursesUpdate($reference,$status)
	{	

        $db = JFactory::getDbo();

       	$reference  = $db->quote($reference);
       	$statis  	= $db->quote($status);

        $query = "UPDATE #__s7dpayments
        SET status = $status
        WHERE ref = $reference
        ";
        $db->setQuery($query);

        //Dados do usuário.
        $user = JFactory::getUser();

		//setando os dados
		$db->query();

		return true;

	}

	//Pegar nome do item
	public static function getPagments($campo,$where,$data)
	{
		//Inserindo os dados do usúario;
        $db =& JFactory::getDBO();

        $where 	= $db->quoteName($where);
        $data 	= $db->quote($data);

        //Buscando Dados existentes
        $db->setQuery('SELECT #__s7dpayments.'.$campo.' FROM #__s7dpayments WHERE '. $where .' = '.$data);
        $result = $db->loadObjectList();
        return $result;
	}

	//Pegar Status
	public static function getStatus($statusId)
	{
		//Inserindo os dados do usúario;
        $db =& JFactory::getDBO();

        $where 	= $db->quoteName($where);
        $data 	= $db->quote($data);

        //Buscando Dados existentes
        $db->setQuery('SELECT #__s7dpayments_status.status FROM #__s7dpayments_status WHERE statusId = '.$statusId);
        $result = $db->loadResult();

        return $result;
	}


	public static function getUser($userid)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('id','name','email','telefone','cidade','estado','cep','endereco','bairro')));
		$query->from($db->quoteName('#__users'));
		$query->where($db->quoteName('id') . ' = '. $db->quote($userid));
		$query->order('id ASC');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}


	//Pegar nome da categoria
	public static function getCategory($id,$campo)
	{
		//Inserindo os dados do usúario;
        $db =& JFactory::getDBO();

        //Buscando Dados existentes
        $db->setQuery('SELECT #__categories.'.$campo.' FROM #__categories WHERE `extension` = \'com_s7dpayments\' and `id` = '.$id);
        $result = $db->loadResult();
        return $result;
	}

	//Pegar nome do item
	public static function getItem($id,$campo)
	{
		//Inserindo os dados do usúario;
        $db =& JFactory::getDBO();

        //Buscando Dados existentes
        $db->setQuery('SELECT #__s7dpayments_courses.'.$campo.' FROM #__s7dpayments_courses WHERE `id` = '.$id);
        $result = $db->loadResult();
        return $result;
	}

	public static function dReplace($string) {

    // matriz de entrada
    $what = array( 'ä','ã','à','á','â','ê','ë','è','é','ï','ì','í','ö','õ','ò','ó','ô','ü','ù','ú','û','À','Á','É','Í','Ó','Ú','ñ','Ñ','ç','Ç',' ','-','(',')',',',';',':','|','!','"','#','$','%','&','/','=','?','~','^','>','<','ª','º' );

    // matriz de saída
    $by   = array( 'a','a','a','a','a','e','e','e','e','i','i','i','o','o','o','o','o','u','u','u','u','A','A','E','I','O','U','n','n','c','C','_','','','','','','','','','','','','','','','','','','','','','','' );

    $text = str_replace($what, $by, trim(strtolower($string)));

	$str = preg_replace("[___]", "__", $text);
	$str = preg_replace("[__]", "_", $text);
	 
	// retira o - quando for o primeiro caracter
	if($str[0]=="_") {
		$str = substr($string, 1);
	}

    // devolver a string
    return $str;
	}
	public static function getUserFile($pass)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('id','name')));
		$query->from($db->quoteName('#__users'));
		$query->where($db->quoteName('password') . ' = '. $db->quote($pass));
		$query->order('id ASC');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}

	//Pegar dados usuário
	public static function getUserName($name,$id)
	{
		$cdb = JFactory::getDbo();

        $cdb->setQuery('SELECT #__users.'.$name.' FROM #__users WHERE  id = '.$id);

        return $cdb->loadResult();
	}

	public static function diffDates($date1)
	{
		$datatime1 = new DateTime($date1);
		$datatime2 = new DateTime(date('Y/m/d H:i:s'));

		$data1  = $datatime1->format('Y-m-d H:i:s');
		$data2  = $datatime2->format('Y-m-d H:i:s');

		$diff = $datatime1->diff($datatime2);
		$horas = $diff->h + ($diff->days * 24);

		$minutos = $diff->format('%i') >= 1 ? $diff->format('%i') : null;
		

		$tano = (int)($horas / 8760) >= 2 ? ' anos atrás' : ' ano atrás';
		$tmes = (int)($horas /730) >= 2 ? ' mêses atrás' : ' mês atrás';
		$tsemana = (int)($horas /168) >= 2 ? ' semanas atrás' : ' semana atrás';
		$tdias = $horas >= 48 ? ' dias atrás' : ' dia atrás';
		$thoras = $diff->format('%h') >= 2 ? ' horas atrás' : ' hora atrás';
		$tminutos = $diff->format('%i') >= 2 ? ' minutos atrás' : ($diff->format('%i') >= 1 ? ' minuto atrás' : ' alguns segundos atrás');
		
		$hormin = $diff->format('%h') == 0 ? $minutos . $tminutos : $diff->format('%h') . $thoras;

		$diahor = $horas >= 24 ? (int)($horas /24) . $tdias :  $hormin;

		$semdia = $horas >= 168 ? (int)($horas /168) . $tsemana : $diahor;

		$mesdia = $horas >= 730 ? (int)($horas /730) . $tmes : $semdia;

		$anomes = $horas >= 8760 ? (int)($horas /8760) . $tano : $mesdia;

		return $anomes;

		
	}

	public static function cutString($mytext,$limit)
	{
		$count = strlen($mytext);
		$limitT = $limit;

		if($count >= $limit):
			$text = substr($mytext, 0, strrpos(substr($mytext, 0, $limitT), ' ')).'...';
		else:
			$text = $mytext;
		endif;

		return $text;
	}


	public static function getRoute($alias)
	{
		//Criando url
		$app  = JFactory::getApplication();
		$mAlias = $app->getMenu()->getActive()->alias;

		$pUrl = explode($mAlias,JUri::current());

		$baseUrl = str_replace("/","",$pUrl[1]);

		// Initialiase variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Create the base select statement.
		$query->select('*')
			->from($db->quoteName('#__s7dpayments'))
			->where($db->quoteName('state') . ' = ' . $db->quote('1'))
			->order($db->quoteName('ordering') . ' ASC');
		
		// Set the query and load the result.
		$db->setQuery($query);
		
		try
		{
			$result = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		echo $app->getMenu()->getItem(157)->parent_id;
	}

	// E-mail Body =============
	public static function sendEmail($bodyText,$assunto,$mailSender,$nameSender,$recipient,$inputs){

		$mail = JFactory::getMailer();
		
		//Recuperando dados do corpo do e-mail.
		$dadosEmail = file_get_contents($bodyText);

		//Carregando texto do body.
		preg_match_all("/{(.+?)}/i", $dadosEmail, $text);

		//Preparando texo de um array.
		preg_match_all("/\[(.+?)\]/i", $dadosEmail, $array);

		//Carregando variáveis do form.
		foreach($inputs as $nm => $value)
		{
			if($value != '' && in_array($nm,array_filter($text[1]))) {
				$iName  = $nm;
				$$iName = $value;
			}
		}

		/****************
		 #### Variaveis Globais ####
		*****************/
		
		$site 		= JUri::base();
		$date 		= date('Y-m-d H:m:s');
		$date 		= date('d/m/Y', strtotime($date)) .' às '.date('H:m:s', strtotime($date));
		$dateTime 	= date("Y-m-d H:i:s");
		$ip 		= $_SERVER["REMOTE_ADDR"];

		//Carregando imagens
		preg_match_all("/src=\"(.+?)\"/", $dadosEmail, $imgs);

		//Recipient.
		$pMail 			= explode(",",trim($recipient));
		$recipientMail 	= count($pMail) == 1 ? $recipient : $pMail;

		$conj = array_merge($text[0],$imgs[1],$array[0]);

		$texto = [];
		foreach($text[1] as $in=> $nms):
			if($nms){
			$names = $nms;
			array_push($texto,$$names);
			}
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
			foreach($inputs as $v)
			{
				if($v['name'] == $i.'[]')
				{
					$arrV = $v['value'].'; ';
					$$i .= $arrV;
				}
			}

			$texto[] = substr($$i,0,-2);		
		}

		/****************
		 # Assunto
		*****************/
		$subject 		= $assunto;
		$messageText 	= str_replace($conj, $texto, $dadosEmail);

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

		return $mail->Send();
	}

	/*********
	 Codificação PagSeguro
	*********/
	public static function ec($text)
	{
  		$enconding = iconv('UTF-8', 'ISO-8859-1', $text);
		return $enconding;
	}

}


?>