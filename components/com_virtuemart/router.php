<?php
if(  !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
 *
 * @package VirtueMart
 * @author Kohl Patrick
 * @author Max Milbers
 * @subpackage router
 * @version $Id$
 * @copyright Copyright (C) 2009 - 2020 by the VirtueMart Team and authors
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

if(version_compare(JVERSION,'4.0.0','ge')) {

	/**
	* Routing class from com_contact
	*
	* @since  3.3
	*/
	class VirtuemartRouter extends RouterView {

		/**
		 * Content Component router constructor
		 *
		 * @param   SiteApplication           $app              The application object
		 * @param   AbstractMenu              $menu             The menu object to work with
		 */
		public function __construct(SiteApplication $app, AbstractMenu $menu) {
			parent::__construct($app, $menu);

			$this->attachRule(new MenuRules($this));
			$this->attachRule(new StandardRules($this));
			$this->attachRule(new NomenuRules($this));
		}

		public function parse(&$segments) {
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'router.php');
			$ret = virtuemartParseRoute($segments);
			$segments = array();
			$ret['option'] = 'com_virtuemart';
			$app = JFactory::getApplication();
			foreach ($ret as $key=>$val) {
				$app->input->set($key, $val);
				if (class_exists('JRequest')) {
					JRequest::setVar($key, $val);
				}
				if (class_exists('vRequest')) {
					vRequest::setVar($key, $val);
				}
			}

			return $ret;
		}
		
		public function preprocess($query) {
			if (!isset($query['Itemid'])) {
				$menu = SiteApplication::getInstance('site')->getMenu();

				// Search for all menu items for your component
				$menuItems = $menu->getItems('component', 'com_virtuemart');

				if (!empty($menuItems))
				{
					$shopMenuItemId = 0;
					
					foreach ($menuItems as $menuItem) {
						if (!empty($menuItem->query['option']) && $menuItem->query['option'] === 'com_virtuemart' && !empty($menuItem->query['view']) && $menuItem->query['view'] === 'category' && empty($menuItem->query['virtuemart_category_id']) && empty($menuItem->query['virtuemart_manufacturer_id'])) {
							$shopMenuItemId = $menuItem->id;
							break;
						}
					}
					
					if ($shopMenuItemId > 0)
					{
						$query['Itemid'] = $shopMenuItemId;
					}
				}
			}
			
			return parent::preprocess($query);
		}

		public function build(&$query) {
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'router.php');
			$ret = virtuemartBuildRoute($query);
			return $ret;
		}

	}

}

function virtuemartBuildRoute(&$query) {

	$segments = array();

	$helper = vmrouterHelper::getInstance($query);
	// simple route , no work , for very slow server or test purpose
	if ($helper->router_disabled) {
		foreach ($query as $key => $value){
			if  ($key != 'option')  {
				if ($key != 'Itemid' and $key != 'lang') {
					if(is_array($value)){
							$value = implode(',',$value);
					}
					$segments[]=$key.'/'.$value;
					unset($query[$key]);
				}
			}
		}
		vmrouterHelper::resetLanguage();
		return $segments;
	}

	if ($helper->edit) return $segments;

	$view = '';

	$jmenu = $helper->menu ;
	//vmdebug('virtuemartBuildRoute $jmenu',$helper->query,$helper->activeMenu,$helper->menuVmitems);
	if(isset($query['langswitch'])) unset($query['langswitch']);

	if(isset($query['view'])){
		$view = $query['view'];
		unset($query['view']);
	}

	switch ($view) {
		case 'virtuemart';
			$query['Itemid'] = $jmenu['virtuemart'] ;
			break;
		case 'category';
			$start = null;
			$limitstart = null;
			$limit = null;

			if ( !empty($query['virtuemart_manufacturer_id'])  ) {
				$segments[] = $helper->lang('manufacturer').'/'.$helper->getManufacturerName($query['virtuemart_manufacturer_id']) ;
				//unset($query['virtuemart_manufacturer_id']);
			}

			if ( isset($query['virtuemart_category_id']) or isset($query['virtuemart_manufacturer_id']) ) {
				$categoryRoute = null;
				$catId = empty($query['virtuemart_category_id'])? 0:(int)$query['virtuemart_category_id'];
				$manId = empty($query['virtuemart_manufacturer_id'])? 0:(int)$query['virtuemart_manufacturer_id'];
				if($helper->full or !isset($query['virtuemart_product_id'])){
					$categoryRoute = $helper->getCategoryRoute( $catId, $manId);
					if ($categoryRoute->route) {
						$segments[] = $categoryRoute->route;
					}
				}
				//We should not need that, because it is loaded, when the category is opened
				//if(!empty($catId)) $limit = vmrouterHelper::getLimitByCategory($catId);

				if(isset($jmenu['virtuemart_category_id'][$catId][$manId])) {
					$query['Itemid'] = $jmenu['virtuemart_category_id'][$catId][$manId];
				} else {
					if($categoryRoute===null) $categoryRoute = $helper->getCategoryRoute($catId,$manId);
					//http://forum.virtuemart.net/index.php?topic=121642.0
					if (!empty($categoryRoute->Itemid)) {
						$query['Itemid'] = $categoryRoute->Itemid;
					} else if (!empty($jmenu['virtuemart'])) {
						$query['Itemid'] = $jmenu['virtuemart'];
					}
				}

				unset($query['virtuemart_category_id']);
				unset($query['virtuemart_manufacturer_id']);
			}
			if ( !empty($jmenu['category']) ) $query['Itemid'] = $jmenu['category'];

			/*if ( isset($query['search'])  ) {
				$segments[] = $helper->lang('search') ;
				unset($query['search']);
			}*/
			/*if ( isset($query['keyword'] )) {
				$segments[] = $helper->lang('search').'='.$query['keyword'];
				unset($query['keyword']);
			}*/

			if ( isset($query['orderby']) ) {
				$segments[] = $helper->lang('by').','.$helper->lang( $query['orderby']) ;
				unset($query['orderby']);
			}

			if ( isset($query['dir']) ) {
				if ($query['dir'] =='DESC'){
					$dir = 'dirDesc';
				} else {
					$dir = 'dirAsc';
				}
				$segments[] = $dir;
				unset($query['dir']);
			}

			// Joomla replace before route limitstart by start but without SEF this is start !
			if ( isset($query['limitstart'] ) ) {
				$limitstart = (int)$query['limitstart'] ;
				unset($query['limitstart']);
			}
			if ( isset($query['start'] ) ) {
				$start = (int)$query['start'] ;
				unset($query['start']);
			}
			if ( isset($query['limit'] ) ) {
				$limit = (int)$query['limit'] ;
				unset($query['limit']);
			}

			if ($start !== null &&  $limitstart!== null ) {
				if(vmrouterHelper::$debug) vmdebug('Pagination limits $start !== null &&  $limitstart!== null',$start,$limitstart);

				//$segments[] = $helper->lang('results') .',1-'.$start ;
			} else if ( $start>0 ) {
				//For the urls leading to the paginated pages
				// using general limit if $limit is not set
				if ($limit === null) $limit= vmrouterHelper::$limit ;
				$segments[] = $helper->lang('results') .','. ($start+1).'-'.($start+$limit);
			} else if ($limit !== null && $limit != vmrouterHelper::$limit ) {
				//for the urls of the list where the user sets the pagination size/limit
				$segments[] = $helper->lang('results') .',1-'.$limit ;
			} else if(!empty($query['search']) or !empty($query['keyword'])){
				$segments[] = $helper->lang('results') .',1-'.vmrouterHelper::$limit ;
			}

			break;
		//Shop product details view
		case 'productdetails';

			$virtuemart_product_id = false;
			if (!empty($query['virtuemart_product_id']) and isset($jmenu['virtuemart_product_id']) and isset($jmenu['virtuemart_product_id'][ $query['virtuemart_product_id'] ] ) ) {
				$query['Itemid'] = $jmenu['virtuemart_product_id'][$query['virtuemart_product_id']];
				unset($query['virtuemart_product_id']);
				unset($query['virtuemart_category_id']);
				unset($query['virtuemart_manufacturer_id']);
			} else {
				if(isset($query['virtuemart_product_id'])) {
					if ($helper->use_id) $segments[] = $query['virtuemart_product_id'];
					$virtuemart_product_id = $query['virtuemart_product_id'];
					unset($query['virtuemart_product_id']);
				}
				//vmdebug('vmRouter case \'productdetails\' Itemid',$helper->rItemid,$query['Itemid']);
				//unset($query['Itemid']);
				$Itemid = false;
				if($helper->full){
					if(empty( $query['virtuemart_category_id'])){
						$query['virtuemart_category_id'] = $helper->getParentProductcategory($virtuemart_product_id);
					}
					$catId = empty($query['virtuemart_category_id'])? 0:(int)$query['virtuemart_category_id'];
					$manId = empty($query['virtuemart_manufacturer_id'])? 0:(int)$query['virtuemart_manufacturer_id'];
					//GJC handle $ref
					$ref = empty($query['ref'])? 0:(int)$query['ref'];
					if(!empty( $catId)){
						// GJC here it goes wrong - it ignores the canonical cat
						// GJC fix in setMenuItemId() by choosing the desired url manually in the menu template overide parameter
						$categoryRoute = $helper->getCategoryRoute($catId,$manId,$ref);
						if ($categoryRoute->route) $segments[] = $categoryRoute->route;

						//Maybe the ref should be just handled by the rItemid?
						/*if($helper->useGivenItemid and $helper->rItemid){
							if($helper->checkItemid($helper->rItemid)){
								$Itemid = $helper->rItemid;
							}
						}*/
						if(!$Itemid){
							if ($categoryRoute->Itemid) $Itemid = $categoryRoute->Itemid;
							else $Itemid = $jmenu['virtuemart'];
						}

					} else {
						//$query['Itemid'] = $jmenu['virtuemart']?$jmenu['virtuemart']:@$jmenu['virtuemart_category_id'][0][0];
					}
				} else {
					//Itemid is needed even if seo_full = 0
					//$query['Itemid'] = $jmenu['virtuemart']?$jmenu['virtuemart']:@$jmenu['virtuemart_category_id'][0][0];
				}

				if(empty($Itemid)){
					//vmdebug('vmRouter case \'productdetails\' Itemid not found yet '.$helper->rItemid,$virtuemart_product_id);
					//Itemid is needed even if seo_full = 0
					if(!empty($jmenu['virtuemart'])){
						$Itemid = $jmenu['virtuemart'];
					} else if(!empty($jmenu['virtuemart_category_id'][0]) and !empty($jmenu['virtuemart_category_id'][0][0])){
						$Itemid = $jmenu['virtuemart_category_id'][0][0];
					}
				}

				if(empty($Itemid)){
					if(vmrouterHelper::$debug) vmdebug('vmRouter case \'productdetails\' No Itemid found, Itemid existing in $query?',$query['Itemid']);
				}  else {
					$query['Itemid'] = $Itemid;
				}
				unset($query['start']);
				unset($query['limitstart']);
				unset($query['limit']);
				unset($query['virtuemart_category_id']);
				unset($query['virtuemart_manufacturer_id']);
				//GJC remove ref on canonical
				unset($query['ref']);

				if($virtuemart_product_id)
					$segments[] = $helper->getProductName($virtuemart_product_id);
			}
			break;
		case 'manufacturer';

			if(isset($query['virtuemart_manufacturer_id'])) {
				if (isset($jmenu['virtuemart_manufacturer_id'][ $query['virtuemart_manufacturer_id'] ] ) ) {
					$query['Itemid'] = $jmenu['virtuemart_manufacturer_id'][$query['virtuemart_manufacturer_id']];
				} else {
					$segments[] = $helper->lang('manufacturers').'/'.$helper->getManufacturerName($query['virtuemart_manufacturer_id']) ;
					if ( isset($jmenu['manufacturer']) ) $query['Itemid'] = $jmenu['manufacturer'];
					else $query['Itemid'] = $jmenu['virtuemart'];
				}
				unset($query['virtuemart_manufacturer_id']);
			} else {
				if ( isset($jmenu['manufacturer']) ) $query['Itemid'] = $jmenu['manufacturer'];
				else $query['Itemid'] = $jmenu['virtuemart'];
			}
			break;
		case 'user';
			//vmdebug('virtuemartBuildRoute case user query and jmenu',$query, $jmenu);
			if ( isset($jmenu['user'])) $query['Itemid'] = $jmenu['user'];
			else {
				$segments[] = $helper->lang('user') ;
				$query['Itemid'] = $jmenu['virtuemart'];
			}

			if (isset($query['task'])) {
				//vmdebug('my task in user view',$query['task']);
				if($query['task']=='editaddresscart'){
					if ($query['addrtype'] == 'ST'){
						$segments[] = $helper->lang('editaddresscartST') ;
					} else {
						$segments[] = $helper->lang('editaddresscartBT') ;
					}
				}

				else if($query['task']=='editaddresscheckout'){
					if ($query['addrtype'] == 'ST'){
						$segments[] = $helper->lang('editaddresscheckoutST') ;
					} else {
						$segments[] = $helper->lang('editaddresscheckoutBT') ;
					}
				}

				else if($query['task']=='editaddress'){

					if (isset($query['addrtype']) and $query['addrtype'] == 'ST'){
						$segments[] = $helper->lang('editaddressST') ;
					} else {
						$segments[] = $helper->lang('editaddressBT') ;
					}
				}
				else if($query['task']=='addST'){
					$segments[] = $helper->lang('addST') ;
				}
				else {
					$segments[] =  $helper->lang($query['task']);
				}
				unset ($query['task'] , $query['addrtype']);
			}
			if(JVM_VERSION>3 and isset($jmenu['user'])){
				array_unshift($segments, $helper->lang('user') );
			}
			//vmdebug('Router buildRoute case user query and segments',$query,$segments);
			break;
		case 'vendor';
			/* VM208 */
			if(isset($query['virtuemart_vendor_id'])) {
				if (isset($jmenu['virtuemart_vendor_id'][ $query['virtuemart_vendor_id'] ] ) ) {
					$query['Itemid'] = $jmenu['virtuemart_vendor_id'][$query['virtuemart_vendor_id']];
				} else {
					if ( isset($jmenu['vendor']) ) {
						$query['Itemid'] = $jmenu['vendor'];
					} else {
						$segments[] = $helper->lang('vendor') ;
						$query['Itemid'] = $jmenu['virtuemart'];
					}
				}
			} else if ( isset($jmenu['vendor']) ) {
				$query['Itemid'] = $jmenu['vendor'];
			} else {
				$segments[] = $helper->lang('vendor') ;
				$query['Itemid'] = $jmenu['virtuemart'];
			}
			if (isset($query['virtuemart_vendor_id'])) {
				$segments[] =  $helper->getVendorName($query['virtuemart_vendor_id']) ;
				unset ($query['virtuemart_vendor_id'] );
			}
			if(!empty($query['Itemid'])){
				unset ($query['virtuemart_vendor_id'] );
				//unset ($query['layout']);

			}
			//unset ($query['limitstart']);
			//unset ($query['limit']);
			break;
		case 'cart';

			$layout = (empty( $query['layout'] )) ? 0 : $query['layout'];
			if(isset( $jmenu['cart'][$layout] )) {
				$query['Itemid'] = $jmenu['cart'][$layout];
			} else if ($layout!=0 and isset($jmenu['cart'][0]) ) {
				$query['Itemid'] = $jmenu['cart'][0];
			} else if ( isset($jmenu['virtuemart']) ) {
				$query['Itemid'] = $jmenu['virtuemart'];
				$segments[] = $helper->lang('cart') ;

			} else {
				// the worst
				$segments[] = $helper->lang('cart') ;
			}
			break;
		case 'orders';
			if ( isset($jmenu['orders']) ) $query['Itemid'] = $jmenu['orders'];
			else {
				$segments[] = $helper->lang('orders') ;
				$query['Itemid'] = $jmenu['virtuemart'];
			}
			if ( isset($query['order_number']) ) {
				$segments[] = 'number/'.$query['order_number'];
				unset ($query['order_number'],$query['layout']);
			} else if ( isset($query['virtuemart_order_id']) ) {
				$segments[] = 'id/'.$query['virtuemart_order_id'];
				unset ($query['virtuemart_order_id'],$query['layout']);
			}
			break;

		// sef only view
		default ;
			$segments[] = $view;

		//VmConfig::$vmlang = $oLang;
	}


	if (isset($query['task'])) {
		$segments[] = $helper->lang($query['task']);
		unset($query['task']);
	}
	if (isset($query['layout'])) {
		$segments[] = $helper->lang($query['layout']) ;
		unset($query['layout']);
	}
	vmrouterHelper::resetLanguage();
	return $segments;
}

/* This function can be slower because is used only one time  to find the real URL*/
function virtuemartParseRoute($segments) {

	$vars = array();

	$helper = vmrouterHelper::getInstance();

	//$helper->setActiveMenu();

	if ($helper->router_disabled) {
		$total = count($segments);
		for ($i = 0; $i < $total; $i=$i+2) {
			if(isset($segments[$i+1])){
				if(isset($segments[$i+1]) and strpos($segments[$i+1],',')!==false){
					$vars[ $segments[$i] ] = explode(',',$segments[$i+1]);
				} else {
					$vars[ $segments[$i] ] = $segments[$i+1];
				}
			}
		}
		if(isset($vars[ 'start'])) {
			$vars[ 'limitstart'] = $vars[ 'start'];
		} else {
			$vars[ 'limitstart'] = 0;
		}
		return $vars;
	}

	if (empty($segments)) {
		return $vars;
	}

	foreach  ($segments as &$value) {
		$value = str_replace(':', '-', $value);
	}

	$splitted = explode(',',end($segments),2);

	if ( $helper->compareKey($splitted[0] ,'results')){
		array_pop($segments);
		$results = explode('-',$splitted[1],2);
		//Pagination has changed, removed the -1 note by Max Milbers NOTE: Works on j1.5, but NOT j1.7
		// limitstart is swapped by joomla to start ! See includes/route.php
		if ($start = $results[0]-1) $vars['limitstart'] = $start;
		else $vars['limitstart'] = 0 ;
		$vars['limit'] = (int)$results[1]-$results[0]+1;

	} else {
		$vars['limitstart'] = 0 ;

	}

	if (empty($segments)) {
		$vars['view'] = 'category';
		$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
		if(!isset($vars['limit'])) $vars['limit'] = vmrouterHelper::getLimitByCategory($vars['virtuemart_category_id'],$vars['view']);
		return $vars;
	}

	//Translation of the ordering direction is not really useful and costs just energy
	if ( end($segments) == 'dirDesc' or end($segments) == 'dirAsc' ){
		if ( end($segments) == 'dirDesc' ) {
			$vars['dir'] = 'DESC';
		} else {
			$vars['dir'] ='ASC' ;
		}
		array_pop($segments);
		if (empty($segments)) {
			$vars['view'] = 'category';
			$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
			if(!isset($vars['limit'])) $vars['limit'] = vmrouterHelper::getLimitByCategory($vars['virtuemart_category_id'],$vars['view']);
			return $vars;
		}
	}
	if(vmrouterHelper::$debug) vmdebug('virtuemartParseRoute $segments ',$segments);
	/*$searchText = 'search';
	//if ($this->seo_translate ) {
		$searchText = vmText::_( 'COM_VIRTUEMART_SEF_search' );
	//}

	$searchPre = substr($segments[0],0,strlen($searchText));
	if($searchPre==$searchText){

	//}


	//if ( $helper->compareKey($segments[0] ,'search') ) {
		$vars['search'] = 'true';
		array_shift($segments);
		if ( !empty ($segments) ) {
			$vars['keyword'] = array_shift($segments);
		}
		$vars['view'] = 'category';
		$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
		$vars['limit'] = vmrouterHelper::getLimitByCategory($vars['virtuemart_category_id'],$vars['view']);
		vmdebug('my segments checking for search',$segments,$vars);
		if (empty($segments)) return $vars;
	}*/

	$orderby = explode(',',end($segments),2);
	if ( count($orderby) == 2 and $helper->compareKey($orderby[0] , 'by') ) {
		$vars['orderby'] = $helper->getOrderingKey($orderby[1]) ;
		// array_shift($segments);
		array_pop($segments);

		if (empty($segments)) {
			$vars['view'] = 'category';
			$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
			if(!isset($vars['limit'])) $vars['limit'] = vmrouterHelper::getLimitByCategory($vars['virtuemart_category_id'],$vars['view']);
			return $vars;
		}
	}

	if ( $segments[0] == 'product') {
		$vars['view'] = 'product';
		$vars['task'] = $segments[1];
		$vars['tmpl'] = 'component';
		return $vars;
	}

	if ( $segments[0] == 'checkout' or $segments[0] == 'cart' or $helper->compareKey($segments[0] ,'cart')) {
		$vars['view'] = 'cart';
		if(count($segments) > 1){ // prevent putting value of view variable into task variable by Viktor Jelinek
			$vars['task'] = array_pop($segments);
		}
		return $vars;
	}

	if (  $helper->compareKey($segments[0] ,'manufacturer') ) {
		if(!empty($segments[1])){
			array_shift($segments);
			$vars['virtuemart_manufacturer_id'] =  $helper->getManufacturerId($segments[0]);

		}

		array_shift($segments);
		// OSP 2012-02-29 removed search malforms SEF path and search is performed
		if (empty($segments)) {
			$vars['view'] = 'category';
			$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
			if(empty($vars['limit'])) $vars['limit'] = vmrouterHelper::getLimitByCategory($vars['virtuemart_manufacturer_id'],'manufacturer');
			return $vars;
		}

	}
	/* added in vm208 */
// if no joomla link: vendor/vendorname/layout
// if joomla link joomlalink/vendorname/layout
	if (  $helper->compareKey($segments[0] ,'vendor') ) {
		$vars['virtuemart_vendor_id'] =  $helper->getVendorId($segments[1]);
		// OSP 2012-02-29 removed search malforms SEF path and search is performed
		// $vars['search'] = 'true';
		// this can never happen
		if (empty($segments)) {
			$vars['view'] = 'vendor';
			$vars['virtuemart_vendor_id'] = $helper->activeMenu->virtuemart_vendor_id ;
			return $vars;
		}

	}


	if (end($segments) == 'modal') {
		$vars['tmpl'] = 'component';
		array_pop($segments);

	}
	if ( $helper->compareKey(end($segments) ,'askquestion') ) {
		$vars = (array)$helper->activeMenu ;
		$vars['task'] = 'askquestion';
		array_pop($segments);

	} elseif ( $helper->compareKey(end($segments) ,'recommend') ) {
		$vars = (array)$helper->activeMenu ;
		$vars['task'] = 'recommend';
		array_pop($segments);

	} elseif ( $helper->compareKey(end($segments) ,'notify') ) {
		$vars = (array)$helper->activeMenu ;
		$vars['layout'] = 'notify';
		array_pop($segments);

	}

	if (empty($segments)) return $vars ;

	// View is first segment now
	$view = $segments[0];
	if ( $helper->compareKey($view,'orders') || $helper->activeMenu->view == 'orders') {
		$vars['view'] = 'orders';
		if ( $helper->compareKey($view,'orders')){
			array_shift($segments);
		}
		if (empty($segments)) {
			$vars['layout'] = 'list';
		}
		else if ($helper->compareKey($segments[0],'list') ) {
			$vars['layout'] = 'list';
			array_shift($segments);
		}
		if ( !empty($segments) ) {
			if ($segments[0] =='number')
				$vars['order_number'] = $segments[1] ;
			else $vars['virtuemart_order_id'] = $segments[1] ;
			$vars['layout'] = 'details';
		}
		if(!isset($vars['limit'])){
			$vars['limit'] = vmrouterHelper::$limit;
		}
		return $vars;
	}
	else if ( $helper->compareKey($view,'user') || $helper->activeMenu->view == 'user') {
		$vars['view'] = 'user';
		if ( $helper->compareKey($view,'user') ) {
			array_shift($segments);
		}

		if ( !empty($segments) ) {
			if (  $helper->compareKey($segments[0] ,'editaddresscartBT') ) {
				$vars['addrtype'] = 'BT' ;
				$vars['task'] = 'editaddresscart' ;
			}
			elseif (  $helper->compareKey($segments[0] ,'editaddresscartST') ) {
				$vars['addrtype'] = 'ST' ;
				$vars['task'] = 'editaddresscart' ;
			}
			elseif (  $helper->compareKey($segments[0] ,'editaddresscheckoutBT') ) {
				$vars['addrtype'] = 'BT' ;
				$vars['task'] = 'editaddresscheckout' ;
			}
			elseif (  $helper->compareKey($segments[0] ,'editaddresscheckoutST') ) {
				$vars['addrtype'] = 'ST' ;
				$vars['task'] = 'editaddresscheckout' ;
			}
			elseif (  $helper->compareKey($segments[0] ,'editaddressST') ) {
				$vars['addrtype'] = 'ST' ;
				$vars['task'] = 'editaddressST' ;
			}
			elseif (  $helper->compareKey($segments[0] ,'editaddressBT') ) {
				$vars['addrtype'] = 'BT' ;
				$vars['task'] = 'edit' ;
				$vars['layout'] = 'edit' ;      //I think that should be the layout, not the task
			}
			elseif (  $helper->compareKey($segments[0] ,'edit') ) {
				$vars['layout'] = 'edit' ;      //uncomment and lets test
			}
			elseif (  $helper->compareKey($segments[0] ,'pluginresponse') ) {
				$vars['view'] = 'pluginresponse' ;
				if(isset($segments[1]))
					$vars['task'] = $segments[1] ;
			}

			else $vars['task'] = $segments[0] ;
		}
		if(!isset($vars['limit'])){
			$vars['limit'] = vmrouterHelper::$limit;
		}
		return $vars;
	}
	else if ( $helper->compareKey($view,'vendor') || $helper->activeMenu->view == 'vendor') {
		$vars['view'] = 'vendor';

		if ( $helper->compareKey($view,'vendor') ) {
			array_shift($segments);
			if (empty($segments)) return $vars;
		}

		$vars['virtuemart_vendor_id'] =  $helper->getVendorId($segments[0]);
		array_shift($segments);
		if(!empty($segments)) {
			if ( $helper->compareKey($segments[0] ,'contact') ) $vars['layout'] = 'contact' ;
			elseif ( $helper->compareKey($segments[0] ,'tos') ) $vars['layout'] = 'tos' ;
			elseif ( $helper->compareKey($segments[0] ,'details') ) $vars['layout'] = 'details' ;
		} else $vars['layout'] = 'details' ;

		if(!isset($vars['limit'])){
			$vars['limit'] = vmrouterHelper::$limit;
		}
		return $vars;

	}
	elseif ( $helper->compareKey($segments[0] ,'pluginresponse') ) {
		$vars['view'] = 'pluginresponse';
		array_shift($segments);
		if ( !empty ($segments) ) {
			$vars['task'] = $segments[0];
			array_shift($segments);
		}
		if ( isset($segments[0]) && $segments[0] == 'modal') {
			$vars['tmpl'] = 'component';
			array_shift($segments);
		}
		return $vars;
	}
	else if ( $helper->compareKey($view,'cart') || $helper->activeMenu->view == 'cart') {
		$vars['view'] = 'cart';
		if ( $helper->compareKey($view,'cart') ) {
			array_shift($segments);
			if (empty($segments)) return $vars;
		}
		if ( $helper->compareKey($segments[0] ,'edit_shipment') ) $vars['task'] = 'edit_shipment' ;
		elseif ( $helper->compareKey($segments[0] ,'editpayment') ) $vars['task'] = 'editpayment' ;
		elseif ( $helper->compareKey($segments[0] ,'delete') ) $vars['task'] = 'delete' ;
		elseif ( $helper->compareKey($segments[0] ,'checkout') ) $vars['task'] = 'checkout' ;
		elseif ( $helper->compareKey($segments[0] ,'orderdone') ) $vars['layout'] = 'orderdone' ;
		else $vars['task'] = $segments[0];
		return $vars;
	}

	else if ( $helper->compareKey($view,'manufacturers') || $helper->activeMenu->view == 'manufacturer') {
		$vars['view'] = 'manufacturer';

		if ( $helper->compareKey($view,'manufacturers') ) {
			array_shift($segments);
		}

		if (!empty($segments) ) {
			$vars['virtuemart_manufacturer_id'] =  $helper->getManufacturerId($segments[0]);
			array_shift($segments);
		}
		if ( isset($segments[0]) && $segments[0] == 'modal') {
			$vars['tmpl'] = 'component';
			array_shift($segments);
		}

		if(!isset($vars['limit'])){
			$vars['limit'] = vmrouterHelper::$limit;
		}
		return $vars;
	}


	/*
	 * seo_sufix must never be used in category or router can't find it
	 * eg. suffix as "-suffix", a category with "name-suffix" get always a false return
	 * Trick : YOu can simply use "-p","-x","-" or ".htm" for better seo result if it's never in the product/category name !
	 */
	$last_elem = end($segments);
	$slast_elem = prev($segments);
	if(vmrouterHelper::$debug) vmdebug('ParseRoute no view found yet',$segments, $vars,$last_elem,$slast_elem);
	if ( !empty($helper->seo_sufix_size) and ((substr($last_elem, -(int)$helper->seo_sufix_size ) == $helper->seo_sufix)
	|| ($last_elem=='notify' && substr($slast_elem, -(int)$helper->seo_sufix_size ) == $helper->seo_sufix)) ) {

		$vars['view'] = 'productdetails';
		if($last_elem == 'notify') {
			$vars['layout'] = 'notify';
			array_pop( $segments );
		}

		if(!$helper->use_id) {
			$product = $helper->getProductId( $segments, $helper->activeMenu->virtuemart_category_id,true );
			$vars['virtuemart_product_id'] = $product['virtuemart_product_id'];
			$vars['virtuemart_category_id'] = $product['virtuemart_category_id'];
			if(vmrouterHelper::$debug) vmdebug('View productdetails, using case !$helper->use_id',$vars,$product,$helper->activeMenu);
		/*} elseif(isset($segments[1])) {
			$vars['virtuemart_product_id'] = $segments[0];
			$vars['virtuemart_category_id'] = $segments[1];
			vmdebug('View productdetails, using case isset($segments[1]',$vars);*/
		} else {
			if(!empty($segments[0]) and ctype_digit($segments[0]) ){
				$pInt = $segments[0];
			} else if(isset($slast_elem) and ctype_digit($slast_elem)) {
				$pInt = $slast_elem;
			}

			$vars['virtuemart_product_id'] = $pInt;
			if(!empty($helper->activeMenu->virtuemart_category_id)){
				$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id;
			} else {
				$product = VmModel::getModel('product')->getProduct($pInt);
				if($product->canonCatId){
					$vars['virtuemart_category_id'] = $product->canonCatId;
				}
			}
			if(vmrouterHelper::$debug) vmdebug('View productdetails, using case "else", which uses $helper->activeMenu->virtuemart_category_id ',$vars);
		}
	}

	if(!isset($vars['virtuemart_product_id'])) {

		//$vars['view'] = 'productdetails';	//Must be commmented, because else we cannot call custom views per extended plugin
		if($last_elem=='notify') {
			$vars['layout'] = 'notify';
			array_pop($segments);
		}
		$product = $helper->getProductId($segments ,$helper->activeMenu->virtuemart_category_id, true);

		//codepyro - removed suffix from router
		//check if name is a product.
		//if so then its a product load the details page
		if(!empty($product['virtuemart_product_id'])) {
			$vars['view'] = 'productdetails';
			$vars['virtuemart_product_id'] = $product['virtuemart_product_id'];
			if(isset($product['virtuemart_category_id'])) {
				$vars['virtuemart_category_id'] = $product['virtuemart_category_id'];
			}
		} else {
			$catId = $helper->getCategoryId ($last_elem ,$helper->activeMenu->virtuemart_category_id);
			if($catId!=false){
				$vars['virtuemart_category_id'] = $catId;
				$vars['view'] = 'category' ;
				if(!isset($vars['limit'])) $vars['limit'] = vmrouterHelper::getLimitByCategory($vars['virtuemart_category_id'],$vars['view']);
			}
		}
	}

	if (!isset($vars['virtuemart_category_id'])){
		if(vmrouterHelper::$debug) vmdebug('ParseRoute $vars[\'virtuemart_category_id\'] not set',$segments,$helper->activeMenu);
		if (!$helper->use_id && ($helper->activeMenu->view == 'category' ) )  {
			$vars['virtuemart_category_id'] = $helper->getCategoryId (end($segments) ,$helper->activeMenu->virtuemart_category_id);
			$vars['view'] = 'category' ;

		} elseif (isset($segments[0]) && ctype_digit ($segments[0]) || $helper->activeMenu->virtuemart_category_id>0 ) {
			$vars['virtuemart_category_id'] = $segments[0];
			$vars['view'] = 'category';

		} elseif ($helper->activeMenu->virtuemart_category_id >0 && $vars['view'] != 'productdetails') {
			$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
			$vars['view'] = 'category';

		} elseif ($id = $helper->getCategoryId (end($segments) ,$helper->activeMenu->virtuemart_category_id )) {

			// find corresponding category . If not, segment 0 must be a view
			$vars['virtuemart_category_id'] = $id;
			$vars['view'] = 'category' ;
		}
		if(!isset($vars['virtuemart_category_id'])) {
			$vars['error'] = '404';
			$vars['virtuemart_category_id'] = -2;
		}
		if(empty($vars['view'])) $vars['view'] = 'category';

		if(!isset($vars['limit'])) $vars['limit'] = vmrouterHelper::getLimitByCategory($vars['virtuemart_category_id'],$vars['view']);
	}
	if (!isset($vars['view'])){
		$vars['view'] = $segments[0] ;
		if ( isset($segments[1]) ) {
			$vars['task'] = $segments[1] ;
		}
	}

	if(vmrouterHelper::$debug) vmdebug('my vars from router',$vars);
	return $vars;
}

class vmrouterHelper {

	public static $debug = false;
	public $slang = '';
	public $query = array();

	static $andAccess = null;
	static $authStr = null;

	/* Joomla menus ID object from com_virtuemart */
	public $menu = null ;

	/* Joomla active menu( Itemid ) object */
	public $activeMenu = null ;

	/*
	  * $use_id type boolean
	  * Use the Id's of categorie and product or not
	  */
	public $use_id = false ;

	public $seo_translate = false ;
	public $Itemid = '';
	private $orderings = null ;
	public static $limit = null ;

	public $router_disabled = false ;

	private static $_instance = false;

	private static $_catRoute = array ();
	public $byMenu = 0;
	public $template = 0;
	public $CategoryName = array();
	private $dbview = array('vendor' =>'vendor','category' =>'category','virtuemart' =>'virtuemart','productdetails' =>'product','cart' => 'cart','manufacturer' => 'manufacturer','user'=>'user');

	private function __construct($query) {

		$this->template = JFactory::getApplication()->getTemplate(true);
		if(empty($this->template) or !isset($this->template->id)){
			$this->template->id = 0;
		}

		if (!$this->router_disabled = VmConfig::get('seo_disabled', false)) {

			$this->_db = JFactory::getDbo();
			$this->seo_translate = VmConfig::get('seo_translate', false);

			//if ( $this->seo_translate ) {
			vmLanguage::loadJLang('com_virtuemart.sef',true);
			/*} else {
				$this->Jlang = vmLanguage::getLanguage();
			}*/

			$this->byMenu =  (int)VmConfig::get('router_by_menu', 0);
			$this->seo_sufix = '';
			$this->seo_sufix_size = 0;

			$this->use_id = VmConfig::get('seo_use_id', false);
			$this->use_seo_suffix = VmConfig::get('use_seo_suffix', true);
			$this->seo_sufix = VmConfig::get('seo_sufix', '-detail');
			$this->seo_sufix_size = strlen($this->seo_sufix) ;


			$this->full = VmConfig::get('seo_full',true);
			$this->useGivenItemid = 0;//VmConfig::get('useGivenItemid',false);

			$this->edit = ('edit' == vRequest::getCmd('task') or vRequest::getInt('manage')=='1');

			/*$this->langFback = vmLanguage::getUseLangFallback();
			$this->slang = VmLanguage::$currLangTag;
			$this->setMenuItemId();
			vmdebug('New Router instance with language '.VmLanguage::$currLangTag);*/
			$this->slang = VmLanguage::$currLangTag;

			if(self::$andAccess === null){
				$user = JFactory::getUser();
				$auth = array_unique($user->getAuthorisedViewLevels());
				self::$andAccess = ' AND client_id=0 AND published=1 AND ( access="' . implode ('" OR access="', $auth) . '" ) ';
				self::$authStr = implode('.',$auth);
			}

			$this->setActiveMenu();

			$this->setRoutingQuery($query);
			//vmdebug('Router initialised with language '.$this->slang);

			self::$debug = VmConfig::get('debug_enable_router',0);
			if(self::$debug){
				VmConfig::$_debug = true;
			}

		}
	}

	public function setRoutingQuery($query){

		if(!empty($query['Itemid'])){
			$this->Itemid = $query['Itemid'];
		}

		// if language switcher we must know the $query
		$this->query = $query;

		$this->langFback = vmLanguage::getUseLangFallback(true);

		$this->setMenuItemId();

		if(!$this->Itemid){

			$this->Itemid = $this->menu['virtuemart'];
			//vmTrace('setRoutingQuery');
			//vmdebug('my router',$this);
			if(vmrouterHelper::$debug) vmdebug('There is no requested itemid set home Itemid',$this->Itemid);
		}
		if(!$this->Itemid) {
			if(vmrouterHelper::$debug) vmdebug( 'There is still no itemid' );
			$this->Itemid = '';
		}

		//vmdebug('setRoutingQuery executed with language '.$this->slang, $query);
	}

	public static function getInstance(&$query = null) {

		static $lConf = true;
		if($lConf){

			if (!class_exists( 'VmConfig' ) or !class_exists('VmLanguage') or !isset(VmLanguage::$currLangTag)) {
				if (!class_exists( 'VmConfig' )){
					require(JPATH_ROOT .'/administrator/components/com_virtuemart/helpers/config.php');
				}

				VmConfig::loadConfig(FALSE,FALSE,true,false);    // this is needed in case VmConfig was not yet loaded before
				//vmdebug('Router Instance, loaded current Lang Tag in config ',VmLanguage::$currLangTag, VmConfig::$vmlang);
			}

			$lConf = false;
		}

		if(isset($query['lang'])){
			$lang_code = vmrouterHelper::getLanguageTagBySefTag($query['lang']); // by default it returns a full language tag such as nl-NL
		} else {
			$lang_code = JFactory::getApplication()->input->get('language', null);  //this is set by languageFilterPlugin
		}
		//vmdebug('called get Router instance',VmLanguage::$currLangTag,$lang_code);
		if (empty($lang_code) or VmLanguage::$currLangTag!=$lang_code) {
			//vmdebug('Router language switch from '.VmLanguage::$currLangTag.' to '.$lang_code);
			vmLanguage::setLanguageByTag($lang_code, false, false); //this is needed if VmConfig was called in incompatible context and thus current VmConfig::$vmlang IS INCORRECT
			vmLanguage::loadJLang('com_virtuemart.sef',true);

			//vmdebug('Router language switchED TO '.VmConfig::$vmlangTag.VmConfig::$vmlangTag);
		}//*/

		if (!self::$_instance){

			self::$_instance = new vmrouterHelper ($query);

			if (self::$limit===null){
				$app = JFactory::getApplication();
				$view = 'category';
				if(isset($query['view'])) $view = $query['view'];

				//We need to set the default here.
				self::$limit = $app->getUserStateFromRequest('com_virtuemart.'.$view.'.limit', 'limit', VmConfig::get('llimit_init_FE', 24), 'int');
			}

		} else {

			if(self::$_instance->slang != VmLanguage::$currLangTag or (self::$_instance->byMenu and $query['Itemid']!=self::$_instance->Itemid)){
				//vmdebug('Execute setRoutingQuery because, ',self::$_instance->slang,VmLanguage::$currLangTag,$query['Itemid'],self::$_instance->Itemid);
				self::$_instance->slang = VmLanguage::$currLangTag;
				self::$_instance->setRoutingQuery($query);
			}

		}


		return self::$_instance;
	}



	public static function getLimitByCategory($catId, $view = 'category'){

		static $c = array();

		if(empty($c[$catId][$view])){

			$initial = VmConfig::get('llimit_init_FE', 24);
			if($view!='manufacturer'){	//Take care, this could be the categor view, just displaying manufacturer products
				$catModel = VmModel::getModel('category');
				$cat = $catModel->getCategory($catId);
				if(!empty($cat->limit_list_initial)){
					$initial = $cat->limit_list_initial;
					if(vmrouterHelper::$debug) vmdebug('limit by category '.$view.' '.$catId.' '.$cat->limit_list_initial);
				}
			}

			$app = JFactory::getApplication();
			$c[$catId][$view] = $app->getUserStateFromRequest('com_virtuemart.category.limit', 'limit',$initial, 'int');
		}
		self::$limit = $c[$catId][$view];

		return self::$limit;
	}

	public function getCategoryRoute($catId,$manId,$ref=0){

		vmSetStartTime('getCategoryRoute');

		$key = $catId. VmConfig::$vmlang . $manId.'r'.$ref; // internal cache key
		if (!isset(self::$_catRoute[$key])){

			if(VmConfig::get('useCacheVmGetCategoryRoute',1)) {
				//vmdebug('getCategoryRoute key '.$key.' not in internal Cache', self::$_catRoute);
				$cache = VmConfig::getCache('com_virtuemart_cats_route','');
				self::$_catRoute = $cache->get('com_virtuemart_cats_route');
				if(isset(self::$_catRoute[$key])){
					$CategoryRoute = self::$_catRoute[$key];
				} else {

					$CategoryRoute = $this->getCategoryRouteNocache($catId,$manId,$ref);
					//vmdebug('getCategoryRoute store outdated cache', $key, self::$_catRoute);
					$cache->store(self::$_catRoute, 'com_virtuemart_cats_route');
				}

			} else {
				$CategoryRoute = $this->getCategoryRouteNocache($catId,$manId,$ref);
			}

		} else {
			$CategoryRoute = self::$_catRoute[$key];
		}

		//vmTime('getCategoryRoute time','getCategoryRoute', false);
		return $CategoryRoute ;
	}

	/* Get Joomla menu item and the route for category */
	public function getCategoryRouteNocache($catId,$manId,$ref){

		$key = $catId. VmConfig::$vmlang . $manId.'r'.$ref;
		if (!isset(self::$_catRoute[$key])){
			if(!$ref){ // not a canonical request
				$category = new stdClass();
				$category->route = '';
				$category->Itemid = 0;
				$menuCatid = 0 ;
				$ismenu = false ;
				$catModel = VmModel::getModel('category');
				// control if category is joomla menu

				if(isset($this->menu['virtuemart_category_id'][$catId][$manId])) {
					$ismenu = true;
					$category->Itemid = $this->menu['virtuemart_category_id'][$catId][$manId];
				} else if (isset($this->menu['virtuemart_category_id'])) {
					if (isset( $this->menu['virtuemart_category_id'][$catId][$manId])) {
						$ismenu = true;
						$category->Itemid = $this->menu['virtuemart_category_id'][$catId][$manId] ;
					} else {
						$catModel->categoryRecursed = 0;
						$CatParentIds = $catModel->getCategoryRecurse($catId,0) ;
						/* control if parent categories are joomla menu */
						foreach ($CatParentIds as $CatParentId) {
							// No ? then find the parent menu categorie !
							if (isset( $this->menu['virtuemart_category_id'][$CatParentId][$manId]) ) {
								$category->Itemid = $this->menu['virtuemart_category_id'][$CatParentId][$manId] ;
								$menuCatid = $CatParentId;
								break;
							}
						}
					}
				}

				if ($ismenu==false) {
					if ( $this->use_id ) $category->route = $catId.'/';
					if (!isset ($this->CategoryName[$this->slang][$catId])) {
						$this->CategoryName[$this->slang][$catId] = $this->getCategoryNames($catId, $menuCatid );
					}
					$category->route .= $this->CategoryName[$this->slang][$catId] ;
					if ($menuCatid == 0  && $this->menu['virtuemart']) $category->Itemid = $this->menu['virtuemart'] ;
				}
				self::$_catRoute[$key] = $category;

			} else { //GJC there is $ref so canonical query
				$category = new stdClass();
				$category->route = '';
				$category->Itemid = 0;
				$menuCatid = 0;
				$ismenu = false;
				$catModel = VmModel::getModel('category');
				// control if category is joomla menu
				if (isset($this->menu['virtuemart_category_id'][$catId][$manId])) {
					$ismenu = true;
					$category->Itemid = $this->menu['virtuemart_category_id'][$catId][$manId];
				} else if (isset($this->menu['virtuemart_category_id'])) {
					if (isset($this->menu['virtuemart_category_id'][$catId][$manId])) {
						$ismenu = true;
						$category->Itemid = $this->menu['virtuemart_category_id'][$catId][$manId];
					} else {
						$catModel->categoryRecursed = 0;
						$CatParentIds = $catModel->getCategoryRecurse($catId, 0);
						/* control if parent categories are joomla menu */
						foreach ($CatParentIds as $CatParentId) {
							// No ? then find the parent menu categorie !
							if (isset($this->menu['virtuemart_category_id'][$CatParentId][$manId])) {
								$category->Itemid = $this->menu['virtuemart_category_id'][$CatParentId][$manId];
								$menuCatid = $CatParentId;
								break;
							}
						}
					}
				}
				if ($ismenu == false) {
					if ($this->use_id) $category->route = $catId . '/';
					if (!isset ($this->CategoryName[$this->slang][$catId])) {
						$this->CategoryName[$this->slang][$catId] = $this->getCategoryNames($catId, $menuCatid);
					}
					$category->route .= $this->CategoryName[$this->slang][$catId];
					if ($menuCatid == 0 && $this->menu['virtuemart']) $category->Itemid = $this->menu['virtuemart'];
				}
				self::$_catRoute[$key] = $category;
			}
		}

		return self::$_catRoute[$key] ;
	}

	/*get url safe names of category and parents categories  */
	public function getCategoryNames($catId,$catMenuId=0){

		static $categoryNamesCache = array();
		$strings = array();

		$catModel = VmModel::getModel('category');

		if($this->full) {
			$catModel->categoryRecursed = 0;
			if($parent_ids = $catModel->getCategoryRecurse($catId,$catMenuId)){

				$parent_ids = array_reverse($parent_ids) ;
			}
		} else {
			$parent_ids[] = $catId;
		}

		//vmdebug('Router getCategoryNames getCategoryRecurse finished '.$catId,$this->slang,$parent_ids);
		foreach ($parent_ids as $id ) {
			if(!isset($categoryNamesCache[$this->slang][$id])){

				$cat = $catModel->getCategory($id,0);

				if(!empty($cat->published)){
					$categoryNamesCache[$this->slang][$id] = $cat->slug;
					$strings[] = $cat->slug;

				} else if(!empty($id)){
					//vmdebug('router.php getCategoryNames set 404 for id '.$id,$cat);
					//$categoryNamesCache[$this->slang][$id] = '404';
					//$strings[] = '404';
				}
			} else {
				$strings[] = $categoryNamesCache[$this->slang][$id];
			}
		}

		if(function_exists('mb_strtolower')){
			return mb_strtolower(implode ('/', $strings ) );
		} else {
			return strtolower(implode ('/', $strings ) );
		}
	}

	/** return id of categories
	 * $names are segments
	 * $virtuemart_category_ids is joomla menu virtuemart_category_id
	 */
	public function getCategoryId($slug,$catId ){

		$catIds = $this->getFieldOfObjectWithLangFallBack('#__virtuemart_categories_','virtuemart_category_id','virtuemart_category_id','slug',$slug);
		if (!$catIds) {
			$catIds = $catId;
		}

		return $catIds;
	}

	static $productNamesCache = array();
	/* Get URL safe Product name */
	public function getProductName($id){


		static $suffix = '';
		static $prTable = false;
		if(!isset(self::$productNamesCache[$this->slang][$id])){
			if($this->use_seo_suffix){
				$suffix = $this->seo_sufix;
			}
			if(!$prTable){
				$prTable = VmTable::getInstance('products');
			}
			$i = 0;
			//vmSetStartTime('Routerloads');
			if(!isset(self::$productNamesCache[$this->slang][$id])){
				$prTable->_langTag = VmConfig::$vmlang;
				$prTable->load($id);
//vmdebug('getProductName '.$this->slang, $prTable->_langTag,VmConfig::$vmlang,$prTable->slug);
				//a product cannot derive a slug from a parent product
				//if(empty($prTable->slug) and $prTable->product_parent_id>0 ){}

				if(!$prTable or empty($prTable->slug)){
					self::$productNamesCache[$this->slang][$id] = false;
				} else {
					self::$productNamesCache[$this->slang][$id] = $prTable->slug.$suffix;
				}
			}

			//*/

			/*$virtuemart_shoppergroup_ids = VirtueMartModelProduct::getCurrentUserShopperGrps();
			$checkedProductKey= VirtueMartModelProduct::checkIfCached($id,TRUE, FALSE, TRUE, 1, $virtuemart_shoppergroup_ids,0);
			if($checkedProductKey[0]){
				if(VirtueMartModelProduct::$_products[$checkedProductKey[1]]===false){
					self::$productNamesCache[$this->slang][$id] = false;
				} else if(isset(VirtueMartModelProduct::$_products[$checkedProductKey[1]])){
					self::$productNamesCache[$this->slang][$id] = VirtueMartModelProduct::$_products[$checkedProductKey[1]]->slug.$suffix;
				}
			}

			if(!isset(self::$productNamesCache[$this->slang][$id])){
				$pModel = VmModel::getModel('product');
				//Adding shoppergroup could be needed
				$pr = $pModel->getProduct($id, TRUE, FALSE, TRUE, 1, $virtuemart_shoppergroup_ids,0);
				if(!$pr or empty($pr->slug)){
					self::$productNamesCache[$this->slang][$id] = false;
				} else {
					self::$productNamesCache[$this->slang][$id] = $pr->slug.$suffix;
				}
			}//*/
			//vmTime('Router load  '.$id,'Routerloads');
		}

		return self::$productNamesCache[$this->slang][$id];
	}

	var $counter = 0;
	/* Get parent Product first found category ID */
	public function getParentProductcategory($id){

		static $parProdCat= array();
		static $catPar = array();
		if(!isset($parProdCat[$id])){
			if(!class_exists('VirtueMartModelProduct')) VmModel::getModel('product');
			$parent_id = VirtueMartModelProduct::getProductParentId($id);

			//If product is child then get parent category ID
			if ($parent_id and $parent_id!=$id) {

				if(!isset($catPar[$parent_id])){

					$checkedProductKey= VirtueMartModelProduct::checkIfCached($parent_id);

					if($checkedProductKey[0]){
						if(VirtueMartModelProduct::$_products[$checkedProductKey[1]]===false){
							//$parentCache[$product_id] = false;
						} else if(isset(VirtueMartModelProduct::$_products[$checkedProductKey[1]]->virtuemart_category_id)){
							$parProdCat[$id] = $catPar[$parent_id] = VirtueMartModelProduct::$_products[$checkedProductKey[1]]->virtuemart_category_id;
						}
					} else {

						$ids = VirtueMartModelProduct::getProductCategoryIds($parent_id);
						if(isset($ids[0])){
							$parProdCat[$id] = $catPar[$parent_id] = $ids[0]['virtuemart_category_id'];
						} else {
							$parProdCat[$id] = $catPar[$parent_id] = false;
						}
						//->loadResult();
						//vmdebug('Router getParentProductcategory executed sql for '.$id, $parProdCat[$id]);
					}

				} else {
					$parProdCat[$id] = $catPar[$parent_id];
					//vmdebug('getParentProductcategory $catPar[$parent_id] Cached ',$id );
				}

				//When the child and parent id is the same, this creates a deadlock
				//add $counter, dont allow more then 10 levels
				if (!isset($parProdCat[$id]) or !$parProdCat[$id]){
					$this->counter++;
					if($this->counter<10){
						$this->getParentProductcategory($parent_id) ;
					}
				}
			} else {
				$parProdCat[$id] = false;
			}

			$this->counter = 0;
		}

		if(!isset($parProdCat[$id])) $parProdCat[$id] = 0;
		return $parProdCat[$id] ;
	}


	/* get product and category ID */
	public function getProductId($names,$catId = NULL, $seo_sufix = true ){
		$productName = array_pop($names);
		if($this->use_seo_suffix and !empty($this->seo_sufix_size) ){
			if(substr($productName, -(int)$this->seo_sufix_size ) !== $this->seo_sufix) {
				return array('virtuemart_product_id' =>0, 'virtuemart_category_id' => false);
			}
			$productName =  substr($productName, 0, -(int)$this->seo_sufix_size );
		}

		static $prodIds = array();
		$categoryName = array_pop($names);

		$hash = base64_encode($productName.VmConfig::$vmlang);

		if(!isset($prodIds[$hash])){
			$prodIds[$hash]['virtuemart_product_id'] = $this->getFieldOfObjectWithLangFallBack('#__virtuemart_products_', 'virtuemart_product_id', 'virtuemart_product_id', 'slug', $productName);
			if(empty($categoryName) and empty($catId)){
				$prodIds[$hash]['virtuemart_category_id'] = false;
			} else if(!empty($categoryName)){
				$prodIds[$hash]['virtuemart_category_id'] = $this->getCategoryId($categoryName,$catId ) ;
			} else {
				$prodIds[$hash]['virtuemart_category_id'] = false;
			}
		}

		return $prodIds[$hash] ;
	}

	/* Get URL safe Manufacturer name */
	public function getManufacturerName($manId ){

		return $this->getFieldOfObjectWithLangFallBack('#__virtuemart_manufacturers_','virtuemart_manufacturer_id','slug','virtuemart_manufacturer_id',(int)$manId);
	}

	/* Get Manufacturer id */
	public function getManufacturerId($slug ){

		return $this->getFieldOfObjectWithLangFallBack('#__virtuemart_manufacturers_','virtuemart_manufacturer_id','virtuemart_manufacturer_id','slug',$slug);
	}
	/* Get URL safe Manufacturer name */
	public function getVendorName($virtuemart_vendor_id ){

		return $this->getFieldOfObjectWithLangFallBack('#__virtuemart_vendors_','virtuemart_vendor_id','slug','virtuemart_vendor_id',(int)$virtuemart_vendor_id);
	}
	/* Get Manufacturer id */
	public function getVendorId($slug ){

		return $this->getFieldOfObjectWithLangFallBack('#__virtuemart_vendors_','virtuemart_vendor_id','virtuemart_vendor_id','slug',$slug);
	}

	public function getFieldOfObjectWithLangFallBack($table, $idname, $name, $wherename, $value){

		static $ids = array();
		$value = trim($value);
		$hash = substr($table,14,-1).$this->slang.$wherename.$value;
		if(isset($ids[$hash])){
			//vmdebug('getFieldOfObjectWithLangFallBack return cached',$hash);
			return $ids[$hash];
		}

		//It is useless to search for an entry with empty where value.
		if(empty($value)) return false;

		$select = implode(', ',VmModel::joinLangSelectFields(array($name), true));
		$joins = implode(' ',VmModel::joinLangTables(substr($table,0,-1),'i',$idname,'FROM'));
		$wherenames = implode(', ',VmModel::joinLangSelectFields(array($wherename), false));

		$q = 'SELECT '.$select.' '.$joins.' WHERE '.$wherenames.' = "'.$this->_db->escape($value).'"';
		$useFb = vmLanguage::getUseLangFallback();
		if(($useFb)){
			$q .= ' OR ld.'.$wherename.' = "'.$this->_db->escape($value).'"';
		}
		$useFb2 = vmLanguage::getUseLangFallbackSecondary();
		if(($useFb2)){
			$q .= ' OR ljd.'.$wherename.' = "'.$this->_db->escape($value).'"';
		}
		$this->_db->setQuery($q);
		try{
			$ids[$hash] = $this->_db->loadResult();
		} catch (Exception $e){
			vmError('Error in slq router.php function getFieldOfObjectWithLangFallBack '.$e->getMessage());
		}

		if($ids[$hash]===null){
			$ids[$hash] = false;
		}
		//vmdebug('getFieldOfObjectWithLangFallBack my query ',str_replace('#__',$this->_db->getPrefix(),$this->_db->getQuery()),$ids[$hash]);
		return $ids[$hash];
	}

	/**
	 * Checks Itemid if it is a vm itemid and allowed to visit
	 * @return bool
	 */
	public function checkItemid($id){

		static $res = array();
		if(isset($res[$id])) {
			return $res[$id];
		} else {

			$q = 'SELECT * FROM `#__menu` WHERE `link` like "index.php?option=com_virtuemart%" and (language="*" or language = "'.vmLanguage::$jSelLangTag.'" )'.self::$andAccess;

			$q .= ' and `id` = "'.(int)$id.'" ';

			$q .= ' ORDER BY `language` DESC';

			$db			= JFactory::getDBO();
			$db->setQuery($q);
			$r = $db->loadResult();
			$res[$id] = boolval($r);
		}

		if(vmrouterHelper::$debug) vmdebug('checkItemid query and result ', $q, $res);
		return $res[$id];
	}

	/* Set $this->menu with the Item ID from Joomla Menus */
	private function setMenuItemId(){

		$home 	= false ;
		static $mCache = array();

		$jLangTag = $this->slang;




		$h = $jLangTag.self::$authStr;
		if($this->byMenu){
			$h .= 'i'.$this->Itemid;
		}

		if(isset($mCache[$h])){
			$this->menu = $mCache[$h];
			//vmdebug('Found cached menu',$h.$this->Itemid);
			return;
		} else {
			//vmdebug('Existing cache',$h.$this->Itemid,$mCache);
		}


		$db			= JFactory::getDBO();

		$q = 'SELECT * FROM `#__menu` WHERE `link` like "index.php?option=com_virtuemart%" and (language="*" or language = "'.$jLangTag.'" ) '.self::$andAccess;

		if($this->byMenu === 1 and !empty($this->Itemid)) {
			$q .= ' and `menutype` = (SELECT `menutype` FROM `#__menu` WHERE `id` = "'.$this->Itemid.'") ';
		}
		$q .= ' ORDER BY `language` DESC';
		$db->setQuery($q);
		$menuVmitems = $db->loadObjectList();
		//vmdebug('setMenuItemId $q',$q);
		$homeid =0;

		$this->menu = array();
		if(empty($menuVmitems)){
			$mCache[$h] = false;
			if(vmrouterHelper::$debug) vmdebug('my $menuVmitems ',$q,$menuVmitems);
			vmLanguage::loadJLang('com_virtuemart', true);
			vmWarn(vmText::_('COM_VIRTUEMART_ASSIGN_VM_TO_MENU'));
		} else {
			//vmdebug('my menuVmItems',$this->template,$menuVmitems);
			// Search  Virtuemart itemID in joomla menu
			foreach ($menuVmitems as $item)	{

				$linkToSplit= explode ('&',$item->link);

				$link =array();
				foreach ($linkToSplit as $tosplit) {
					$splitpos = strpos($tosplit, '=');
					$link[ (substr($tosplit, 0, $splitpos) ) ] = substr($tosplit, $splitpos+1);
				}

				//This is fix to prevent entries in the errorlog.
				if(!empty($link['view'])){
					$view = $link['view'] ;
					if (array_key_exists($view,$this->dbview) ){
						$dbKey = $this->dbview[$view];
					}
					else {
						$dbKey = false ;
					}

					if($dbKey){
						if($dbKey=='category'){
							$catId = empty($link['virtuemart_category_id'])? 0:$link['virtuemart_category_id'];
							$manId = empty($link['virtuemart_manufacturer_id'])? 0:$link['virtuemart_manufacturer_id'];

							if(!isset($this->menu ['virtuemart_'.$dbKey.'_id'] [$catId] [$manId])){
								$this->menu ['virtuemart_'.$dbKey.'_id'] [$catId] [$manId] = $item->id;
							} else {
								//vmdebug('This menu item exists two times',$item,$this->template->id);
								if($item->template_style_id==$this->template->id){
									$this->menu ['virtuemart_'.$dbKey.'_id'] [$catId] [$manId]= $item->id;

								}
							}

						} else if ( isset($link['virtuemart_'.$dbKey.'_id']) ){
							if(!isset($this->menu['virtuemart_'.$dbKey.'_id'][ $link['virtuemart_'.$dbKey.'_id'] ])){
								$this->menu['virtuemart_'.$dbKey.'_id'][ $link['virtuemart_'.$dbKey.'_id'] ] = $item->id;
							} else {
								//vmdebug('This menu item exists two times',$item,$this->template->id);
								if($item->template_style_id==$this->template->id){
									$this->menu['virtuemart_'.$dbKey.'_id'][ $link['virtuemart_'.$dbKey.'_id'] ] = $item->id;
								}
							}
						} else if ( $dbKey == 'cart' ){
							$layout = empty($link['layout'])? 0:$link['layout'];
							if(!isset($this->menu[$dbKey][$layout])){
								$this->menu[$dbKey][$layout] = $item->id;
							} else {
								//vmdebug('This menu item exists two times',$item,$this->template->id);
								if($item->template_style_id==$this->template->id){
									$this->menu[$dbKey][$layout] = $item->id;
								}
							}
						} else {
							if(!isset($this->menu[$dbKey])){
								$this->menu[$dbKey] = $item->id;
							} else {
								//vmdebug('This menu item exists two times',$item,$this->template->id);
								if($item->template_style_id==$this->template->id){
									$this->menu[$dbKey] = $item->id;
								}
							}
						}
					}

					elseif ($home == $view ) continue;
					else {
						if(!isset($this->menu[$view])){
							$this->menu[$view]= $item->id ;
						} else {
							//vmdebug('This menu item exists two times',$item,$this->template->id);
							if($item->template_style_id==$this->template->id){
								$this->menu[$view]= $item->id ;
							}
						}
					}

					if ((int)$item->home === 1) {
						$home = $view;
						$homeid = $item->id;
					}
				} else {
					static $msg = array();
					$id = empty($item->id)? '0': $item->id;
					if(empty($msg[$id])){
						if(vmrouterHelper::$debug) vmdebug('my item with empty $link["view"]',$item);
						$msg[$id] = 1;
					}

					//vmError('$link["view"] is empty');
				}
			}
			$mCache[$h] = $this->menu;

			//I wonder if this still makes sense
			if($this->byMenu){
				foreach ($menuVmitems as $item)	{
					if($this->Itemid!=$item->id){
						$mCache[$h.$item->id] = &$mCache[$h.$this->Itemid];
					}
				}
			}

		}

		if ( !isset( $this->menu['virtuemart']) or !isset($this->menu['virtuemart_category_id'][0])) {

			if (!isset ($this->menu['virtuemart_category_id'][0][0]) ) {
				$this->menu['virtuemart_category_id'][0][0] = $homeid;
			}
			// init unsetted views  to defaut front view or nothing(prevent duplicates routes)
			if ( !isset( $this->menu['virtuemart']) ) {
				if (isset ($this->menu['virtuemart_category_id'][0][0]) ) {
					$this->menu['virtuemart'] = $this->menu['virtuemart_category_id'][0][0] ;
				} else $this->menu['virtuemart'] = $homeid;
			}
		}
		//vmdebug('setMenuItemId',$this->menu);
		$mCache[$h] = $this->menu;
	}

	/* Set $this->activeMenu to current Item ID from Joomla Menus */
	function setActiveMenu(){
		if ($this->activeMenu === null ) {

			$app		= JFactory::getApplication();
			$menu		= $app->getMenu('site');

			$this->rItemid = vRequest::getInt('Itemid',false);
			if(!empty($query['Itemid'])){
				$this->Itemid = $query['Itemid'];
			} else {
				$this->Itemid = $this->rItemid;
			}
			if(vmrouterHelper::$debug) vmdebug('setActiveMenu',$this->Itemid,$this->rItemid);
			$menuItem = false;
			if ($this->Itemid ) {
				$menuItem = $menu->getItem($this->Itemid);
			} else {
				$menuItem = $menu->getActive();
				if($menuItem){
					$this->Itemid = $menuItem->id;
				}
				if(vmrouterHelper::$debug) vmdebug('setActiveMenu by getActive',$this->Itemid);
			}

			if(!$menuItem){
				if(vmrouterHelper::$debug) vmdebug('There is no menu item',$menuItem);
			}
			$this->activeMenu = new stdClass();
			$this->activeMenu->view			= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
			$this->activeMenu->virtuemart_category_id	= (empty($menuItem->query['virtuemart_category_id'])) ? 0 : $menuItem->query['virtuemart_category_id'];
			$this->activeMenu->virtuemart_product_id	= (empty($menuItem->query['virtuemart_product_id'])) ? null : $menuItem->query['virtuemart_product_id'];
			$this->activeMenu->virtuemart_manufacturer_id	= (empty($menuItem->query['virtuemart_manufacturer_id'])) ? null : $menuItem->query['virtuemart_manufacturer_id'];
			/* added in 208 */
			$this->activeMenu->virtuemart_vendor_id	= (empty($menuItem->query['virtuemart_vendor_id'])) ? null : $menuItem->query['virtuemart_vendor_id'];

			$this->activeMenu->component	= (empty($menuItem->component)) ? null : $menuItem->component;
		}

	}


	/*
	 * Get language key or use $key in route
	 */
	public function lang($key) {
		if ($this->seo_translate ) {
			$jtext = (strtoupper( $key ) );
			if (vmText::$language->hasKey('COM_VIRTUEMART_SEF_'.$jtext) ){
				return vmText::_('COM_VIRTUEMART_SEF_'.$jtext);
			}
		}

		return $key;
	}

	/*
	 * revert key or use $key in route
	 */
	public function getOrderingKey($key) {

		if ($this->seo_translate ) {
			if ($this->orderings == null) {
				$this->orderings = array(
					'virtuemart_product_id'=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_ID'),
					'product_sku'		=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_SKU'),
					'product_price'		=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_PRICE'),
					'category_name'		=> vmText::_('COM_VIRTUEMART_SEF_CATEGORY_NAME'),
					'category_description'=> vmText::_('COM_VIRTUEMART_SEF_CATEGORY_DESCRIPTION'),
					'mf_name' 			=> vmText::_('COM_VIRTUEMART_SEF_MF_NAME'),
					'product_s_desc'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_S_DESC'),
					'product_desc'		=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_DESC'),
					'product_weight'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_WEIGHT'),
					'product_weight_uom'=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_WEIGHT_UOM'),
					'product_length'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_LENGTH'),
					'product_width'		=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_WIDTH'),
					'product_height'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_HEIGHT'),
					'product_lwh_uom'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_LWH_UOM'),
					'product_in_stock'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_IN_STOCK'),
					'low_stock_notification'=> vmText::_('COM_VIRTUEMART_SEF_LOW_STOCK_NOTIFICATION'),
					'product_available_date'=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_AVAILABLE_DATE'),
					'product_availability'  => vmText::_('COM_VIRTUEMART_SEF_PRODUCT_AVAILABILITY'),
					'product_special'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_SPECIAL'),
					'created_on' 		=> vmText::_('COM_VIRTUEMART_SEF_CREATED_ON'),
					// 'p.modified_on' 		=> vmText::_('COM_VIRTUEMART_SEF_MDATE'),
					'product_name'		=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_NAME'),
					'product_sales'		=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_SALES'),
					'product_unit'		=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_UNIT'),
					'product_packaging'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_PACKAGING'),
					'intnotes'			=> vmText::_('COM_VIRTUEMART_SEF_INTNOTES'),
					'pc.ordering' => vmText::_('COM_VIRTUEMART_SEF_ORDERING')
				);
			}

			if ($result = array_search($key,$this->orderings )) {
				return $result;
			}
		}

		return $key;
	}

	static public function getLanguageTagBySefTag($lTag) {

		static $langs = null;
		if($langs===null){
			$langs = JLanguageHelper::getLanguages('sef');
			//vmdebug('my langs in router '.$lTag,$langs);
		}
		static $langTags = array();

		if(isset($langTags[$lTag])) {
			return $langTags[$lTag];
		} else {
			foreach ($langs as $langTag => $language) {
				if ($language->lang_code == $lTag) {
					$langTags[$lTag] = $language->lang_code;
					break;
				}
			}
		}
		//vmdebug('getLanguageTagBySefTag',$lTag,$langTags[$lTag]);
		if(isset($langTags[$lTag])) {
			return $langTags[$lTag];
		} else return false;
	}

	static function resetLanguage(){
		//Reset language of the router helper in case
		if(VmLanguage::$jSelLangTag!=VmLanguage::$currLangTag){
			//vmdebug('Reset language to '.VmLanguage::$jSelLangTag);
			vmLanguage::setLanguageByTag(VmLanguage::$jSelLangTag, false);
			self::$_instance->slang = false;//VmLanguage::$currLangTag;

		}
	}
	/*
	 * revert string key or use $key in route
	 */
	public function compareKey($string, $key) {
		if ($this->seo_translate ) {
			if (vmText::_('COM_VIRTUEMART_SEF_'.$key) == $string ) {
				return true;
			}

		}
		if ($string == $key) return true;
		return false;
	}
}

// pure php no closing tag