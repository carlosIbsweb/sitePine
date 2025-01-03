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

	public function __construct()
	{
		self::executCart();
	}

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

	public static function getCart($cartid,$dadoc,$dado)
	{
		$cdb = JFactory::getDbo();

		$idcart = "'".$cartid."'";

        $cdb->setQuery('SELECT #__s7dpayments_cart.'.$dado.' FROM #__s7dpayments_cart WHERE  '.$dadoc.' = '.$idcart);

        return $cdb->loadResult();
	}

	public static function setCartUpdate($cartid,$userid)
	{	
		$list = $_POST;

        $remov = array
        (
        	'enviar'
        );

        foreach($remov as $k => $i):
        	unset($list[$i]);
        endforeach;

        $capJson = self::getCart($cartid,'cartid','products');

        $aDate = date('d/m/Y H:i');

        //buscando os dados do produto

       foreach(self::getCat($_GET['cat']) as $cat):
			$catTitle = $cat->title;
			$courseCode = $cat->alias;
			foreach(self::getItens($_GET['courseId']) as $items):
				if($cat->id == $items->catid):
					$price = $items->price;
					$title = $items->title;
					$image = $items->image;
					$courseid = $items->id;
					$catid = $items->catid;
				endif;
			endforeach;
		endforeach;


        $complementos = Array
        (
        	"data" => $aDate,
        	"price" => $price,
        	"course" => $title,
        	"img" => $image,
        	"id" => $courseid,
        	"catid" => $catid,
        	"cattitle" => $catTitle,
        	"courseCode" => $courseCode
        );

        $setJson = substr(json_encode($list,JSON_UNESCAPED_UNICODE),0,-2).','.substr(json_encode($complementos,JSON_UNESCAPED_UNICODE),1).'}';

        $json = is_array(json_decode($capJson,true)) ? substr($capJson,0,-1).','.substr($setJson,1) : $setJson;

        if(is_array(json_decode(self::getCart($cartid,'cartid','products'),true))):

        foreach(json_decode(self::getCart($cartid,'cartid','products')) as $k => $items):
				if($items->id == $courseid):
					$cCart = $temid = $items->id;
				endif;
			endforeach;
		endif;


        $db = JFactory::getDbo();

        $query = "UPDATE #__s7dpayments_cart
        SET products = '".$json."', userid = '".$userid."'
        WHERE cartid ='".$cartid."'
        ";
        $db->setQuery($query);


		if(empty($cCart)):
			$db->query();
			//redirecionando para o carrinho
			header('Location: ?cart');
		else:
			echo '<div class="errorLimit">A quantidade máxima permitida para compra é 1.</div>';
		endif;

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

	public static function executCart()
	{
		//cart ------------------------------------------
		$user = JFactory::getUser();
		$userid = $user->id;

		$_SESSION['user'] = $user->id;

		//Destruindo a sessão ltem.
		if(isset($_SESSION['ltem']) && !isset($_GET['cart'])): unset($_SESSION['ltem']); endif;

		isset($_SESSION['cartid']) && time() - $_SESSION['cartTim'] > 3600 ? session_unset() : (isset($_GET['store']) || isset($_GET['cart']) ? $_SESSION['cartTim'] = time() : null);

		if(!isset($_SESSION['cartid'])):
			$_SESSION['cartid'] = uniqid();
			$_SESSION['cartTim'] = time();
		endif;

		//Sessão de id do carrinho
		$cartid = $_SESSION['cartid'];

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

		if(isset($_POST['executcart'])):
			count($courses->items) < 1 ? self::setCourses($cartid) : self::setCoursesUpdate($userid,$cartid);
		endif;

		return false;
	}
}