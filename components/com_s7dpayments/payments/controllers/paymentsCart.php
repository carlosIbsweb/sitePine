<?php
/**
 * @version     1.0.0
 * @package     com_s7dpayments
 * @copyright   Copyright (C) 2016. Todos os direitos reservados.
 * @license     GNU General Public  License versão 2 ou posterior; consulte o arquivo License. txt
 * @author      Carlos <carlosnaluta@gmail.com> - http://site7dias.com.br
 */

// No direct access
defined('_JEXEC') or die;

class paymentsCart extends s7dPayments
{

//Cart ------------------------------------------------------------
	public static function setCart($cartid)
	{
        
        $idcart = "'".$cartid."'";

        //date
        $date = "'".date('Y/m/d H:i:s')."'";

        //Inserindo os dados do usúario;
        $db =& JFactory::getDBO();

        $query = "INSERT INTO `#__s7dpayments_cart` (`cartid`,`date`)
        VALUES ($idcart,$date);";
        $db->setQuery( $query );

        return $db->query();
            
	}

	/*********
	 *Adicionar Crianças. Tabela payments_cri.
	*********/
	public static function setCri($userid,$criancas)
	{

        //Inserindo os dados do usúario;
        $db =& JFactory::getDBO();


        //Buscando Dados existentes
        $db->setQuery('SELECT #__s7dpayments_cri.userid FROM #__s7dpayments_cri WHERE  userid = '.$db->quote($userid));
        $result = $db->loadResult();

        if(empty($result))
        {
	
        	$query = "INSERT INTO `#__s7dpayments_cri` (`userid`,`criancas`)
        	VALUES ($userid,".$db->quote( $criancas ).")";
        	$db->setQuery( $query );
        }else{
        	$criUp = array();
        	$criIn = array();
        	foreach(json_decode(self::getCri( $userid )) as $kid=> $item)
        	{
        		$criUp[$kid] = $item;
        	}

        	foreach(json_decode($criancas) as $kid=> $item)
        	{
        		$criIn[$kid] = $item;
        	}

        	$cri = json_encode(array_merge($criUp,$criIn),JSON_UNESCAPED_UNICODE);
        	$query = "UPDATE #__s7dpayments_cri SET criancas = ".$db->quote($cri)."
        	WHERE userid =".$db->quote($userid);
        	$db->setQuery($query);
        }

        return $db->query();
	}

	/*********
	 *Buscar
	*********/
	public static function getCri($userid)
	{
        //Inserindo os dados do usúario;
        $db =& JFactory::getDBO();

        //Buscando Dados existentes
        $db->setQuery('SELECT #__s7dpayments_cri.criancas FROM #__s7dpayments_cri WHERE  userid = '.$db->quote($userid));
        $result = $db->loadResult();
        return $result;
    }

	public static function getCart($cartid,$dadoc,$dado)
	{
		$cdb = JFactory::getDbo();

		$idcart = "'".$cartid."'";

        $cdb->setQuery('SELECT #__s7dpayments_cart.'.$dado.' FROM #__s7dpayments_cart WHERE  '.$dadoc.' = '.$idcart);

        return $cdb->loadResult();
	}

	public static function setCartUpdate($cartid,$userid)
	{	
		
		$list = array_filter($_POST['course']);
		$criancas = is_array($_POST['criancas']) ? array_filter($_POST['criancas']) : null;

        $capJson = self::getCart($cartid,'cartid','products');

		/*Diarias-----------------------------------*/
		

        //cupom de desconto
        $cDiscount = self::getCart($cartid,'cartid','discount');
        $cDiscount = $cDiscount === 0 ? null : $cDiscount;

        $aDate = date('d/m/Y H:i');

        //buscando os dados do produto
       foreach(self::getCat($_GET['cat']) as $cat):
			$catTitle = $cat->title;
			foreach(self::getItens($_GET['courseId']) as $items):
				if($cat->id == $items->catid):
					
				if($items->discount){
					//Preço com desconto
					$originalPrice 	= $items-> price;
					$finalPrice 	= $items->price - ($items->price * ($items-> discount/100));
				}else{
					$finalPrice = $items-> price;
				}
					$title = $items->title;
					$image = $items->image;
					
					//Params Categoria Pai seg
					$cparam = json_decode(self::getCategory(self::getCategory($cat->id,'parent_id'),'params'));
					$dias = $cparam->diarias;
					$courseid = $items->id;
					$catid = $items->catid;
					$courseCode = $cat->alias == 'cursos-completos' ? $items->alias : $cat->alias;
				endif;
			endforeach;
		endforeach;

		$criId 		= uniqid();
		$products 	= [];
		$criJson 	= [];

		$finalPrice = $cDiscount ? $finalPrice - ($finalPrice * ($cDiscount/100)) : $finalPrice;

		//Preço por diária-------------------------------------------------
		$diarias = $list['diaria'];
		$days = self::eDias($dias);


		//Convertento para inteiro o dia.
		$dDays = array_map(array(self,'dayInt'),$days->days);

		//Quantidade de dias permitidos
		$diap = $cparam->diaspermitido;
		//return false;
		/*Barrando dias vazio ou inexistentes*/
		if(array_diff(explode(',',$diarias),$dDays) && !empty($diarias)) 
		{
			echo '<div class="alert alert-danger paymentsAlert" role="alert"> <strong>Erro!</strong> Alguns dos dias não permitidos</div>';
			return false;
		}


		//Verificar quantidade de dias e filtrar dias.
		$diariaD = self::diarias($diarias,$dDays,$diap);
		if(!$diariaD){
			echo '<div class="alert alert-danger paymentsAlert" role="alert"> <strong>Erro!</strong> Máximo de diárias permitido é '.$diap.'.</div>';
			return false;
		}

   
		if( (isset($list['semana']) && $list['semana'] == 'diaria') && (!empty($cparam->diarias) && $cparam->tipodevenda == 1 || $cparam->tipodevenda == 3 ))
		{
			if(count(array_filter(explode(',',$diarias))) == 0)
			{
				echo '<div class="alert alert-danger paymentsAlert" role="alert"> <strong>Erro!</strong> Em Diárias é necessário que selecione ao menos 1 dia.</div>';
				return false;
			}

		
				/*
					Valor completo / 4
				*/
				$dayx = count(array_filter(explode(',',$diarias)));
				$finalPrice = ($finalPrice/$cparam->divisao)*$dayx;

				//Arrendodnado para baixo a diária
				$finalPrice = floor($finalPrice * 100) / 100;

				/*Title*/
				$tD = '<br> <span>Diárias: '.self::diarias($diarias,$dDays,$diap,$days->diaM,2).'</span>';
		}

		/*Pacote completo mesmo*/
		if($diariaD === true)
		{
			$diariaD = '';
			$tD = '';
		}


		/*************************************-------------------------------- */
		//

        $complementos = Array
        (
			"title"      => true,
        	"data" 		 => $aDate,
        	"price" 	 => $finalPrice,
        	"course" 	 => $title.$tD,
        	"img" 		 => $image,
        	"id" 		 => $courseid,
        	"catid" 	 => $catid,
        	"cattitle" 	 => $catTitle,
        	"courseCode" => $courseCode,
        	"criancas" 	 => $criancas,
			"diarias"    => $diariaD,
			"semana"     => $list['semana'],
			"periodo"    => $dias
        );

        $jsonS = $complementos;
        array_push($products, $jsonS);
        array_push($criJson,$criancas);
        //$json = is_array(json_decode(self::getCart($cartid,'cartid','products'),true)) ?  : 
        		//json_encode($products,JSON_UNESCAPED_UNICODE);

        $pJson = array();
        if(is_array(json_decode(self::getCart($cartid,'cartid','products'),true))):
			foreach(json_decode(self::getCart($cartid,'cartid','products')) as $k => $items):
				if($items->id != $courseid):
					array_push($pJson,$items);
				else:
					$criCart = count($items->criancas);
				endif;
			endforeach;

			$json = json_encode(array_merge($pJson,$products),JSON_UNESCAPED_UNICODE);
		else:
			$json = json_encode($products,JSON_UNESCAPED_UNICODE);
		endif;

		if(empty($criancas)):
			echo '<div class="alert alert-danger paymentsAlert" role="alert"> <strong>Erro!</strong> Ingresso deve conter pelo menos uma criança!</div>';
			return false;
		else:
			self::setCri($userid,json_encode($criancas,JSON_UNESCAPED_UNICODE));
			//redirecionando para o carrinho
			header('Location: ?cart');
		endif;

        $db = JFactory::getDbo();

        $query = "UPDATE #__s7dpayments_cart
        SET products = '".$json."', userid = '".$userid."'
        WHERE cartid ='".$cartid."'
        ";
        $db->setQuery($query);

		$db->query();

		return true;

	}

	//Passando numero inteiro
	function dayInt($day){
		return intVal($day);
	}

	public static function getCartList()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('id','date','cartid')));
		$query->from($db->quoteName('#__s7dpayments_cart'));
		$query->order('id ASC');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}

	public function deleteCart($cartid)
	{
		$list = array();
		if(is_array(json_decode(self::getCart($cartid,'cartid','products'),true))): 
		foreach(json_decode(self::getCart($cartid,'cartid','products')) as $k => $items):
			if($items->id != $_POST['delitem']):
				$list[$k] = $items;
			endif;
		endforeach;
		endif;

		$listDel = count($list) != 0 ? json_encode($list,JSON_UNESCAPED_UNICODE) : '';

		$listDelAll = $_POST['delitem'] == 'delAll' ? '' : $listDel;


		$db = JFactory::getDbo();

        $query = "UPDATE #__s7dpayments_cart
        SET products = '".$listDelAll."'
        WHERE cartid ='".$cartid."'
        ";
        $db->setQuery($query);

		$db->query();
	}

	public function delCartExpire($cartid)
	{
		$del = "'".$cartid."'";

		$db = JFactory::getDbo();
 
		$query = $db->getQuery(true);
 
		// delete all custom keys for user 1001.
		$conditions = array(
    	$db->quoteName('cartid') . ' = '.$del);
 
		$query->delete($db->quoteName('#__s7dpayments_cart'));
		$query->where($conditions);
 
		$db->setQuery($query);
 
		$result = $db->execute();
	}

	public static function executCart($userid)
	{
		$cartid = $_SESSION['cartid'];


		//Quantidade de crianças.
		$qntCri = [];
		$count = count(json_decode(self::getCart($cartid,'cartid','products'),true));

		//Pegando o carrinho completo
		$gCart = json_decode(self::getCart($cartid,'cartid','products'),true);

		if(isset($_GET['l'])){
			print_r($gCart);
			exit();
		}

		foreach($gCart as $cart){
			array_push($qntCri,$cart['criancas']);
		}

		//Quantidade de ciranças pro grupo de descoto, se tiver tivo o grupo.
		$limiteGrupo = count($qntCri[0]);

		if(isset($_POST['pdiscount'])):
			$cupom = self::getDiscont($_POST['pdiscount']);
			self::setDiscount($cupom,$cartid,$userid,$_POST['pdiscount'],$limiteGrupo);
		endif;

		//Instanciando o methodo courses
		$courses = self::getCourses();

		//Destruindo a sessão ltem.
		if(isset($_SESSION['ltem']) && !isset($_GET['cart'])): unset($_SESSION['ltem']); endif;

		isset($_SESSION['cartid']) && time() - $_SESSION['cartTim'] > 3600 ? session_unset() : (isset($_GET['store']) || isset($_GET['cart']) ? $_SESSION['cartTim'] = time() : null);

		if(!isset($_SESSION['cartid'])):
			$_SESSION['cartid'] = uniqid();
			$_SESSION['cartTim'] = time();
		endif;

		if(!empty($cartid) && isset($_GET['store'])):
			if(!empty(self::getCart($cartid,'cartid','id'))):
				isset($_POST['enviar']) ? self::setCartUpdate($cartid,$userid) : null;
			else:
				self::setCart($cartid);
			endif;
		endif;

		if(isset($_POST['delitem'])):
			self::deleteCart($cartid);
		endif;

		/***********
		 Inserindo Pagamentos.
		**********/
		//if(isset($_POST['formPag']) || isset($_POST['ftransf'])):
			//count($courses->items) < 1 ? self::setCourses($cartid) : self::setCoursesUpdate($userid,$cartid);
		//endif;

		//se cupom do grupo não passar
		if(self::getDiscontGroup($_SESSION['pdiscountused'],$limiteGrupo)){
			return false;
		}

		if(isset($_POST['formPag']) || isset($_POST['ftransf'])):
			unset($_SESSION['pdiscountused']);
			self::setCourses($cartid);
		endif;



		return true;
	}

	//Desconto Valt
	public static function getDiscont($cupom, $dado = 'discount')
	{
		// Build the query for the table list.
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT '.$db->quoteName($dado)
			. ' FROM #__s7dpayments_discount'
			//. ' WHERE codigo = ' . $db->quote($cupom)
			. ' WHERE codigo = ' . $db->quote($cupom) .' AND '.$db->quote(date('Y-m-d')).' < `valid` AND `state` = '.$db->quote(1)
		);

		try {
			$result = $db->loadResult();
		} catch (Exception $e) {
			echo 'erro';
		}

		if(empty($result))
		{
			return false;
		}
		else
		{
			return $result;
		}
	}

	//Desconto Uniq Voucher
	public static function getDiscontUniq($cupom)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
		$query
		    ->select(array('a.*','b.codigo','b.limit','b.uniq'))
		    ->from($db->quoteName('#__s7dpayments_discount_used', 'a'))
		    ->join('INNER', $db->quoteName('#__s7dpayments_discount', 'b') . ' ON ' . $db->quoteName('a.discountId') . ' = ' . $db->quoteName('b.codigo'))
		    ->where($db->quoteName('b.codigo') . ' = ' . $db->quote($cupom))
		    ->where($db->quoteName('b.state') . ' = ' . $db->quote(1));
		    //->where($db->quoteName('b.uniq') . ' = ' . $db->quote(1));

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		

		$count = count($results);
		$limit = $results[0]->limit;
		$uniq = $results[0]->uniq;

		//Grupos
		$grupo = $results[0]->grupo;
		$grupo_qnt = $results[0]->grupo_qnt;
		
		if( $count >= $limit && $uniq ){
			return true;
		}else {
			return false;
		}
	}

	//Desconto Uniq Voucher grupo
	public static function getDiscontGroup($cupom,$qnt = 1)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
		$query
		    ->select(array('*'))
		    ->from($db->quoteName('#__s7dpayments_discount', 'b'))
		    ->where($db->quoteName('b.codigo') . ' = ' . $db->quote($cupom))
		    ->where($db->quoteName('b.state') . ' = ' . $db->quote(1));
		    //->where($db->quoteName('b.uniq') . ' = ' . $db->quote(1));

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		//Grupos
		$grupo = $results[0]->grupo;
		$grupo_qnt = $results[0]->grupo_qnt;
		
		if($grupo && $qnt <= $grupo_qnt){
			if(($qnt+1) > $grupo_qnt){
				return false;
			}
			echo '<div class="alert alert-danger cf-alert" role="alert">Voucher só é válido com igresso maior que '.$grupo_qnt.'</div>';
			return true;
		}else {
			return false;
		}
	}

	public static function setDiscount($discount,$cartid,$userId,$discountId,$qnt = 1)
	{
		/*Voucher Unico*/
		if(self::getDiscontUniq($discountId)){
			echo '<div class="alert alert-danger cf-alert" role="alert">Voucher inválidoa!</div>';
			return false;
		}

		if(self::getDiscontGroup($discountId,$qnt)){
			return false;
		}

		if(!$discount)
		{	
			echo '<div class="alert alert-danger cf-alert" role="alert">Voucher inválidob!</div>';
			return false;
		}

		//Set Discount Used
		if(self::getDiscontUsed($userId,$discountId,$cartid, self::getDiscont($discountId, 'limiteusuario')))
		{
			return false;
		}

		$capJson = self::getCart($cartid,'cartid','products');

		/*//////////////\\\\\\\\\\\
		 * Desconto nos produtos
		 *///////////////\\\\\\\\\\\
		$arr = array();
		$nArr = array();
		foreach(json_decode($capJson) as $ak=> $item)
		{
		  foreach($item as $k => $i)
		  {
		    if($k == 'price')
		    {
		      $discountT = $i - ($i * ($discount/100));
		      $arr[$ak][$k] = $discountT;
		    }
		    else
		    {
		      $arr[$ak][$k] = $i;
		    }
		  }
		}
		
		$products = json_encode($arr,JSON_UNESCAPED_UNICODE);


		// Initialiase variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Create the base update statement.
		$query->update($db->quoteName('#__s7dpayments_cart'))
			->set($db->quoteName('discount') . ' = ' . $db->quote($discount))
			->set($db->quoteName('products') . ' = ' . $db->quote($products))
			->set($db->quoteName('voucher') . ' = ' . $db->quote($discountId))
			->where($db->quoteName('cartid') . ' = ' . $db->quote($cartid))
			->where($db->quoteName('userid') . ' = ' . $db->quote($userId));
		
		// Set the query and execute the update.
		$db->setQuery($query);
		
		try
		{
			if($db->execute())
			{
				self::setDiscountUsed($userId,$discountId,$cartid);
				$_SESSION['pdiscountused'] = $discountId;

				//Voucher Vips
				if(self::getDiscont($discountId,'vip') == 1)
				{
					$_SESSION['pineVip'] = $discountId;
				}
				return true;
			}
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return true;
	}

	public static function getDiscontUsed($userId,$discountId,$cartId,$limiteUsuario = 0)
	{
		// Initialiase variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Create the base select statement.
		$query->select('*')
			->from($db->quoteName('#__s7dpayments_discount_used'))
			->where($db->quoteName('userId') . ' = ' . $db->quote($userId));
		
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

		if(count($result))
		{
			foreach($result as $item)
			{
				if($item->discountId == $discountId && $limiteUsuario == 0)
				{	
					echo '<div class="alert alert-danger cf-alert" role="alert">Este Voucher já foi utilizado!</div>';
					return true;
				}

				if($item->cartId == $cartId)
				{
					echo '<div class="alert alert-danger cf-alert" role="alert">Só é permitido um Voucher por compra!</div>';
					return true;
				}
			}
		}
		else
		{
			return false;
		}
	}

	public static function setDiscountUsed($userId,$discountId,$cartId)
	{
		// Initialiase variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Create the base insert statement.
		$query->insert($db->quoteName('#__s7dpayments_discount_used'))
			->columns(array($db->quoteName('userId'), $db->quoteName('discountId'), $db->quoteName('cartId')))
			->values($db->quote($userId) . ', ' . $db->quote($discountId). ', ' . $db->quote($cartId));
		
		// Set the query and execute the insert.
		$db->setQuery($query);
		
		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}
	}

	/*Separa os dias
		ex: 27/06/2022 01/07/2022
		return array
	*/

	public static function eDias($date)
	{	
			
		list($data_i,$data_f) = explode(' ',$date);
			
		$sem = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];


		$data_i = date('Y-m-d',strtotime(str_replace('/','-',$data_i)));
		$data_f = date('Y-m-d',strtotime(str_replace('/','-',$data_f)));


		// Calcula a diferença em segundos entre as datas
		$diferenca = strtotime($data_f) - strtotime($data_i);

		//Calcula a diferença em dias
		$dias = floor($diferenca / (60 * 60 * 24));

		$aDays = array();
		$aSem  = array();
		$anM = array();
		$diaM = array();

		for($i = 0; $i <= $dias; $i++){
			$semD = date('w',strtotime("$data_i + $i days "));
			array_push($aDays,date('d',strtotime("$data_i + $i days ")));
			array_push($aSem,$sem[$semD]);
			array_push($anM,date('m',strtotime("$data_i + $i days ")));
			$diaM[intVal(date('d',strtotime("$data_i + $i days ")))] = intVal(date('m',strtotime("$data_i + $i days ")));
		}
			$items = new stdClass();
			$items->days = $aDays;
			$items->sem = $aSem;
			$items->mes = $anM;
			$items->diaM = $diaM;
			
			return $items;
	}

	/*Filtrar campo para permitir somente dias em que estiver na semana*/
	public static function diarias($arrs,$arg,$per,$inp = ',',$inpq = 1){

		$arrs = explode(',',$arrs);

		/*verificar se os dias permitidos estão no padrão */
		if(count(array_filter($arrs)) > $per)
		{
			return false;
		}

		$ars = array();
		foreach($arrs as $arr)
		{
			if(in_array($arr,$arg)){
				array_push($ars,$arr);
			}
		}

		$out = $ars;
		if(count($ars) == 0)
		{
			return true;
		}

		if($inpq == 2){
			$ars = array_map(array(self,'apad'),$ars);

			$out = array();
			sort($ars);
			foreach($ars as $ar){
				array_push($out, $ar.'/'.self::apad($inp[intVal($ar)]));
			}

			return implode(', ',$out);
		}

		return implode(',',$out);
	}

	function apad($item){
		return str_pad($item,2, "0", \STR_PAD_LEFT);
	}

	public static function getGrupo() {

		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query
		    ->select(array('*'))
		    ->from($db->quoteName('#__s7dpayments'))
		    ->where($db->quoteName('status') . ' IN(1,2,3,4,5) ');
		    //->where($db->quoteName('b.uniq') . ' = ' . $db->quote(1));

		$db->setQuery($query);

		$results = $db->loadObjectList();

		// Categoria que eu irei contar a quantidade de crianças
		$catid = $_GET['cat'];

		//print_r(self::getGrupoPorQuantidade($catid));

		// Começar a contagem
		$contagem = 0;

		foreach($results as $result)
		{
			$json = $result->items;

			// Decodificar o JSON em um array associativo
			$data = json_decode($json);

			// Iterar sobre os itens do JSON e contar com base no catid
			foreach ($data as $item) {
			    if($item->catid == $catid){
			    	foreach($item->criancas as $criancaId => $crianca){
			    		$contagem += 1;
			    	}
			    }
			}
		}
		//echo "Quantidade de crianças com catid $catid: $contagem";
	}

	public static function getGrupoPorQuantidade($catid)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
		$query
		    ->select(array('pg.*'))
		    ->from($db->quoteName('#__pine_grupos', 'pg'))
		    ->join('LEFT', $db->quoteName('#__s7dpayments_courses', 'c') . ' ON ' . $db->quoteName('c.pine_grupo_id') . ' = ' . $db->quoteName('pg.id'))
		    ->where($db->quoteName('c.catid') . ' = ' . $db->quote($catid));
		    //->order($db->quoteName('a.created') . ' DESC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}
}