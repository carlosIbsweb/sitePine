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
					$courseid = $items->id;
					$catid = $items->catid;
					$courseCode = $cat->alias == 'cursos-completos' ? $items->alias : $cat->alias;
				endif;
			endforeach;
		endforeach;

		$criId 		= uniqid();
		$products = [];
		$criJson = [];

		//Preço
		$finalPrice = $cDiscount ? $finalPrice - ($finalPrice * ($cDiscount/100)) : $finalPrice;

        $complementos = Array
        (
        	"data" 		 => $aDate,
        	"price" 	 => $finalPrice,
        	"course" 	 => $title,
        	"img" 		 => $image,
        	"id" 		 => $courseid,
        	"catid" 	 => $catid,
        	"cattitle" 	 => $catTitle,
        	"courseCode" => $courseCode,
        	"criancas" 	 => $criancas
        );

        $jsonS = array_merge($list,$complementos);
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

		if(isset($_POST['pdiscount'])):
			$cupom = self::getDiscont($_POST['pdiscount']);
			self::setDiscount($cupom,$cartid,$userid,$_POST['pdiscount']);
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

		$count = count(json_decode(self::getCart($cartid,'cartid','products'),true));

		if(isset($_POST['delitem'])):
			self::deleteCart($cartid);
		endif;

		/***********
		 Inserindo Pagamentos.
		**********/
		//if(isset($_POST['formPag']) || isset($_POST['ftransf'])):
			//count($courses->items) < 1 ? self::setCourses($cartid) : self::setCoursesUpdate($userid,$cartid);
		//endif;

		if(isset($_POST['formPag']) || isset($_POST['ftransf'])):
			self::setCourses($cartid);
		endif;

		return true;
	}

	//Desconto Valt
	public static function getDiscont($cupom)
	{
		// Build the query for the table list.
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT discount'
			. ' FROM #__s7dpayments_discount'
			. ' WHERE codigo = ' . $db->quote($cupom)
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

	public static function setDiscount($discount,$cartid,$userId,$discountId)
	{
		if(!$discount)
		{	
			echo '<div class="alert alert-danger cf-alert" role="alert">Voucher inválido!</div>';
			return false;
		}

		//Set Discount Used
		if(self::getDiscontUsed($userId,$discountId,$cartid))
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
			->where($db->quoteName('cartid') . ' = ' . $db->quote($cartid))
			->where($db->quoteName('userid') . ' = ' . $db->quote($userId));
		
		// Set the query and execute the update.
		$db->setQuery($query);
		
		try
		{
			if($db->execute())
			{
				self::setDiscountUsed($userId,$discountId,$cartid);
				return true;
			}
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return true;
	}

	public static function getDiscontUsed($userId,$discountId,$cartId)
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
				if($item->discountId == $discountId)
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
}