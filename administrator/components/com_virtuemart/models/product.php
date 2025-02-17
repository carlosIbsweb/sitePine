<?php
/**
 *
 * Description Model for VirtueMart Products
 *
 * @package    VirtueMart
 * @subpackage
 * @author Max Milbers, Patrick Kohl, Valerie Isaksen
 * @link https://virtuemart.net
 * @copyright Copyright (c) 2004 - 2022 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: product.php 10633 2022-04-14 12:15:37Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');


class VirtueMartModelProduct extends VmModel {

	/**
	 * products object
	 *
	 * @var integer
	 */
	var $products = array();
	static $decimals = array('product_length','product_width','product_height','product_weight','product_packaging');
	var $_onlyQuery 	= false;

	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 *
	 * @author Max Milbers
	 */
	function __construct () {

		parent::__construct ('virtuemart_product_id');
		$this->setMainTable ('products');
		$this->starttime = microtime (TRUE);
		$this->maxScriptTime = VmConfig::getExecutionTime() * 0.99 - 2;

		if (VmConfig::isSite ()) {
			$this->_validOrderingFieldName = array();
			$browseOrderByFields = VmConfig::get ('browse_orderby_fields',array('pc.ordering,product_name','`p`.product_sku','category_name','mf_name'));
			$this->addvalidOrderingFieldName (array('pc.ordering,product_name'));
		}
		else {
			$browseOrderByFields = self::getValidProductFilterArray ();
			$this->addvalidOrderingFieldName (array('pc.ordering,product_name','product_price','product_sales'));
		}
		$this->addvalidOrderingFieldName ((array)$browseOrderByFields);

		$this->removevalidOrderingFieldName ('virtuemart_product_id');

		//array_unshift ($this->_validOrderingFieldName, 'pc.ordering,product_name');
		array_unshift ($this->_validOrderingFieldName, 'p.virtuemart_product_id');
		$this->_selectedOrdering = VmConfig::get ('browse_orderby_field', 'p.virtuemart_product_id,product_name');

		$this->setToggleName('product_special');

		$this->initialiseRequests ();

		//This is just done now for the moment for developing, the idea is of course todo this only when needed.
		$this->populateState ();

		self::$omitLoaded = VmConfig::get('omitLoaded',false);

		$this->memory_limit = VmConfig::getMemoryLimitBytes();

		$this->_maxItems = VmConfig::get ('absMaxProducts', 700);

		self::$searchMap = array(
			'title:' => '_name',
			'name:' => '_name',
			'customtitle:' => 'customtitle',
			'desc:' => '_desc',
			'metadesc:' => 'metadesc',
			'metakey:' => 'metakey',
			'slug:' => 'slug',
			'sku:' => '_sku',
			'mpn:'=> '_mpn',
			'gtin:'=> '_gtin'
		);
	}

	var $keyword = "0";
	var $product_parent_id = FALSE;
	var $virtuemart_manufacturer_id = FALSE;
	var $virtuemart_category_id = 0;
	var $search_type = '';
	var $searchcustoms = FALSE;
	var $searchplugin = 0;
	var $filter_order = 'p.virtuemart_product_id';
	var $filter_order_Dir = 'DESC';
	var $valid_BE_search_fields = array('product_name', '`p`.product_sku','slug', 'product_s_desc', '`l`.`metadesc`');
	var $_autoOrder = 0;
	var $orderByString = 0;
	var $listing = FALSE;


	static function getValidProductFilterArray () {

		static $filterArray;

		if (!isset($filterArray)) {

			$filterArray = array('product_name', '`p`.created_on', '`p`.product_sku','`p`.product_mpn',
			'product_s_desc', 'product_desc','`l`.slug',
			'category_name', 'category_description', 'mf_name',
			'product_price', '`p`.product_special', '`p`.product_sales', '`p`.product_availability', '`p`.product_available_date',
			'`p`.product_height', '`p`.product_width', '`p`.product_length', '`p`.product_lwh_uom',
			'`p`.product_weight', '`p`.product_weight_uom', '`p`.product_in_stock', '`p`.low_stock_notification',
			'`p`.modified_on', '`p`.product_gtin',
			'`p`.product_unit', '`p`.product_packaging', '`p`.virtuemart_product_id', 'pc.ordering');

			//other possible fields
			//'p.intnotes',		this is maybe interesting, but then only for admins or special shoppergroups

			// this fields leads to trouble, because we have this fields in product, category and manufacturer,
			// they are anyway making not a lot sense for orderby or search.
			//'l.metadesc', 'l.metakey', 'l.metarobot', 'l.metaauthor'
		}

		return $filterArray;
	}


	/**
	 * This function resets the variables holding request depended data to the initial values
	 *
	 * @author Max Milbers
	 */
	function initialiseRequests () {

		$this->keyword = false;
		$this->valid_search_fields = $this->valid_BE_search_fields;
		$this->product_parent_id = FALSE;
		$this->virtuemart_manufacturer_id = FALSE;
		$this->search_type = '';
		$this->searchcustoms = FALSE;
		$this->searchplugin = 0;
		$this->filter_order = VmConfig::get ('browse_orderby_field');
		$this->filter_order_Dir = VmConfig::get('prd_brws_orderby_dir', 'ASC');

		$this->_uncategorizedChildren = null;
		$this->searchAllCats = false;
		$this->virtuemart_vendor_id = 0;
	}

	/**
	 * @deprecated
	 */
	function updateRequests () {
		$this->populateState();
	}

	/**
	 * This functions updates the variables of the model which are used in the sortSearchListQuery
	 *  with the variables from the Request
	 *
	 * @author Max Milbers
	 */
	protected function populateState () {

		if($this->__state_set) return ;

		$app = JFactory::getApplication ();
		$option = 'com_virtuemart';
		$view = vRequest::getCmd('view','product');

		$valid_search_fields = VmConfig::get ('browse_search_fields',array());
		$task = '';

		$this->published = vRequest::getInt('published',null);
		if (VmConfig::isSite()) {
			$filter_order = vRequest::getString ('orderby', "0");

			if($filter_order == "0"){
				$filter_order_raw = $this->getLastProductOrdering($this->_selectedOrdering);
				$filter_order = $this->checkFilterOrder ($filter_order_raw);
			} else {
				$filter_order = $this->checkFilterOrder ($filter_order);
				$this->setLastProductOrdering($filter_order);

			}
			$filter_order_Dir = strtoupper (vRequest::getCmd ('dir', VmConfig::get('prd_brws_orderby_dir', 'ASC')));
			$filter_order_Dir = $this->checkFilterDir($filter_order_Dir);

			$this->product_parent_id = vRequest::getInt ('product_parent_id', FALSE);
			$this->virtuemart_manufacturer_id = vRequest::getInt ('virtuemart_manufacturer_id', FALSE);
			//$this->virtuemart_category_id = vRequest::getInt ('virtuemart_category_id', FALSE);
			$this->searchAllCats = $app->getUserStateFromRequest('com_virtuemart.customfields.searchAllCats','searchAllCats',false);

			$this->keyword = vRequest::getString('keyword','');
			$this->keyword = urldecode($this->keyword);
			$this->keyword = vRequest::filter($this->keyword,FILTER_SANITIZE_STRING,FILTER_FLAG_ENCODE_LOW);

			vRequest::setVar('keyword',urlencode($this->keyword));
			$this->search_type = vRequest::getVar ('search_type', '');
			$this->virtuemart_vendor_id = vmAccess::getVendorId();

			//$oldCat = shopFunctionsF::getLastVisitedCategoryId();
			$this->searchcustoms = $app->getUserStateFromRequest ($option . '.customfields', 'customfields', '', 'array');
			if(!empty($this->searchcustoms)){
				if(VmConfig::get('changeCategoryRemoveFilter',1)){
					$oldCat = shopFunctionsF::getLastVisitedCategoryId();
					$this->virtuemart_category_id = vRequest::getInt ('virtuemart_category_id', FALSE);
					if($oldCat!=$this->virtuemart_category_id){
						vmdebug('category id changed and I UNSET');
						$app->setUserState('com_virtuemart.customfields',null);
						$this->searchcustoms = array();
					}
				}

					if(is_object($this->searchcustoms)) $this->searchcustoms = get_object_vars($this->searchcustoms);
					$this->searchcustoms = vRequest::filter($this->searchcustoms,FILTER_SANITIZE_STRING,FILTER_FLAG_ENCODE_LOW);
				foreach($this->searchcustoms as $k=>$v){
					if(empty($v)) unset($this->searchcustoms[$k]);
				}
			}
			//vmdebug('$this->searchcustoms vRequest::filter',$this->searchcustoms);
		}
		else {
			$task = vRequest::getCmd('task','');
			if (!empty($task)) $task = '.'.$task.'.';
			$filter_order = strtolower ($app->getUserStateFromRequest ('com_virtuemart.'. $view . $task.'.filter_order', 'filter_order', $this->_selectedOrdering, 'string'));

			/*$session = \JFactory::getSession();
			$registry = $session->get('registry');
			vmdebug('my registry',$registry);*/
			$filter_order = $this->checkFilterOrder ($filter_order, $task);
			$filter_order_Dir = strtoupper ($app->getUserStateFromRequest ($option . '.'. $view . $task.'.filter_order_Dir', 'filter_order_Dir', '', 'word'));

			$valid_search_fields = array_unique(array_merge($this->valid_BE_search_fields, $valid_search_fields));

			$view = vRequest::getCmd ('view');
			$stateTypes = array('virtuemart_category_id'=>'int','virtuemart_manufacturer_id'=>'int',/*'product_parent_id'=>'int',*/'filter_product'=>'string','search_type'=>'string','search_order'=>'string','search_date'=>'string','virtuemart_vendor_id' => 'int', 'published' => 'int');

			$this->product_parent_id = vRequest::getInt ('product_parent_id', FALSE);
			foreach($stateTypes as $type => $filter){
				$k= 'com_virtuemart.' . $view . '.'.$type;
				if($filter=='int'){
					$new_state = vRequest::getInt($type, false);
				} else {
					$new_state = vRequest::getVar($type, false);
				}

				if($new_state===false){
					$this->{$type} = $app->getUserState($k, '');
				} else {
					$app->setUserState( $k,$new_state);
					$this->{$type} = $new_state;
				}
			}

			$this->keyword = $this->filter_product;

			$this->search_type = $app->getUserStateFromRequest ($option . '.'. $view . $task.'.search_type', 'search_type', '', 'word');
			if(!vmAccess::manager('managevendors')){
				$this->virtuemart_vendor_id = vmAccess::getVendorId();
			}

			$this->virtuemart_custom_id = $app->getUserStateFromRequest ($option . '.virtuemart_custom_id', 'virtuemart_custom_id', '', 'array');
			if(!empty($this->virtuemart_custom_id)){
				if(is_object($this->virtuemart_custom_id)) $this->virtuemart_custom_id = get_object_vars($this->virtuemart_customfield_id);
				$this->virtuemart_custom_id = vRequest::filter($this->virtuemart_custom_id,FILTER_SANITIZE_NUMBER_INT,FILTER_FLAG_NO_ENCODE);
			}
		}

		$filter_order_Dir = $this->checkFilterDir ($filter_order_Dir, $task);

		// this should be  $this->_selectedOrdering ??
		$this->filter_order = $filter_order;
		$this->filter_order_Dir = $filter_order_Dir;
		$this->valid_search_fields = $valid_search_fields;

		$this->searchplugin = vRequest::getInt ('custom_parent_id', 0);

		$this->__state_set = true;

	}

	/**
	 * @author Max Milbers
	 */
	public function getLastProductOrdering($default = 0){
		$session = JFactory::getSession();
		return $session->get('vmlastproductordering', $default, 'vm');
	}

	/**
	 * @author Max Milbers
	 */
	public function setLastProductOrdering($ordering){
		$session = JFactory::getSession();
		return $session->set('vmlastproductordering', (string) $ordering, 'vm');
	}

	/**
	 * Sets the keyword variable for the search
	 *
	 * @param string $keyword
	 */
	function setKeyWord ($keyword) {

		$this->keyword = $keyword;
	}

	/**
	 * Function for sorting, searching, filtering and pagination for product ids.
	 *
	 * @author Max Milbers
	 * @param bool $onlyPublished this is not used anylonger, either blocked by perms or use the published by populated state or the injection array
	 * @param false $virtuemart_category_id only products within the given categories
	 * @param false $group return a group (featured, topten, recent, latest,...)
	 * @param false $nbrReturnProducts
	 * @param array $params array to inject parameters, overwrites behaviour by Request variable
	 * @return array|mixed|null
	 */
	function sortSearchListQuery ($onlyPublished = TRUE, $virtuemart_category_id = FALSE, $group = FALSE, $nbrReturnProducts = FALSE, $params = array() ) {
		if($this->debug === 1) vmTrace('sortSearchListQuery',FALSE,3);
		$keyword = isset($params['keyword'])? $params['keyword'] : $this->keyword;
		/*
		 * Plugins must return an array with product ids
		 */
		$result = array();
		$used = vDispatcher::trigger('plgVmMySortSearchListProductsQuery',
			array(
				&$result,
				$keyword,
				$onlyPublished,
				$virtuemart_category_id,
				$group,$nbrReturnProducts,
				$params,
			));

		if($used){
			foreach ($used as $ret) {
				if ($ret === true) {
					return $result;
					//if plugin returns true, return it's values from the result
				}
			}
		}

		$db = JFactory::getDbo();

		//vmdebug('sortSearchListQuery '.$group,$nbrReturnProducts);

		//User Q.Stanley said that removing group by is increasing the speed of product listing in a bigger shop (10k products) by factor 60
		//So what was the reason for that we have it? TODO experiemental, find conditions for the need of group by
		$groupBy = ' group by p.`virtuemart_product_id` ';

		//administrative variables to organize the joining of tables
		$joinLang = false;
		$joinCategory = FALSE;
		$joinCatLang = false;
		$joinMf = FALSE;
		$joinMfLang = false;
		$joinPrice = FALSE;
		$joinCustom = FALSE;
		$joinShopper = FALSE;
		$joinChildren = FALSE;

		$langFields = isset($params['langFields'])? $params['langFields']: array();

		$where = array();

		$isSite = true;
		if(!VmConfig::isSite() and vmAccess::manager('product') ){
			$isSite = false;
		}

		$this->useLback = vmLanguage::getUseLangFallback();
		$this->useJLback = vmLanguage::getUseLangFallbackSecondary();

		if(isset($params['searchcustoms'])){
			$searchcustoms = $params['searchcustoms'];
		} else {
			$searchcustoms = $this->searchcustoms;
		}

		if(isset($params['virtuemart_custom_id'])){
			$virtuemart_custom_id = $params['virtuemart_custom_id'];
		} else if(!empty($this->virtuemart_custom_id))  {
			$virtuemart_custom_id = $this->virtuemart_custom_id;
		}

		if (!empty($searchcustoms) or !empty($virtuemart_custom_id)) {
			$joinCustom = TRUE;

			$and = isset($params['combineTags'])? $params['combineTags'] : vRequest::getInt('combineTags',true);
			if (!empty($searchcustoms)){
				if($this->debug === 1) vmdebug('sortSearchListQuery',$searchcustoms);


				$first = true;
				foreach ($searchcustoms as $key => $searchcustom) {
					if(empty($searchcustom)) continue;
					if(!empty($searchcustom) and !empty((int)$key)){

						if(VmConfig::get('strictCustomfieldTags', false)){
							$searchCustomSQ = '= "' . $db->escape(trim($searchcustom), TRUE).'"';
						} else {
							$searchCustomSQ = 'like "%' . $db->escape(trim($searchcustom), TRUE).'%"';
						}

						if($first){
							//$custom_search[] = '(pf.`virtuemart_custom_id`="' . (int)$key . '" and pf.`customfield_value` like "%' . $db->escape($searchcustom, TRUE) . '%")';
							$custom_search[] = '(pf.`virtuemart_custom_id`="' . (int)$key . '" and pf.`customfield_value` '.$searchCustomSQ.')';
							$first = false;
						} else {
							//$custom_search[] = 'p.`virtuemart_product_id` IN ( SELECT h.`virtuemart_product_id` FROM `#__virtuemart_product_customfields` as h
						//WHERE h.`virtuemart_custom_id`="' . (int)$key . '" and h.`customfield_value` like "%' . $db->escape ($searchcustom, TRUE) . '%")';
							$custom_search[] = 'p.`virtuemart_product_id` IN ( SELECT h.`virtuemart_product_id` FROM `#__virtuemart_product_customfields` as h
						WHERE h.`virtuemart_custom_id`="' . (int)$key . '" and h.`customfield_value` '.$searchCustomSQ.')';						}
					}
				}
			}

			if(!empty($virtuemart_custom_id)) {
				foreach ($virtuemart_custom_id as $key => $virtuemart_customId) {
					if(empty($virtuemart_customId)) continue;
					$custom_search[] = '(pf.`virtuemart_custom_id`="' . (int)$virtuemart_customId . '" )';
				}
			}


			if(!empty($custom_search)){

				if(empty($searchcustoms)) $searchcustoms = true;
				if($and){
					$andor= ' AND ';
				} else {
					$andor= ' OR ';
				}
				$where[] = " ( " . implode ($andor, $custom_search) . " ) ";
				//$where[] = " ( " . implode (' AND ', $custom_search_value) . " AND (".implode (' OR ', $custom_search_key).")) ";
				if($this->searchAllCats){
					$virtuemart_category_id = FALSE;
				}
			} else {
				$searchcustoms = false;
			}

		}

		$filter_search = array();

		if (!empty($keyword) and $group === FALSE) {

			$keyword = vRequest::filter(html_entity_decode($keyword, ENT_QUOTES, "UTF-8"),FILTER_SANITIZE_STRING,FILTER_FLAG_ENCODE_LOW);
			$keyword = $db->escape( $keyword, true );
			$keyword =  '"%' .str_replace(array(' '),'%', $keyword). '%"';

			//$keyword = '"%' . $db->escape ($keyword, TRUE) . '%"';
			//vmdebug('Current search field',$this->valid_search_fields);
			$adjustedKeyword = ''; 
			$newSearchfields = $this->filterMapSearchFields($this->valid_search_fields, $keyword, $adjustedKeyword); 
			if ((!empty($adjustedKeyword) && ($adjustedKeyword !== $keyword))) {
				$keyword = $adjustedKeyword;
			}
			vmdebug('Current search field',$newSearchfields,$this->valid_search_fields);
			foreach ($newSearchfields as $searchField) {
				$prodLangFB = false;
				if ($searchField == 'category_name' || $searchField == 'category_description') {
					$joinCatLang = true;
				}
				else if ($searchField == 'mf_name') {
					$joinMfLang = true;
				}
				else if ($searchField == 'product_price') {
					$joinPrice = TRUE;
				}
				else if ($searchField == 'product_name' or $searchField == 'product_s_desc' or $searchField == 'product_desc' or $searchField == 'slug' or $searchField == 'metadesc' /*strpos($searchField, 'slug')!== FALSE or strpos($searchField, 'metadesc')!== FALSE*/){
					$langFields[] = $searchField;
					$prodLangFB = true;
				}

				if($prodLangFB){
					$fields = self::joinLangLikeField($searchField,$keyword);
					//vmdebug('my search fields',$fields);
					$filter_search = array_merge($filter_search, $fields);
				} else {
					if (strpos ($searchField, '`') !== FALSE){
						$keywords_plural = preg_replace('/\s+/', '%" AND '.$searchField.' LIKE "%', $keyword);
						$filter_search[] =  $searchField . ' LIKE ' . $keywords_plural;
					} else {
						$keywords_plural = preg_replace('/\s+/', '%" AND `'.$searchField.'` LIKE "%', $keyword);
						$filter_search[] = '`'.$searchField.'` LIKE '.$keywords_plural;
					}
				}

			}
			if (!empty($filter_search)) {
				$where[] = '(' . implode (' OR ', $filter_search) . ')';
			}
			else {
				$where[] = '`l`.product_name LIKE ' . $keyword;
				$langFields[] = 'product_name';
				//If they have no check boxes selected it will default to product name at least.
			}
		}

		if($isSite and !VmConfig::get('use_as_catalog',0)) {
			if(VmConfig::get('stockhandle_products',false)){
				$product_stockhandle = $this->getProductStockhandle();
				if (($product_stockhandle->disableit_children || VmConfig::get('stockhandle','none') == "disableit_children") && ($product_stockhandle->disableit || VmConfig::get('stockhandle','none') == "disableit")) {
					$where[] = ' CASE
									WHEN (p.`product_stockhandle` = "0" AND "'. VmConfig::get('stockhandle','none') .'" = "disableit_children") OR (p.`product_stockhandle` = "disableit_children")
										THEN ((p.`product_in_stock` - p.`product_ordered`) >"0" OR (children.`product_in_stock` - children.`product_ordered`) > "0")
									WHEN (p.`product_stockhandle` = "0" AND "'. VmConfig::get('stockhandle','none') .'" = "disableit") OR (p.`product_stockhandle` = "disableit")
										THEN p.`product_in_stock` - p.`product_ordered` > "0"
									ELSE 1
								 END = 1 ';
					$joinChildren = TRUE;
				} else if ($product_stockhandle->disableit_children || VmConfig::get('stockhandle','none') == "disableit_children") {
					$where[] = ' CASE
									WHEN (p.`product_stockhandle` = "0" AND "'. VmConfig::get('stockhandle','none') .'" = "disableit_children") OR (p.`product_stockhandle` = "disableit_children")
										THEN ((p.`product_in_stock` - p.`product_ordered`) >"0" OR (children.`product_in_stock` - children.`product_ordered`) > "0")
									ELSE 1
								 END = 1 ';
					$joinChildren = TRUE;
				} else if ($product_stockhandle->disableit || VmConfig::get('stockhandle','none') == "disableit") {
					$where[] = ' CASE
									WHEN (p.`product_stockhandle` = "0" AND "'. VmConfig::get('stockhandle','none') .'" = "disableit") OR (p.`product_stockhandle` = "disableit")
										THEN p.`product_in_stock` - p.`product_ordered` > "0"
									ELSE 1
								 END = 1 ';
				}
			} else if (VmConfig::get('stockhandle','none') == "disableit_children") {
				$where[] = ' ( (p.`product_in_stock` - p.`product_ordered`) >"0" OR (children.`product_in_stock` - children.`product_ordered`) > "0") ';
				$joinChildren = TRUE;
			} else if (VmConfig::get('stockhandle','none')=='disableit') {
				$where[] = ' p.`product_in_stock` - p.`product_ordered` >"0" ';
			}
		}

		if ($this->product_parent_id) {
			$where[] = ' p.`product_parent_id` = ' . $this->product_parent_id;
			$virtuemart_category_id = false;
		}

		if (!empty($virtuemart_category_id )){
			if( !is_array($virtuemart_category_id)) {
				$virtuemart_category_id = array($virtuemart_category_id);
			}
			$virtuemart_category_id = vRequest::filter($virtuemart_category_id, FILTER_SANITIZE_NUMBER_INT,FILTER_FLAG_NO_ENCODE);
		}

		if (!empty($virtuemart_category_id )) {
			$joinCategory = TRUE;

			if(VmConfig::get('show_subcat_products',false)){
				/*GJC add subcat products*/
				$catmodel = VmModel::getModel ('category');
				$cats = '';
				foreach($virtuemart_category_id as $catId){
					$childcats = $catmodel->getChildCategoryList(1, $catId,null, null, true);
					foreach($childcats as $k=>$childcat){
						if(!empty($childcat->virtuemart_category_id)){
							$cats .= $childcat->virtuemart_category_id .',';
						}
					}
					$cats .= $catId;
				}
				$cats = trim($cats,',');
                /*if(!empty($cats)){
                    $joinCategory = TRUE;
                    $where[] = ' `pc`.`virtuemart_category_id` IN ('.$cats.') ';
                }*/
			} else {
				$cats = implode(',', $virtuemart_category_id);
			}

			if(!empty($cats)){
				$joinCategory = TRUE;
				$where[] = ' `pc`.`virtuemart_category_id` IN ('.$cats.') ';
			}

		} else if ($isSite) {

			if($searchcustoms or !empty($filter_search)){
				if (!VmConfig::get('show_uncat_parent_products',TRUE)) {
					$joinCategory = TRUE;
					$where[] = ' ((p.`product_parent_id` = "0" AND `pc`.`virtuemart_category_id` > "0") OR p.`product_parent_id` > "0") ';
				}
				if (!VmConfig::get('show_uncat_child_products',TRUE)) {
					$joinCategory = TRUE;
					$where[] = ' ((p.`product_parent_id` > "0" AND `pc`.`virtuemart_category_id` > "0") OR p.`product_parent_id` = "0") ';
				}
			}

		}

		if ($isSite and !VmConfig::get('show_unpub_cat_products',TRUE)) {
			$joinCategory = TRUE;
			$where[] = ' `c`.`published` = 1 ';
		}

		if ($isSite) {

			$virtuemart_shoppergroup_ids = isset($params['virtuemart_shoppergroup_ids']) ? $params['virtuemart_shoppergroup_ids'] : self::getCurrentUserShopperGrps();

			if (is_array ($virtuemart_shoppergroup_ids)) {
				$sgrgroups = array();
				foreach ($virtuemart_shoppergroup_ids as $key => $virtuemart_shoppergroup_id) {
					$sgrgroups[] = '`ps`.`virtuemart_shoppergroup_id`= "' . (int)$virtuemart_shoppergroup_id . '" ';
				}
				$sgrgroups[] = '`ps`.`virtuemart_shoppergroup_id` IS NULL ';
				$where[] = " ( " . implode (' OR ', $sgrgroups) . " ) ";

				$joinShopper = TRUE;
			}
		}

		$virtuemart_manufacturer_id = isset($params['virtuemart_manufacturer_id']) ? $params['virtuemart_manufacturer_id'] : $this->virtuemart_manufacturer_id;
		if ($virtuemart_manufacturer_id) {
			$joinMf = TRUE;
			if(is_array($virtuemart_manufacturer_id)){
				$mans = array();
				foreach ($virtuemart_manufacturer_id as $key => $v) {
					$mans[] = '`#__virtuemart_product_manufacturers`.`virtuemart_manufacturer_id`= "' . (int)$v . '" ';
				}
				$where[] = " ( " . implode (' OR ', $mans) . " ) ";
			} else {
				$where[] = ' `#__virtuemart_product_manufacturers`.`virtuemart_manufacturer_id` = ' . (int)$virtuemart_manufacturer_id;
			}

		}

		// Time filter
		$search_type = isset($params['search_type']) ? $params['search_type'] : $this->search_type;
		if ($search_type != '') {
			$search_order = isset($params['search_order']) ? $params['search_order'] : vRequest::getCmd ('search_order',$this->search_order);
			$search_order = $db->escape ($search_order == 'bf' ? '<' : '>');
			switch ($search_type) {
				case 'parent':
					$where[] = 'p.`product_parent_id` = "0"';
					break;
				case 'product':
					$where[] = 'p.`modified_on` ' . $search_order . ' "' . $db->escape (vRequest::getVar ('search_date')) . '"';
					break;
				case 'price':
					$joinPrice = TRUE;
					$where[] = 'pp.`modified_on` ' . $search_order . ' "' . $db->escape (vRequest::getVar ('search_date')) . '"';
					break;
				case 'withoutprice':
					$joinPrice = TRUE;
					$where[] = 'pp.`product_price` IS NULL';
					break;
				case 'stockout':
					$where[] = ' p.`product_in_stock`- p.`product_ordered` < 1';
					break;
				case 'stocklow':
					$where[] = 'p.`product_in_stock`- p.`product_ordered` < p.`low_stock_notification`';
					break;
				default:
					$group = $this->search_type;
					break;
			}
		}


		// special  orders case
		$ff_select_price = '';
		$filterOrderDir = isset($params['filter_order_Dir']) ? $params['filter_order_Dir'] : $this->filter_order_Dir;
		$filter_order = isset($params['filter_order']) ? $params['filter_order'] : $this->filter_order;


		if($filter_order == 'pc.ordering,product_name'){
			if (empty($virtuemart_category_id )) {
				$filter_order = 'product_name';
			}
		}
		//vmdebug('my filter ordering ',$filter_order);
		switch ($filter_order) {
			case '`p`.product_special':
				if($isSite){
					$where[] = ' p.`product_special`="1" '; // TODO Change  to  a  individual button
					$orderBy = 'ORDER BY RAND()';
				} else {
					$orderBy = 'ORDER BY p.`product_special` '.$filterOrderDir.', p.`virtuemart_product_id` '.$filterOrderDir;
				}
				break;
			case 'category_name':
				$orderBy = ' ORDER BY `category_name` '.$filterOrderDir.', p.`virtuemart_product_id` '.$filterOrderDir;
				$joinCategory = TRUE;
				$joinCatLang = true;
				break;
			case 'category_description':
				$orderBy = ' ORDER BY `category_description` '.$filterOrderDir.', p.`virtuemart_product_id` '.$filterOrderDir;
				$joinCategory = TRUE;
				$joinCatLang = true;
				break;
			case 'mf_name':
			case '`l`.mf_name':
				$orderBy = ' ORDER BY `mf_name` '.$filterOrderDir.', p.`virtuemart_product_id` '.$filterOrderDir;
				$joinMf = TRUE;
				$joinMfLang = true;
				break;
			case 'ordering':
			case 'pc.ordering':
				$orderBy = ' ORDER BY `pc`.`ordering` '.$filterOrderDir.', p.`virtuemart_product_id` '.$filterOrderDir;
				$joinCategory = TRUE;
				break;
			case 'pc.ordering,product_name':
				$orderBy = ' ORDER BY `pc`.`ordering` '.$filterOrderDir.', `product_name` '.$filterOrderDir;
				$joinCategory = TRUE;
				$joinLang = true;
				$langFields[] = 'product_name';
				break;
			case 'pordering':
				$orderBy = ' ORDER BY `p`.`pordering` '.$filterOrderDir;

				break;
			case 'product_price':
				$orderBy = ' ORDER BY `product_price` '.$filterOrderDir.', p.`virtuemart_product_id` '.$filterOrderDir;
				$ff_select_price = ' , IF(pp.override, pp.product_override_price, pp.product_price) as product_price ';
				$joinPrice = TRUE;
				break;
			case 'created_on':
			case '`p`.created_on':
				$orderBy = ' ORDER BY p.`created_on` '.$filterOrderDir.', p.`virtuemart_product_id` '.$filterOrderDir;
				break;
			case 'published':
				$orderBy = ' ORDER BY p.`published` '.$filterOrderDir.', p.`virtuemart_product_id` '.$filterOrderDir;
				break;
			default:
				if (!empty($filter_order)) {
					$orderBy = ' ORDER BY '.$filter_order.' ' . $filterOrderDir ;
					if(strpos($filter_order, 'virtuemart_product_id')===FALSE){
						$orderBy .= ', p.`virtuemart_product_id` '.$filterOrderDir;
					}
					if($filter_order=='product_name'){
						$joinLang = true;
						$langFields[] = 'product_name';
					}
				} else {
					$orderBy = ' ORDER BY product_name ' . $filterOrderDir.', p.`virtuemart_product_id` '.$filterOrderDir;
					$langFields[] = 'product_name';
				}
				break;
		}
		$filterOrderDir = '';

		$origReturnPrd = 3;
		//Group case from the modules
		if ($group) {

			//$latest_products_days = VmConfig::get ('latest_products_days', 7);
			$latest_products_orderBy = VmConfig::get ('latest_products_orderBy','created_on');
			$groupBy = 'group by p.`virtuemart_product_id` ';
			switch ($group) {
				case 'featured':

					$where[] = 'p.`product_special`="1" ';
					//$limits = $this->setPaginationLimits();
					if($isSite){
						$origReturnPrd = $nbrReturnProducts;
						$nbrReturnProducts = $nbrReturnProducts * 3;
					}
					break;
				case 'discontinued':
					$where[] = 'p.`product_discontinued`="1" ';
					if($isSite){
						$origReturnPrd = $nbrReturnProducts;
						$nbrReturnProducts = $nbrReturnProducts * 3;
					}
					break;
				case 'latest':
					$orderBy = 'ORDER BY p.`' . $latest_products_orderBy . '` DESC, p.`virtuemart_product_id` DESC';
					break;
				case 'random':
					$orderBy = 'ORDER BY RAND() '; //LIMIT 0, '.(int)$nbrReturnProducts ; //TODO set limit LIMIT 0, '.(int)$nbrReturnProducts;
					break;
				case 'topten':
					$orderBy = 'ORDER BY p.`product_sales` DESC, p.`virtuemart_product_id` DESC'; //LIMIT 0, '.(int)$nbrReturnProducts;  //TODO set limitLIMIT 0, '.(int)$nbrReturnProducts;
					$joinPrice = true;
					$where[] = 'pp.`product_price`>"0.001" ';
					break;
				case 'recent':
					$rIds = self::getRecentProductIds($nbrReturnProducts);	// get recent viewed from browser session
					return $rIds;
			}
			// 			$joinCategory 	= false ; //creates error
			// 			$joinMf 		= false ;	//creates error
			$joinPrice = TRUE;	//Why we set this all the time?
			$this->searchplugin = FALSE;

		}

		if($group!='discontinued' and !VmConfig::get('discontinuedPrdsBrowseable',1) /*and $isSite*/){
			$where[] = ' p.`product_discontinued` = "0" ';
		}

		if(!vmAccess::manager('product')){
			$where[] = ' p.`published`="1" ';
		} else {
			if($isSite and !VmConfig::get('showUnpublishedProducts', true)){
				$where[] = ' p.`published`="1" ';
			} else {
				$published = isset($params['published']) ? $params['published'] : $this->published;
				vmdebug('my published ',$published,$params);
				if($published>0){
					$where[] = ' p.`published`="1" ';
				} else if(!$isSite and $published=='0'){
					$where[] = ' p.`published`="0" ';
				}
			}
		}

		$priceLow = isset($params['priceLow'])? $params['priceLow'] : vRequest::getInt('priceLow',null);
		if(isset($priceLow)){
			$joinPrice = TRUE;
			$where[] = 'pp.`product_price` >= "'.$priceLow.'" ';
		}
		$priceHigh = isset($params['priceHigh'])? $params['priceHigh'] : vRequest::getInt('priceHigh',null);
		if(isset($priceHigh)){
			$joinPrice = TRUE;
			$where[] = 'pp.`product_price` <= "'.$priceHigh.'" ';
		}

		if(VmConfig::get('multix','none')!='none'){
			if(!empty($this->virtuemart_vendor_id)){
				$where[] = ' p.`virtuemart_vendor_id` = "'.$this->virtuemart_vendor_id.'" ';
			}
		}


		$joinedTables = array();

		//Maybe we have to join the language to order by product name, description, etc,...
		$productLangFields = array('product_s_desc','product_desc','product_name','metadesc','metakey','slug');
		if(!empty($orderBy)){
			foreach($productLangFields as $field){
				if(strpos($orderBy,$field,6)!==FALSE){
					$langFields[] = $field;
					$joinLang = true;
					break;
				}
			}
		}


		if($isSite ){
			if((empty($keyword) or $group !== FALSE) and self::$omitLoaded and self::$_alreadyLoadedIds){
				$where[] = ' ( p.`virtuemart_product_id` NOT IN ('.implode(',',self::$_alreadyLoadedIds).') ) ';
			}
		}


		$selectLang = '';
		//This option switches between showing products without the selected language or only products with language.
		if ($joinLang or count($langFields)>0 or ($isSite and VmConfig::get('prodOnlyWLang',false)) ){

			$joinedTables = self::joinLangTables($this->_maintable,'p','virtuemart_product_id');
			$langFields = array_unique($langFields);
			$langSelects = self::joinLangSelectFields($langFields);

			if(!empty($langSelects)){
				$selectLang = ', '.implode(', ',$langSelects);
			}
		}

		$select = ' p.`virtuemart_product_id`'.$ff_select_price.$selectLang.' 
		FROM `#__virtuemart_products` as p ';

		if ($searchcustoms) {
			$joinedTables[] = ' INNER JOIN `#__virtuemart_product_customfields` as pf ON p.`virtuemart_product_id` = pf.`virtuemart_product_id` ';
		}

		if ($joinShopper == TRUE) {
			$joinedTables[] = ' LEFT JOIN `#__virtuemart_product_shoppergroups` as ps ON p.`virtuemart_product_id` = `ps`.`virtuemart_product_id` ';
		}

		if ($joinCategory == TRUE or $joinCatLang) {
			/*if($app->isSite() and !empty($keyword)){ 	//We need an extra boolean to handel this correctly
				$joink = 'INNER';
			} else {*/
				$joink = 'LEFT';
			//}
			$joinedTables[] = ' '.$joink.' JOIN `#__virtuemart_product_categories` as pc ON p.`virtuemart_product_id` = `pc`.`virtuemart_product_id` ';
			if ($isSite and !VmConfig::get('show_unpub_cat_products',TRUE)) {
				$joinedTables[] = ' LEFT JOIN `#__virtuemart_categories` as c ON c.`virtuemart_category_id` = `pc`.`virtuemart_category_id` ';
			}
			if($joinCatLang){
				$joinedTables[] = ' LEFT JOIN `#__virtuemart_categories_' . VmConfig::$vmlang . '` as cl ON cl.`virtuemart_category_id` = `pc`.`virtuemart_category_id`';
			}
		}

		if ($joinMf == TRUE or $joinMfLang) {
			$joinedTables[] = ' LEFT JOIN `#__virtuemart_product_manufacturers` ON p.`virtuemart_product_id` = `#__virtuemart_product_manufacturers`.`virtuemart_product_id` ';
			if($joinMfLang){
				$joinedTables[] = 'LEFT JOIN `#__virtuemart_manufacturers_' . VmConfig::$vmlang . '` as m ON m.`virtuemart_manufacturer_id` = `#__virtuemart_product_manufacturers`.`virtuemart_manufacturer_id` ';
			}
		}

		if ($joinPrice == TRUE) {
			$joinedTables[] = ' LEFT JOIN `#__virtuemart_product_prices` as pp ON p.`virtuemart_product_id` = pp.`virtuemart_product_id`   ';

		}

		if ($this->searchplugin !== 0) {
			if (!empty($PluginJoinTables)) {
				$plgName = $PluginJoinTables[0];
				$joinedTables[] = ' LEFT JOIN `#__virtuemart_product_custom_plg_' . $plgName . '` as ' . $plgName . ' ON ' . $plgName . '.`virtuemart_product_id` = p.`virtuemart_product_id` ';
			}
		}

		if ($joinChildren) {
			$joinedTables[] = ' LEFT OUTER JOIN `#__virtuemart_products` children ON p.`virtuemart_product_id` = children.`product_parent_id` ';
		}

		if ($this->searchplugin !== 0) {
			JPluginHelper::importPlugin('vmcustom');
			vDispatcher::trigger('plgVmBeforeProductSearch', array(&$select, &$joinedTables, &$where, &$groupBy, &$orderBy,&$joinLang));
		}

		if (count ($where) > 0) {
			$whereString = ' WHERE (' . implode (' AND ', $where) . ') ';
		}
		else {
			$whereString = '';
		}
		//vmdebug ( ' joined ? ',$select, $joinedTables, $whereString, $groupBy, $orderBy, $filter_order_Dir );		/* jexit();  */

		$this->orderByString = $orderBy;

		if($this->_onlyQuery){
			return (array($select,$joinedTables,$where,$orderBy,$joinLang));
		}
		$joinedTables = " \n".implode(" \n",$joinedTables);


		vmSetStartTime('sortSearchQuery');
		$product_ids = $this->exeSortSearchListQuery (2, $select, $joinedTables, $whereString, $groupBy, $orderBy, $filterOrderDir, $nbrReturnProducts);

		if($isSite and ($group=='featured' or $group=='discontinued')){

			$product_idsTmp = $product_ids;
			shuffle($product_idsTmp);
			$max = count($product_idsTmp);
			//vmdebug('Lets get a '.$group.' shuffle',$product_ids,$product_idsTmp);
			$product_ids = array_slice($product_idsTmp,0,$origReturnPrd);
			$this->setGetCount(true);

		}
		vmTime('sortSearchQuery products: '.$group,'sortSearchQuery');

		return $product_ids;
	}

	public function getProductStockhandle () {

		static $product_stockhandle = null;

		if($product_stockhandle===null){
			$db = JFactory::getDbo();
			$db->setQuery (' SELECT `product_stockhandle` FROM `#__virtuemart_products` WHERE `product_stockhandle` = "disableit_children" AND `published` = "1" LIMIT 1 ');

			$product_stockhandle = new stdClass();
			$product_stockhandle->disableit_children = $db->loadResult () ? 1 : 0;

			$db->setQuery (' SELECT `product_stockhandle` FROM `#__virtuemart_products` WHERE `product_stockhandle` = "disableit" AND `published` = "1" LIMIT 1 ');
			$product_stockhandle->disableit = $db->loadResult () ? 1 : 0;
		}
		return $product_stockhandle;

	}

	/**
	 * Override
	 *
	 * @see VmModel::setPaginationLimits()
	 */
	public function setPaginationLimits ( $force = false ) {

		$app = JFactory::getApplication ();
		$view = vRequest::getCmd ('view','virtuemart');

		$cateid = vRequest::getInt ('virtuemart_category_id', -1);
		$manid = vRequest::getInt ('virtuemart_manufacturer_id', 0);

		$limitString = 'com_virtuemart.' . $view . '.limit';
		$limit = (int)$app->getUserStateFromRequest ($limitString, 'limit');

		$limitStartString  = 'com_virtuemart.' . $view . '.limitstart';
		if (VmConfig::isSite() and ($cateid != -1 or $manid != 0) ) {

			//vmdebug('setPaginationLimits is site and $cateid,$manid ',$cateid,$manid);
			$lastCatId = ShopFunctionsf::getLastVisitedCategoryId ();
			$lastManId = ShopFunctionsf::getLastVisitedManuId ();

			if( !empty($cateid) and $cateid != -1) {
				$gCatId = $cateid;
			} else if( !empty($lastCatId) ) {
				$gCatId = $lastCatId;
			}

			if(!empty($gCatId)){
				$catModel= VmModel::getModel('category');
				$category = $catModel->getCategory($gCatId);
			} else {
				$category = new stdClass();
			}

			if ((!empty($lastCatId) and $lastCatId != $cateid) or (!empty($manid) and $lastManId != $manid)) {
				//We are in a new category or another manufacturer, so we start at page 1
				$limitStart = vRequest::getInt ('limitstart', 0,'GET');
			}
			else {
				//We were already in the category/manufacturer, so we take the value stored in the session
				$limitStartString  = 'com_virtuemart.' . $view . 'c' . $cateid .'m'.$manid. '.limitstart';
				$limitStart = $app->getUserStateFromRequest ($limitStartString, 'limitstart', vRequest::getInt ('limitstart', 0,'GET'), 'int');
			}
//vmdebug('setPaginationLimits $limitStart',$limitStart);
			if(empty($limit) and !empty($category->limit_list_initial)){
				$suglimit = $category->limit_list_initial;
			}
			else if(!empty($limit)){
				$suglimit = $limit;
			} else {
				$suglimit = VmConfig::get ('llimit_init_FE', 24);
			}
			if(empty($category->products_per_row)){
				$category->products_per_row = VmConfig::get ('products_per_row', 3);
			}
			if(empty($category->products_per_row)){
				$category->products_per_row = 1;
			}
			$rest = $suglimit%$category->products_per_row;
			$limit = $suglimit - $rest;

			if(!empty($category->limit_list_step)){
				$prod_per_page = explode(",",$category->limit_list_step);
			} else {
				//fix by hjet
				$prod_per_page = explode(",",VmConfig::get('pagseq_'.$category->products_per_row));
			}

			if($limit <= $prod_per_page['0'] && array_key_exists('0',$prod_per_page)){
				$limit = $prod_per_page['0'];
			}

			//vmdebug('Calculated $limit  ',$limit,$suglimit);
		}
		else {
			$limitStart = $app->getUserStateFromRequest ('com_virtuemart.' . $view . '.limitstart', 'limitstart', vRequest::getInt ('limitstart', 0,'GET'), 'int');
		}

		if(empty($limit)){
			if(VmConfig::isSite()){
				$limit = VmConfig::get ('llimit_init_FE',24);
			} else {
				$limit = VmConfig::get ('llimit_init_BE',30);
			}
			if(empty($limit)){
				$limit = 30;
			}
		}

		$this->setState ('limit', (int)$limit);
		$this->setState ($limitString, (int)$limit);
		$this->_limit = $limit;

		//There is a strange error in the frontend giving back 9 instead of 10, or 24 instead of 25
		//This functions assures that the steps of limitstart fit with the limit
		$limitStart = ceil ((float)$limitStart / (float)$limit) * $limit;

		$this->setState ('limitstart', (int)$limitStart);
		$this->setState ($limitStartString, (int)$limitStart);

		$this->_limitStart = $limitStart;

		return array($this->_limitStart, $this->_limit);
	}

	static public function getCurrentUserShopperGrps(){

		static $ids = false;

		if(!$ids){
			$usermodel = VmModel::getModel ('user');
			$currentVMuser = $usermodel->getCurrentUser ();
			if(!is_array($currentVMuser->shopper_groups)){
				$ids = (array)$currentVMuser->shopper_groups;
			} else {
				$ids = $currentVMuser->shopper_groups;
			}
		}

		return $ids;
	}

	static public function emptyStaticCache(){
		self::$_products = array();
		self::$_productsSingle = array();
		self::$_cacheOpt = array();
		self::$_cacheOptSingle = array();
	}

	static public function checkIfCached($virtuemart_product_id, $front = NULL, $withCalc = TRUE, $onlyPublished = TRUE, $quantity = 1,$virtuemart_shoppergroup_ids = 0, $withRating = 0){

		if(!isset($front) and isset(self::$_cacheOpt[$virtuemart_product_id])){
			$front = self::$_cacheOpt[$virtuemart_product_id]->front;
			$withCalc = self::$_cacheOpt[$virtuemart_product_id]->withCalc;
			$onlyPublished = self::$_cacheOpt[$virtuemart_product_id]->onlyPublished;
			$quantity = self::$_cacheOpt[$virtuemart_product_id]->quantity;
			$virtuemart_shoppergroup_ids = self::$_cacheOpt[$virtuemart_product_id]->virtuemart_shoppergroup_ids;
			$withRating = self::$_cacheOpt[$virtuemart_product_id]->withRating;
		} else {
			$front = empty($front)?0:TRUE;
			$withCalc = $withCalc?TRUE:0;
			$onlyPublished = $onlyPublished?TRUE:0;
			$withRating = $withRating?TRUE:0;

			$opt = new stdClass();
			$opt->virtuemart_product_id = (int)$virtuemart_product_id;
			$opt->front = $front;
			$opt->withCalc = $withCalc;
			$opt->onlyPublished = $onlyPublished;
			$opt->quantity = (int)$quantity;

			if($virtuemart_shoppergroup_ids !=0 and is_array($virtuemart_shoppergroup_ids)){
				$virtuemart_shoppergroup_ids = implode('.',$virtuemart_shoppergroup_ids);
			} else {
				$virtuemart_shoppergroup_ids = $virtuemart_shoppergroup_ids?TRUE:0;
			}

			$opt->virtuemart_shoppergroup_ids = $virtuemart_shoppergroup_ids;
			$opt->withRating = $withRating;
			self::$_cacheOpt[$virtuemart_product_id] = $opt;
		}


		$productKey = $virtuemart_product_id.':'.$front.$onlyPublished.':'.$quantity.':'.$virtuemart_shoppergroup_ids.':'.(int)$withCalc.(int)$withRating.VmLanguage::$currLangTag;

		if (array_key_exists ($productKey, self::$_products)) {
			//vmdebug('getProduct, take from cache : '.$productKey);
			return  array(true,$productKey);
		} else if(empty($withCalc) or empty($withRating)){


			//$productKeyTmp = $virtuemart_product_id.':'.$front.$onlyPublished.':'.$quantity.':'.$virtuemart_shoppergroup_ids.':'.TRUE.TRUE.VmLanguage::$currLangTag;
			//$productKeyTmp2 = $virtuemart_product_id.':'.$front.$onlyPublished.':'.$quantity.':'.$virtuemart_shoppergroup_ids.':'.TRUE.TRUE.VmLanguage::$currLangTag;
			$testKeys = array();
			if(empty($withCalc) and empty($withRating)){
				$testKeys[] = $virtuemart_product_id.':'.$front.$onlyPublished.':'.$quantity.':'.$virtuemart_shoppergroup_ids.':01'.VmLanguage::$currLangTag;
				$testKeys[] = $virtuemart_product_id.':'.$front.$onlyPublished.':'.$quantity.':'.$virtuemart_shoppergroup_ids.':10'.VmLanguage::$currLangTag;
				$testKeys[] = $virtuemart_product_id.':'.$front.$onlyPublished.':'.$quantity.':'.$virtuemart_shoppergroup_ids.':11'.VmLanguage::$currLangTag;
			} else if(empty($withCalc)){
				$testKeys[] = $virtuemart_product_id.':'.$front.$onlyPublished.':'.$quantity.':'.$virtuemart_shoppergroup_ids.':01'.VmLanguage::$currLangTag;
				$testKeys[] = $virtuemart_product_id.':'.$front.$onlyPublished.':'.$quantity.':'.$virtuemart_shoppergroup_ids.':11'.VmLanguage::$currLangTag;
			} else {
				$testKeys[] = $virtuemart_product_id.':'.$front.$onlyPublished.':'.$quantity.':'.$virtuemart_shoppergroup_ids.':10'.VmLanguage::$currLangTag;
				$testKeys[] = $virtuemart_product_id.':'.$front.$onlyPublished.':'.$quantity.':'.$virtuemart_shoppergroup_ids.':11'.VmLanguage::$currLangTag;
			}

			foreach($testKeys as $key){
				if (array_key_exists ($key,  self::$_products)) {
					//vmdebug('getProduct, take from cache full product '.$key.' instead '.$productKey);
					return  array(true,$key);
				}
			}
			//vmdebug('getProduct, no cached full product '.$key.' for '.$productKey);
			return  array(false,$productKey);
		} else {
			//vmdebug('getProduct, not cached '.$productKey);
			return array(false,$productKey);
		}
	}

	static $_products = array();
	static $_cacheOpt = array();
	static $_cacheOptSingle = array();
	static $_alreadyLoadedIds = array();
	static $omitLoaded = false;

	/**
	 * This function creates a product with the attributes of the parent.
	 *
	 * @param int     $virtuemart_product_id
	 * @param boolean $front for frontend use
	 * @param boolean $withCalc calculate prices?
	 * @param boolean published
	 * @param int quantity
	 * @param boolean load customfields
	 */
	public function getProduct ($virtuemart_product_id = NULL, $front = TRUE, $withCalc = TRUE, $onlyPublished = TRUE, $quantity = 1,$virtuemart_shoppergroup_ids = 0) {

		vmSetStartTime('getProduct');
		if (isset($virtuemart_product_id)) {
			$virtuemart_product_id = $this->setId ($virtuemart_product_id);
		}
		else {
			if (empty($this->_id)) {
				vmdebug('Can not return product with empty id');
				return FALSE;
			}
			else {
				$virtuemart_product_id = $this->_id;
			}
		}
		if(empty($quantity)) {
			vmTrace('getProduct Quanty empty');
			$quantity = 1;
		}
		if($virtuemart_shoppergroup_ids === 0){
			$virtuemart_shoppergroup_ids = self::getCurrentUserShopperGrps();
		}

		$checkedProductKey = self::checkIfCached($virtuemart_product_id, $front, $withCalc, $onlyPublished, $quantity, $virtuemart_shoppergroup_ids,$this->withRating);
		if($checkedProductKey[0]){

			if(self::$_products[$checkedProductKey[1]]===false){
				return false;
			} else if(is_object(self::$_products[$checkedProductKey[1]])){
				//vmTime('getProduct return cached clone','getProduct');
				//vmdebug('getProduct cached',self::$_products[$checkedProductKey[1]]->prices);

				return clone(self::$_products[$checkedProductKey[1]]);
			} else {
				vmdebug('getProduct cached self::$_products[$checkedProductKey[1] no object',self::$_products[$checkedProductKey[1]]);

			}
		}
		$productKey = $checkedProductKey[1];

		if ($this->memory_limit<$mem = memory_get_usage(FALSE)) {
			vmdebug ('Memory limit reached in model product getProduct('.$virtuemart_product_id.'), consumed: '.round($mem,2).'M');
			vmError ('Memory limit reached in model product getProduct() ' . $virtuemart_product_id);
			return false;
		}
		$child = $this->getProductSingle ($virtuemart_product_id, $front,$quantity,false,$virtuemart_shoppergroup_ids);

		if (!$child->published && $onlyPublished) {
			self::$_products[$productKey] = false;
			vmTime('getProduct return false, not published '.$virtuemart_product_id,'getProduct');
			return FALSE;
		}

		if(!isset($child->orderable)){
			$child->orderable = TRUE;
			$child->show_notify = false;
		}
		//store the original parent id
		$pId = $child->virtuemart_product_id;
		$ppId = $child->product_parent_id;
		$published = $child->published;

		$child->product_realparent_id = $child->product_parent_id;
		if(!empty($pId)){
			$child->allIds[] = $pId;
		} else {
			vmdebug('getProduct $pId empty ',$virtuemart_product_id,$pId);
		}

		$i = 0;
		$runtime = microtime (TRUE) - $this->starttime;
		//Check for all attributes to inherited by parent products
		while (!empty($child->product_parent_id)) {
			$runtime = microtime (TRUE) - $this->starttime;
			if ($runtime >= $this->maxScriptTime) {
				vmdebug ('Max execution time reached in model product getProduct() ', $child);
				vmError ('Max execution time reached in model product getProduct() ' . $child->product_parent_id);
				break;
			}
			else {
				if ($i > 10) {
					vmdebug ('Time: ' . $runtime . ' Too many child products in getProduct() ', $child);
					vmError ('Time: ' . $runtime . ' Too many child products in getProduct() ' . $child->product_parent_id);
					break;
				}
			}
			//$child->allIds[] = $child->product_parent_id;
			if(!empty($child->product_parent_id)) $child->allIds[] = $child->product_parent_id;

			$withPrice = true;
			if(isset($child->selectedPrice)){
				$withPrice = false;
			}
			$parentProduct = $this->getProductSingle ($child->product_parent_id, $front,$quantity, false, 0, $withPrice);
			if(!$parentProduct){
				$msg = 'Child product id '.$child->virtuemart_product_id. ' is missing parent product with $child->product_parent_id '.$child->product_parent_id;
				vmError($msg,$msg);
				break;
			}
			if ($child->product_parent_id === $parentProduct->product_parent_id) {
				vmError('Error, parent product with virtuemart_product_id = '.$parentProduct->virtuemart_product_id.' has same parent id like the child with virtuemart_product_id '.$child->virtuemart_product_id);
				vmTrace('Error, parent product with virtuemart_product_id = '.$parentProduct->virtuemart_product_id.' has same parent id like the child with virtuemart_product_id '.$child->virtuemart_product_id);
				break;
			}
			$attribs = get_object_vars ($parentProduct);

			foreach ($attribs as $k=> $v) {

				if (!property_exists($parentProduct, $k) or 'shared_stock' == $k or (!$child->shared_stock and ('product_in_stock' == $k or 'product_ordered' == $k))) {// Do not copy parent stock into child
					//vmdebug('Do not copy',$k);
					continue;
				}
				if('has_categories' == $k or 'has_manufacturers' == $k or 'has_medias' == $k or 'has_prices' == $k or 'has_shoppergroups' == $k){
					continue;
				}
				if (strpos ($k, '_') !== 0 and property_exists($child, $k) and empty($child->{$k})) {
					$child->{$k} = $v;
					//	vmdebug($child->product_parent_id.' $child->$k',$child->$k);
				}
			}
			$i++;
			if ($child->product_parent_id != $parentProduct->product_parent_id) {
				$child->product_parent_id = $parentProduct->product_parent_id;
			}
			else {
				$child->product_parent_id = 0;
			}

		}

		//$child->product_name = vRequest::vmHtmlEntities( $child->product_name);
		//vmdebug('getProduct Time: '.$runtime);
		$child->published = $published;
		$child->virtuemart_product_id = $pId;
		$child->product_parent_id = $ppId;

		if(!isset($child->selectedPrice) or empty($child->allPrices)){
			$child->selectedPrice = 0;
			$child->prices = $child->allPrices[$child->selectedPrice] = $this->fillVoidPrice();
		}

		$child->customfields = false;
		$customfieldsModel = VmModel::getModel ('Customfields');
		$child->modificatorSum = null;
		if(!empty($child->allIds)){
			$child->customfields = $customfieldsModel->getCustomEmbeddedProductCustomFields ($child->allIds,0,-1, FALSE);
		} else {
			vmTrace('Empty product allIds in getProduct? '. $virtuemart_product_id);
		}


		if ($withCalc) {

			if(VmConfig::isSite()){
				if($quantity < $child->min_order_level){
					$quantity = $child->min_order_level;
				}
			}

			$child->allPrices[$child->selectedPrice] = $this->getPrice ($child, $quantity);
		}
		$child->prices = $child->allPrices[$child->selectedPrice];

		/*if (empty($child->product_template)) {
			$child->product_template = VmConfig::get ('producttemplate');
		}*/

		if(!empty($child->canonCatId) ) {
			// Add the product link  for canonical
			$child->canonical = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id . '&virtuemart_category_id=' . $child->canonCatId;
		} else {
			$child->canonical = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id;
		}

		if(!empty($child->virtuemart_category_id)) {
			$child->link = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id . '&virtuemart_category_id=' . $child->virtuemart_category_id;
		} else {
			$child->link = $child->canonical;
		}

		$child->quantity = $quantity;
		$child->addToCartButton = false;
		if(empty($child->categories)) $child->categories = array();

		if($this->withRating){
			if(!isset($child->rating)){
				$ratings = $this->getTable('ratings');
				$ratings->load($virtuemart_product_id,'virtuemart_product_id');
				if($ratings->published){
					$child->rating = $ratings->rating;
				}
			}
		}

		$stockhandle = VmConfig::get('stockhandle_products', false) && $child->product_stockhandle ? $child->product_stockhandle : VmConfig::get('stockhandle', 'none');
		if ($front and $stockhandle == 'disableit' and ($child->product_in_stock - $child->product_ordered) <= 0) {
			vmdebug ('STOCK 0', VmConfig::get ('use_as_catalog', 0), VmConfig::get ('stockhandle', 'none'), $child->product_in_stock);
			self::$_products[$productKey] = false;
		} else {
			$product_available_date = substr($child->product_available_date,0,10);
			$current_date = date("Y-m-d");
			if (($child->product_in_stock - $child->product_ordered) < 1) {
				if ($product_available_date != '0000-00-00' and $current_date < $product_available_date) {
					$child->availability = vmText::_('COM_VIRTUEMART_PRODUCT_AVAILABLE_DATE') .': '. JHtml::_('date', $child->product_available_date, vmText::_('DATE_FORMAT_LC4'));
				} else if ($stockhandle == 'risetime' and VmConfig::get('rised_availability') and empty($child->product_availability)) {
					$child->availability =  (file_exists(VMPATH_ROOT .'/'. VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability'))) ? JHtml::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability', '7d.gif'), VmConfig::get('rised_availability', '7d.gif'), array('class' => 'availability')) : vmText::_(VmConfig::get('rised_availability'));

				} else if (!empty($child->product_availability)) {
					$child->availability = (file_exists(VMPATH_ROOT .'/'. VmConfig::get('assets_general_path') . 'images/availability/' . $child->product_availability)) ? JHtml::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . $child->product_availability, $child->product_availability, array('class' => 'availability')) : vmText::_($child->product_availability);
				}
			}
			else if ($product_available_date != '0000-00-00' and $current_date < $product_available_date) {
				$child->availability = vmText::_('COM_VIRTUEMART_PRODUCT_AVAILABLE_DATE') .': '. JHtml::_('date', $child->product_available_date, vmText::_('DATE_FORMAT_LC4'));
			}

			if ($child->min_order_level > 0) {
				$minOrderLevel = $child->min_order_level;
			} else {
				$minOrderLevel = 1;
			}

			if (($stockhandle == 'disableit' or $stockhandle == 'disableadd') and ($child->product_in_stock - $child->product_ordered) < $minOrderLevel) {
				$child->orderable = false;
				$child->show_notify = true;
			}

			foreach(self::$decimals as $decimal){
				if(empty($child->{$decimal})){
					$child->{$decimal} = 0.0;
				}
			}
			self::$_products[$productKey] = $child;
		}



		if(!self::$_products[$productKey]){
			return false;
		} else {
			//vmdebug('getProduct fresh',$child->customfields);
			//vmTime('getProduct loaded ','getProduct');
			return $child;//clone(self::$_products[$productKey]);
		}

	}

	public function loadProductPrices($productId,$virtuemart_shoppergroup_ids,$front){

		$db = JFactory::getDbo();
		if(!isset($this->_nullDate))$this->_nullDate = $db->getNullDate();
		if(!isset($this->_now)){

			$config = JFactory::getConfig();
			$siteOffset = $config->get('offset');
			$siteTimezone = new DateTimeZone($siteOffset);

			$jnow = JFactory::getDate();
			$date = new JDate($jnow);
			$date->setTimezone($siteTimezone);
			$this->_now = $date->format('Y-m-d H:i:s',true);
		}

		$q = 'SELECT * FROM `#__virtuemart_product_prices` WHERE `virtuemart_product_id` = "'.$productId.'" ';

		if($front){
			if($virtuemart_shoppergroup_ids and count($virtuemart_shoppergroup_ids)>0){
				$q .= ' AND (';
				$sqrpss = '';
				foreach($virtuemart_shoppergroup_ids as $sgrpId){
					$sqrpss .= ' `virtuemart_shoppergroup_id` ="'.$sgrpId.'" OR ';
				}

				$q .= $sqrpss.' `virtuemart_shoppergroup_id` IS NULL OR `virtuemart_shoppergroup_id`="0") ';
			}
			$q .= ' AND ( (`product_price_publish_up` IS NULL OR `product_price_publish_up` = "' . $db->escape($this->_nullDate) . '" OR `product_price_publish_up` <= "' .$db->escape($this->_now) . '" )
		        AND (`product_price_publish_down` IS NULL OR `product_price_publish_down` = "' .$db->escape($this->_nullDate) . '" OR product_price_publish_down >= "' . $db->escape($this->_now) . '" ) )';
		}

		$q .= ' ORDER BY `product_price` '.VmConfig::get('price_orderby','DESC');

		static $loadedProductPrices = array();
		$hash = $productId.','.implode('.',$virtuemart_shoppergroup_ids).','.(int)$front; //md5($q);

		if(!isset($loadedProductPrices[$hash])){
			$err = ''; 
			try {
				$db->setQuery($q);
				$prices = $db->loadAssocList();
			} catch(Exception $e) {
				$err = $e->getMessage(); 
			}
			
			if(!empty($err)){
				vmError('getProductSingle '.$err);
			} else {
				if(empty($prices)){
					$loadedProductPrices[$hash] = false;
				} else {
					$loadedProductPrices[$hash] = $prices ;
				}
			}
		}

		return $loadedProductPrices[$hash];
	}

	public function getRawProductPrices(&$product,$quantity,$virtuemart_shoppergroup_ids,$front,$withParent=0, $optimised = true){

		$productId = $product->virtuemart_product_id===0? $this->_id:$product->virtuemart_product_id;

		if(!$optimised or !isset($product->has_prices) or $product->has_prices){
			$product->allPrices = $this->loadProductPrices($productId,$virtuemart_shoppergroup_ids,$front);
			$product->has_prices = 0;
		} else {
			//$product->has_prices = 0;
			$product->allPrices = false;
		}

		$i = 0;
		$runtime = microtime (TRUE) - $this->starttime;
		$product_parent_id = $product->product_parent_id;
		//vmdebug('getRawProductPrices',$product->allPrices);
		//Check for all prices to inherited by parent products
		if( $withParent and !empty($product_parent_id)) {
			//vmdebug('getRawProductPrices load parent prices for '.$productId,$product_parent_id);
			while ( $product_parent_id and (empty($product->allPrices) or count($product->allPrices)==0) ) {
				$runtime = microtime (TRUE) - $this->starttime;
				if ($runtime >= $this->maxScriptTime) {
					vmdebug ('Max execution time reached in model product getProductPrices() ', $product);
					vmError ('Max execution time reached in model product getProductPrices() ' . $product->product_parent_id);
					break;
				}
				else {
					if ($i > 10) {
						vmdebug ('Time: ' . $runtime . ' Too many child products in getProductPrices() ', $product);
						vmError ('Time: ' . $runtime . ' Too many child products in getProductPrices() ' . $product->product_parent_id);
						break;
					}
				}
				$product->allPrices = $this->loadProductPrices($product_parent_id,$virtuemart_shoppergroup_ids,$front);
				$i++;

				if(!isset($product->allPrices['salesPrice']) and $product_parent_id!=0){
					$product_parent_id = $this->getProductParentId($product_parent_id);
				}
			}
		}

		$emptySpgrpPrice = 0;
		$pbC = VmConfig::get('pricesbyCurrency',false);
		if($front and $pbC){
			$app = JFactory::getApplication();

			$calculator = calculationHelper::getInstance();
			$cur = (int)$app->getUserStateFromRequest( 'virtuemart_currency_id', 'virtuemart_currency_id',$calculator->vendorCurrency );
			$emptySpgrpPrice = null;
		}

		$product->selectedPrice = null;
		if(!empty($product->allPrices) and is_array($product->allPrices)){

			$product->has_prices = count($product->allPrices);
			foreach($product->allPrices as $k=>$price){

				if(empty($price['price_quantity_start'])){
					$price['price_quantity_start'] = 0;
				}

				if(!empty($price['virtuemart_shoppergroup_id']) and !in_array($price['virtuemart_shoppergroup_id'],$virtuemart_shoppergroup_ids)){
					//vmdebug('Unset price, shoppergroup does not fit '.$k.' '.$price['virtuemart_shoppergroup_id'],$virtuemart_shoppergroup_ids);
					if($front) unset($product->allPrices[$k]);
					continue;
				}

				//This does not work correctly :-( , maybe someone could explain me
				//$quantityFits = (empty($price['price_quantity_end']) and $price['price_quantity_start'] <= $quantity) or ($price['price_quantity_start'] <= $quantity and $quantity <= $price['price_quantity_end']) ;
				$quantityFits = false;
				if(empty($price['price_quantity_end']) and $price['price_quantity_start'] <= $quantity){
					$quantityFits = true;
				} else if ($price['price_quantity_start'] <= $quantity and $quantity <= $price['price_quantity_end']) {
					$quantityFits = true;
				} else {
					$quantityFits = false;
				}

				$currency = true;
				if($front and $pbC==2){
					$currency = false;

					if($cur and $cur==$price['product_currency']){
						$currency = true;
						//$product->selectedPrice = $k;
						//break;
					}
				}

				if(empty($price['virtuemart_shoppergroup_id']) and empty($emptySpgrpPrice) and $quantityFits and $currency){
					$emptySpgrpPrice = $k;
					//vmdebug('Set default price',(int)$k);
				} else if( $quantityFits and $currency ){
					$product->selectedPrice = $k;
					//vmdebug('Set price by quantity/currency',(int)$k);
				}

				if($front and $pbC==1){
					if($cur and $cur==$price['product_currency']){
						$product->selectedPrice = $k;
						//vmdebug('Set price by currency',(int)$k);
						break;
					}
				}
			}

			if(!isset($product->selectedPrice) and isset($emptySpgrpPrice)){
				$product->selectedPrice = $emptySpgrpPrice;
			}

		}

	}

	static public function checkIfCachedSingle($virtuemart_product_id, $front = NULL, $quantity = 1, $withParent=false,$virtuemart_shoppergroup_ids=0, $prices = true){

		if(!isset($front) and isset(self::$_cacheOptSingle[$virtuemart_product_id])){
			$front = self::$_cacheOptSingle[$virtuemart_product_id]->front;
			$withParent = self::$_cacheOptSingle[$virtuemart_product_id]->withParent;
			$quantity = self::$_cacheOptSingle[$virtuemart_product_id]->quantity;
			$virtuemart_shoppergroup_ids = self::$_cacheOptSingle[$virtuemart_product_id]->virtuemart_shoppergroup_ids;
			$prices = self::$_cacheOptSingle[$virtuemart_product_id]->prices;
		} else {

			//$virtuemart_shoppergroup_ids = 0;
			if(is_array($virtuemart_shoppergroup_ids)){
				$virtuemart_shoppergroup_ids = implode('.',$virtuemart_shoppergroup_ids);
			}

			$front = $front?TRUE:0;
			$withParent = $withParent?TRUE:0;
			$prices = $prices?TRUE:0;
			$opt = new stdClass();
			$opt->virtuemart_product_id = (int)$virtuemart_product_id;
			$opt->front = $front;
			$opt->withParent = $withParent;
			$opt->quantity = (int)$quantity;
			$opt->virtuemart_shoppergroup_ids = $virtuemart_shoppergroup_ids;
			$opt->prices = $prices;
			self::$_cacheOptSingle[$virtuemart_product_id] = $opt;
		}


		$productKey = $virtuemart_product_id.':'.$virtuemart_shoppergroup_ids.':'.$quantity.':'.$front.':'.$prices.VmLanguage::$currLangTag;

		if (array_key_exists ($productKey, self::$_productsSingle)) {
			//vmdebug('getProduct, take from cache : '.$productKey);
			return  array(true,$productKey);
		/*} else if(!$withCalc){
			$productKeyTmp = $virtuemart_product_id.':'.$front.$onlyPublished.':'.$quantity.':'.$virtuemart_shoppergroup_ids.':'.TRUE.$withRating;
			if (array_key_exists ($productKeyTmp,  self::$_products)) {
				//vmdebug('getProduct, take from cache full product '.$productKeyTmp);
				return  array(true,$productKeyTmp);
			}*/
		} else {
			//vmdebug('getProduct, not cached '.$productKey);
			return array(false,$productKey);
		}
	}

	var $withRating = false;
	static $_productsSingle = array();

	public function getProductSingle ($virtuemart_product_id, $front = TRUE, $quantity = 1, $withParent=false,$virtuemart_shoppergroup_ids=0, $prices = true) {

		$virtuemart_product_id = $this->setId ($virtuemart_product_id);

		if($virtuemart_shoppergroup_ids===0){
			$virtuemart_shoppergroup_ids = self::getCurrentUserShopperGrps();
		}

		$checkedProductKey = $this->checkIfCachedSingle($virtuemart_product_id, $front, $quantity, $withParent, $virtuemart_shoppergroup_ids, $prices);
		if($checkedProductKey[0]){
			if(self::$_productsSingle[$checkedProductKey[1]]===false){
				return false;
			} else {
				//vmTime('getProduct return cached clone','getProduct');
				//vmdebug('getProduct cached',self::$_products[$checkedProductKey[1]]->prices);
				return clone(self::$_productsSingle[$checkedProductKey[1]]);
			}
		}
		$productKey = $checkedProductKey[1];

		if (!empty($this->_id)) {

			$product = $this->getTable ('products');

			$res = $product->load ($this->_id, 0, 0);//->loadFieldValues(false);


			if(!$res or (empty($product->virtuemart_vendor_id) and empty($product->slug))){

				self::$_productsSingle[$checkedProductKey[1]] = false;
				if(empty($product->slug)){
					$msg = 'Empty slug product with id '.$product->virtuemart_product_id.', entries exists for language? '.VmConfig::$vmlangTag;
					vmError($msg,$msg.' You may contact the administrator');
				} else {
					vmError('Could not find product with id '.$product->virtuemart_product_id.', still existing?');
				}

				//vmdebug('Product was not found',$product);
				$pr = $this->fillVoidProduct ($front, $virtuemart_product_id);
				return $pr;
			}

			$optimised = VmConfig::get('optimisedProductSql', true);
			$product->allIds = array();

			$storeHasMedias = false;
			$product->virtuemart_media_id = false;
			if(!$optimised or !isset($product->has_medias) or $product->has_medias){
				$xrefTable = $this->getTable ('product_medias');
				$product->virtuemart_media_id = $xrefTable->load ((int)$this->_id);
				//vmdebug('getProductSingle loaded media',$product->has_medias);
				if(!isset($product->has_medias)){
					$storeHasMedias = (int)!empty($product->virtuemart_media_id );
				}
			}

			// Load the shoppers the product is available to for Custom Shopper Visibility
			$storeHasShoppergroups = false;
			if(!$optimised or !isset($product->has_shoppergroups) or $product->has_shoppergroups){
				$product->shoppergroups = $this->getTable('product_shoppergroups')->load($this->_id);
				//vmdebug('getProductSingle loaded product_shoppergroups', $storeHasShoppergroups);
				if(!isset($product->has_shoppergroups)){
					$storeHasShoppergroups = (int)!empty($product->shoppergroups );
				}
			}

			if (!empty($product->shoppergroups) and $front) {
				$commonShpgrps = array_intersect ($virtuemart_shoppergroup_ids, $product->shoppergroups);
				if (empty($commonShpgrps)) {
					$pr = $this->fillVoidProduct ($front, $virtuemart_product_id);
					$pr->slug = $product->slug;
					$pr->access = false;
					return $pr;
				}
			}

			//We prestore the result, so we can directly load the product parent id by cache
			self::$_productsSingle[$productKey] = $product;

			$storeHasPrices = false;
			if($prices) {
				if(!isset($product->has_prices)){
					$storeHasPrices = 1;
				}
				$this->getRawProductPrices($product,$quantity,$virtuemart_shoppergroup_ids,$front,$withParent,$optimised);
			}

			$storeHasManufacturers = false;
			if(!$optimised or !isset($product->has_manufacturers) or $product->has_manufacturers){
				$product->virtuemart_manufacturer_id = $this->getTable('product_manufacturers')->load($this->_id);
				//vmdebug('getProductSingle loaded product_manufacturers',$product->has_manufacturers);
				if(!isset($product->has_manufacturers)){
					$storeHasManufacturers = (int)!empty($product->virtuemart_manufacturer_id );
				}
			}

			if (!empty($product->virtuemart_manufacturer_id[0])) {
				//This is a fallback
				$mfTable = $this->getTable ('manufacturers');
				$mfTable->load ((int)$product->virtuemart_manufacturer_id[0]);
				$product = (object)array_merge ((array)$mfTable, (array)$product);
			}
			else {
				$product->virtuemart_manufacturer_id = array();
				$product->mf_name = '';
				$product->mf_desc = '';
				$product->mf_url = '';
			}

			// Load the categories the product is in
			$storeHasCategories = false;
			if(!$optimised or !isset($product->has_categories) or $product->has_categories){
				$product->categoryItem = $this->getProductCategories ($this->_id); //We need also the unpublished categories, else the calculation rules do not work
				//vmdebug('getProductSingle loaded categories',$product->categoryItem);
				if(!isset($product->has_categories)){
					$storeHasCategories = (int)!empty($product->categoryItem );
				}
			}



			if($optimised and ($storeHasMedias!==false or $storeHasShoppergroups!==false or $storeHasManufacturers!==false or $storeHasCategories!==false or $storeHasPrices!==false)){


				$q = '';
				if($storeHasPrices!==false){
					$q .= ' `has_prices`='.(int)$storeHasPrices.',';
				}
				if($storeHasMedias!==false){
					$q .= ' `has_medias`='.$storeHasMedias.',';
				}
				if($storeHasShoppergroups!==false){
					$q .= ' `has_shoppergroups`='.$storeHasShoppergroups.',';
				}
				if($storeHasManufacturers!==false){
					$q .= ' `has_manufacturers`='.$storeHasManufacturers.',';
				}
				if($storeHasCategories!==false){
					$q .= ' `has_categories`='.$storeHasCategories.',';
				}
				//vmdebug('Update? product store HasXref '.$product->virtuemart_product_id,$q);
				if(!empty($q)){

					$q = rtrim($q,',');
vmSetStartTime('letsUpdateProducts');
					$db = JFactory::getDbo();
					$q = 'UPDATE #__virtuemart_products SET '.$q.' WHERE `virtuemart_product_id`='.$product->virtuemart_product_id.';';
					$db->setQuery($q);
					$res = $db->execute();vmdebug('Updated product store HasXref '.$product->virtuemart_product_id,$q);
					if(!$res){
						vmError('Could not update Product', 'Could not update Product with id '.$product->virtuemart_product_id.' still existing?');
					}
					vmTime('Updated product xref '.$product->virtuemart_product_id,'letsUpdateProducts');
				}

			}

			$product->canonCatId = false;
			$product->canonCatIdname = '';
			$public_cats = array();
			$product->categories = array();

			if(!empty($product->categoryItem)){
				$tmp = array();
				foreach($product->categoryItem as $category){
					if($category['published']){
						if(!$product->canonCatId) $product->canonCatId = $category['virtuemart_category_id'];
//					use a canonical category if published and a values is stored
						if (!empty($product->product_canon_category_id)  && $category['virtuemart_category_id'] == $product->product_canon_category_id ){
							$product->canonCatId = $product->product_canon_category_id;
							$product->canonCatIdname = $category['category_name'];
							//vmdebug('Canon cat found');
						}
						$public_cats[] = $category['virtuemart_category_id'];

					}
					$tmp[] = $category['virtuemart_category_id'];
				}
				$product->categories = $tmp;
			}



			if (!empty($product->categories) and is_array ($product->categories)){
				if ($front) {
					//We must first check if we come from another category, due the canoncial link we would have always the same catgory id for a product
					//But then we would have wrong neighbored products / category and product layouts
					if(!isset($this->categoryId)){
						static $menu = null;
						if(!isset($menu)){
							$app = JFactory::getApplication();
							$menus	= $app->getMenu('site');
							$this->Itemid = vRequest::getInt('Itemid',false);
							if ($this->Itemid ) {
								$menu = $menus->getItem($this->Itemid);
							} else {
								$menu = $menus->getActive();
							}
						}

						$this->categoryId = vRequest::getInt('virtuemart_category_id', 0);

						if(empty($this->categoryId)){
							if(!empty($menu->query['virtuemart_category_id'])){
								$this->categoryId = $menu->query['virtuemart_category_id'];
							} else {
								$this->categoryId = ShopFunctionsF::getLastVisitedCategoryId();
							}
						}
					}

					//$last_category_id = shopFunctionsF::getLastVisitedCategoryId ();
					if ($this->categoryId!==0 and in_array ($this->categoryId, $product->categories)) {
						$product->virtuemart_category_id = $this->categoryId;
					}

					if ($this->categoryId!==0 and $this->categoryId!=$product->canonCatId){
						if(in_array($this->categoryId,$public_cats)){
							$product->virtuemart_category_id = $this->categoryId;
						}
					}


				}
				//vmdebug('$product->virtuemart_category_id',$product->virtuemart_category_id);
				if(empty($product->virtuemart_category_id)){
					//$virtuemart_category_id = vRequest::getInt ('virtuemart_category_id', 0);
					if(is_array($this->virtuemart_category_id)){
						$virtuemart_category_id = reset($this->virtuemart_category_id);
					} else{
						$virtuemart_category_id = $this->virtuemart_category_id;
					}

				//quorvia if we are getting a product and we are in admin - we may be going back to a category list - but there may be no category_id from the URL -
				// we dont want the canon category setting we want the category we are going back to because the product ordering is screwed if we dont use that
					if(!$front and $this->virtuemart_category_id and $virtuemart_category_id==0){
						$virtuemart_category_id = $this->virtuemart_category_id;
					}
					if ($virtuemart_category_id!==0 and in_array ($virtuemart_category_id, $product->categories)) {
						$product->virtuemart_category_id = $virtuemart_category_id;
					} else if(!empty($product->canonCatId)) {
						$product->virtuemart_category_id = $product->canonCatId;
						//} else if (!$front and !empty($product->categories) and is_array ($product->categories) and array_key_exists (0, $product->categories)) {
						//why the restriction why we should use it for BE only?
					} else if (!empty($product->categories) and is_array ($product->categories) ) {
						$product->virtuemart_category_id = reset($product->categories);
						//vmdebug('I take for product the main category ',$product->virtuemart_category_id,$product->categories);
					}
				}
			}

			if(empty($product->virtuemart_category_id)) $product->virtuemart_category_id = $product->canonCatId;

			if(!empty($product->virtuemart_category_id)){

				$found = false;
				foreach($product->categoryItem as $category){

					if($category['virtuemart_category_id'] == $product->virtuemart_category_id){
						$product->ordering = $category['ordering'];
						//This is the ordering id in the list to store the ordering notice by Max Milbers
						$product->id = $category['id'];
						$product->category_name = $category['category_name'];
						$found = true;
						break;
					}
				}
				if(!$found){
					$product->ordering = $this->_autoOrder++;
					$product->id = $this->_autoOrder;
					vmdebug('$product->virtuemart_category_id no ordering stored for product '.$this->_id);
				}

			} else {
				$product->category_name = '';
				$product->virtuemart_category_id = '';
				$product->ordering = '';
				$product->id = $this->_autoOrder++;
			}

			if($product->shared_stock){
				$prT = $this->getTable ('products');
				$parent = $prT->load ($product->product_parent_id, 0, 0);
				$product->product_in_stock = $parent->product_in_stock;
				$product->product_ordered = $parent->product_ordered;
			}
			// Check the stock level
			if (empty($product->product_in_stock)) {
				$product->product_in_stock = 0;
			}

			self::$_productsSingle[$productKey] = $product;
		}
		else {
			self::$_productsSingle[$productKey] = $this->fillVoidProduct ($front);
		}

		return clone(self::$_productsSingle[$productKey]);
	}

	/**
	 * This fills the empty properties of a product
	 * todo add if(!empty statements
	 *
	 * @author Max Milbers
	 * @param unknown_type $product
	 * @param unknown_type $front
	 */
	private function fillVoidProduct ($front = TRUE, $productId = 0) {

		/* Load an empty product */
		$product = $this->getTable ('products');
		$product->reset();
		$product->load ();

		$product->virtuemart_product_id = $productId;
		/* Add optional fields */
		$product->virtuemart_manufacturer_id = NULL;
		$product->virtuemart_product_price_id = NULL;
		$product->virtuemart_category_id = 0;
		$product->allPrices[0] = $this->fillVoidPrice();
		$product->categories = array();
		$product->canonCatId = '';
		$product->allIds = array();
		if ($front) {
			$product->link = '';
			$product->virtuemart_shoppergroup_id = 0;
			$product->mf_name = '';
			$product->packaging = '';
			$product->related = '';
			$product->box = '';
			$product->addToCartButton = false;
		}
		$product->virtuemart_vendor_id = vmAccess::isSuperVendor();

		return $product;
	}

	public function fillVoidPrice(){

		$prices = array();
		$prices['product_price'] = '';
		$prices['virtuemart_product_price_id'] = 0;
		$prices['product_currency'] = null;
		$prices['price_quantity_start'] = null;
		$prices['price_quantity_end'] = null;
		$prices['product_price_publish_up'] = null;
		$prices['product_price_publish_down'] = null;
		$prices['product_tax_id'] = 0;
		$prices['product_discount_id'] = null;
		$prices['product_override_price'] = null;
		$prices['override'] = null;
		$prices['categories'] = array();
		$prices['shoppergroups'] = array();
		$prices['virtuemart_shoppergroup_id'] = null;

		return $prices;
	}

	/**
	 * Load  the product category
	 *
	 * @author Max Milbers
	 * @return array list of categories product is in
	 */
	public function getProductCategories ($virtuemart_product_id) {

		static $prodCats = array();

		if(empty($virtuemart_product_id)) return false;

		if(!isset($prodCats[$virtuemart_product_id])){

			$categories = array();
			$categoryIds = self::getProductCategoryIds($virtuemart_product_id);
			$catTable = $this->getTable('categories');

			foreach($categoryIds as $categoryId){
				$tmp = $catTable->load($categoryId['virtuemart_category_id'])->loadFieldValues();
				$tmp['id'] = $categoryId['id'];
				$tmp['ordering'] = $categoryId['ordering'];
				$categories[] = $tmp;
			}
			$prodCats[$virtuemart_product_id] = $categories;
		}

		return $prodCats[$virtuemart_product_id];
	}

	static public function getProductCategoryIds ($id) {

		static $c = array();
		if(!isset($c[$id])){
			$db = JFactory::getDbo();

			$q = 'SELECT * FROM `#__virtuemart_product_categories`  WHERE `virtuemart_product_id` = ' . (int)$id;
			$db->setQuery ($q);
			$c[$id] = $db->loadAssocList();
		}
		return $c[$id];
	}

	/**
	 * Get the products in a given category
	 *
	 * @access public
	 * @param int $virtuemart_category_id the category ID where to get the products for
	 * @return array containing product objects
	 * @deprecated
	 */
	public function getProductsInCategory ($categoryId) {

		$ids = $this->sortSearchListQuery (TRUE, $categoryId);
		$this->products = $this->getProducts ($ids);
		return $this->products;
	}


	/**
	 * Loads different kind of product lists.
	 * you can load them with calculation or only published onces, very intersting is the loading of groups
	 * valid values are latest, topten, featured, recent.
	 *
	 * The function checks itself by the config if the user is allowed to see the price or published products
	 *
	 * @author Max Milbers
	 */
	public function getProductListing ($group = FALSE, $nbrReturnProducts = FALSE, $withCalc = TRUE, $onlyPublished = TRUE, $single = FALSE, $filterCategory = TRUE, $category_id = 0, $filterManufacturer = TRUE, $manufacturer_id = 0) {

		$ids = array();
		if (VmConfig::isSite()) {
			$front = TRUE;
			if (!vmAccess::manager()) {
				$onlyPublished = TRUE;
				$withCalc = (int)VmConfig::get ('show_prices', 1);
			}
		}
		else {
			$front = FALSE;
		}

		$this->setFilter ();
		if ($filterCategory) {
			if ($category_id) {
				$this->virtuemart_category_id = $category_id;
			}
		}
		else {
			$this->virtuemart_category_id = FALSE;
		}

		if ($filterManufacturer) {
			if ($manufacturer_id) {
				$this->virtuemart_manufacturer_id = $manufacturer_id;
			}
		}
		else {
			$this->virtuemart_manufacturer_id = FALSE;
		}
		if($group == 'recent'){
			$ids = self::getRecentProductIds($nbrReturnProducts);	// get recent viewed from browser session
		} else {
			if($group){
				$params = array('searchcustoms'=>false,'virtuemart_custom_id'=>false, 'keyword' =>false);
			} else {
				$params = array();
			}
			$ids = $this->sortSearchListQuery ($onlyPublished, $this->virtuemart_category_id, $group, $nbrReturnProducts, $params );
			if($ids){
				self::$_alreadyLoadedIds = array_merge(self::$_alreadyLoadedIds,$ids);
			}
		}

		//quickndirty hack for the BE list
		$this->listing = TRUE;
		$products = $this->getProducts ($ids, $front, $withCalc, $onlyPublished, $single);
		$this->listing = FALSE;

		return $products;
	}

	static public function getProductsListing ($group = FALSE, $nbrReturnProducts = FALSE, $withCalc = TRUE, $onlyPublished = TRUE, $single = FALSE, $filterCategory = TRUE, $category_id = 0, $filterManufacturer = TRUE, $manufacturer_id = 0, $omit = 0) {

		$productModel = VmModel::getModel('Product');
		VirtueMartModelProduct::$omitLoaded = $omit;
		$productModel->_withCount = false;
		$products = $productModel->getProductListing($group, $nbrReturnProducts, $withCalc, $onlyPublished, $single, $filterCategory, $category_id, $filterManufacturer, $manufacturer_id);//*/

		return $products;
	}

	/**
	 * overriden getFilter to persist filters
	 *
	 * @author OSP
	 */
	public function setFilter () {

		if (!VmConfig::isSite ()) { //persisted filter only in admin
			$view = vRequest::getCmd ('view');
			$mainframe = JFactory::getApplication ();
			$this->virtuemart_category_id = $mainframe->getUserStateFromRequest ('com_virtuemart.' . $view . '.filter.virtuemart_category_id', 'virtuemart_category_id', 0, 'int');
			$this->setState ('virtuemart_category_id', $this->virtuemart_category_id);
			$this->virtuemart_manufacturer_id = $mainframe->getUserStateFromRequest ('com_virtuemart.' . $view . '.filter.virtuemart_manufacturer_id', 'virtuemart_manufacturer_id', 0, 'int');
			$this->setState ('virtuemart_manufacturer_id', $this->virtuemart_manufacturer_id);
		}
		else {
			$this->virtuemart_category_id = vRequest::getInt ('virtuemart_category_id', FALSE);
		}
	}

	/**
	 * Returns products for given array of ids
	 *
	 * @author Max Milbers
	 * @param int $productIds
	 * @param boolean $front
	 * @param boolean $withCalc
	 * @param boolean $onlyPublished
	 */
	public function getProducts ($productIds, $front = TRUE, $withCalc = TRUE, $onlyPublished = TRUE, $single = FALSE) {

		if (empty($productIds)) {
			return array();
		}

		$maxNumber = $this->_maxItems;
		$products = array();
		$i = 0;
		if ($single) {

			foreach ($productIds as $id) {

				if ($product = $this->getProductSingle ((int)$id, $front,1,false)) {
					$products[] = $product;
					$i++;
				}
				if ($i > $maxNumber) {
					vmdebug ('Better not to display more than ' . $maxNumber . ' products');
					return $products;
				}
			}
		}
		else {

			foreach ($productIds as $id) {
				if ($product = $this->getProduct ((int)$id, $front, $withCalc, $onlyPublished,1)) {
					$products[] = $product;
					$i++;
				}
				if ($i > $maxNumber) {
					vmdebug ('Better not to display more than ' . $maxNumber . ' products');
					break;
				}
			}
		}

		//GJC	test if cat deep search
		if(VmConfig::get('deep_cat',false)){

			$set_categoryId = vRequest::getInt('virtuemart_category_id', -1);
			$cat_deep_search = true;
			if($cat_deep_search && $set_categoryId != -1 ){
				foreach ($products as $product) {

					$catmodel = VmModel::getModel ('category');
					$childcats = $catmodel->getChildCategoryList(1, $set_categoryId,null, null, true);
					$testcats = array($set_categoryId);
					foreach($childcats as $childcat) {
						$testcats[] = $childcat->virtuemart_category_id;
					}
					foreach($product->categoryItem as $catItem){
						$product_categories[] = $catItem['virtuemart_category_id'];
					}
					$found_cat = array_intersect ($testcats, $product_categories);
					$found_cat = array_values($found_cat);
					if(!empty($found_cat[0])) {
						$product->link = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id  . '&virtuemart_category_id=' . $found_cat[0];
					}
				}
			}
		}


		return $products;
	}


	/**
	 * This function retrieves the "neighbor" products of a product specified by $virtuemart_product_id
	 * Neighbors are the previous and next product in the current list
	 *
	 * @author Max Milbers
	 * @param object $product The product to find the neighours of
	 * @return array
	 */
	public function getNeighborProducts ($product, $onlyPublished = TRUE, $max = 1) {

		$db = JFactory::getDBO ();
		$neighbors = array('previous' => '', 'next' => '');

		$oldDir = $this->filter_order_Dir;


		if($this->filter_order_Dir=='ASC'){
			$direction = 'DESC';
			$op = '<=';
		} else {
			$direction = 'ASC';
			$op = '>=';
		}
		$this->filter_order_Dir = $direction;

		//We try the method to get exact the next product, the other method would be to get the list of the browse view again and do a match
		//with the product id and giving back the neighbours
		$this->_onlyQuery = true;
		$queryArray =  $this->sortSearchListQuery($onlyPublished,(int)$product->virtuemart_category_id,false,1,array('langFields'=>array('product_name')));
//vmdebug('my query stuff ',$queryArray);
		if(isset($queryArray[1])){

			$pos= strpos($queryArray[3],'ORDER BY');
			$sp = array();

			$orderByName = '`l`.product_name, virtuemart_product_id';
			$whereorderByName = '`l`.product_name';
			$orderByValue = $product->product_name;
			//$orderByValue = $db->escape($product->product_name);
			if($pos){
				$orderByName = trim(substr ($queryArray[3],($pos+8)) );

				$orderByNameMain = $orderByName;
				if($cpos = strpos($orderByName,',')!==false){
					$t = explode(',',$orderByName);
					if(!empty($t[0])){
						$orderByNameMain = $t[0];
					}
				}
				$orderByNameMain = str_replace(array('DESC','ASC'), '',$orderByNameMain);
				$orderByNameMain = trim(str_replace('`','',$orderByNameMain));

				if($orderByNameMain=='product_price'){
					if(isset($product->prices['product_price'])){
						$product->product_price = $product->prices['product_price'];
					} else {
						$product->product_price = 0.0;
					}
				}

				if(strpos($orderByNameMain,'.')){
					$sp = explode('.',$orderByNameMain);
					$orderByNameMain = $sp[count($sp)-1];
				}

				$tableLangKeys = array('product_name','product_s_desc','product_desc');
				if(isset($product->{$orderByNameMain})){
					$orderByValue = $product->{$orderByNameMain};
					if(isset($sp[0])){
						$orderByNameMain = '`'.$sp[0].'`.'.$orderByNameMain;
					} else if(in_array($orderByNameMain,$tableLangKeys)){
						$orderByNameMain = '`l`.'.$orderByNameMain;
					}
				}
				$whereorderByName = $orderByNameMain;
			}

			$selectLang = ' `l`.`product_name`';

			$q = 'SELECT p.`virtuemart_product_id`,'.$selectLang.','.$whereorderByName.' FROM `#__virtuemart_products` as p';

			$joinT = '';
			if(is_array($queryArray[1])){
				$joinT = implode('',$queryArray[1]);
			}

			/*if(strpos($orderByName,'virtuemart_product_id')!==false){
				$q .= $joinT . ' WHERE (' . implode (' AND ', $queryArray[2]) . ') AND p.`virtuemart_product_id`'.$op.'"'.$product->virtuemart_product_id.'" ';
			} else {*/
			$q .= $joinT . ' WHERE (' . implode (' AND ', $queryArray[2]) . ') AND p.`virtuemart_product_id`!="'.$product->virtuemart_product_id.'" ';
			//}


			$alreadyFound = '';
			foreach ($neighbors as &$neighbor) {

				if(!empty($alreadyFound)) $alreadyFound = 'AND p.`virtuemart_product_id`!="'.$alreadyFound.'"';
				$qm = $alreadyFound.' AND '.$whereorderByName.' '.$op.' "'.$db->escape($orderByValue).'"  ORDER BY '.$orderByName.' LIMIT 1';
				$db->setQuery ($q.$qm);
				//vmdebug('getneighbors '.$q.$qm);
				if ($result = $db->loadAssocList ()) {
					$neighbor = $result;
					$alreadyFound = $result[0]['virtuemart_product_id'];
				}

				if($this->filter_order_Dir=='ASC'){
					$direction = 'DESC';
					$op = '<=';

				} else {
					$direction = 'ASC';
					$op = '>=';
				}
				$orderByName = str_replace($this->filter_order_Dir,$direction,$orderByName);
			}
		}

		$this->filter_order_Dir = $oldDir;
		$this->_onlyQuery = false;
		return $neighbors;
	}

	/**
	 * @param array $cid
	 * @param $order
	 * @param null $filter
	 * QUORVIA save the product display sequence in the product list table
	 * do not change the numbers passed from the list
	 * @return bool
	 *
	 * @throws Exception
	 * @since version	 */
//	 quorvia created this function because old save order may have an issue
	function saveorder ($cid, $order, $filter = NULL) {
		vRequest::vmCheckToken();
		$virtuemart_category_id = vRequest::getInt ('virtuemart_category_id', 0);
		if(is_array($virtuemart_category_id)) $virtuemart_category_id = reset($virtuemart_category_id);

//		quorvia if no category could be found do not update anything for sequence
		if ($virtuemart_category_id) {

			$updated = 0;
			$db = JFactory::getDbo();
			foreach( $order as $prod => $ord ) {
				//dont increment - just use the values supplied  but make a positive int
				$ordering = abs( (int)$ord);
				if(empty($ordering)) $ordering = "0";
				$product_id = (int)$prod;
				if(!empty( $product_id )) {
					$qupdate = 'UPDATE `#__virtuemart_product_categories` SET `ordering` =  '.$ordering.'  WHERE `virtuemart_product_id` =  '.$product_id.'  AND `virtuemart_category_id` = '.$virtuemart_category_id;

					$db->setQuery( $qupdate );
					try {
						if(!$db->execute()) {
						
							return FALSE;
						}
					} catch (Exception $e) {
						vmError( $e->getMessage() );
						return false; 
					}
				}
				$updated++;
			}
			vmInfo( 'COM_VIRTUEMART_ITEMS_MOVED', $updated );
		} else {
			vmWarn ('There is no category_id');
		}

		JFactory::getApplication()->redirect( 'index.php?option=com_virtuemart&view=product&virtuemart_category_id='.$virtuemart_category_id );
	}


	/**
	 * Moves the order of a record
	 *
	 * @param integer The increment to reorder by
	 */
	function move ($direction, $filter = NULL) {

		vRequest::vmCheckToken();
		$virtuemart_category_id = vRequest::getInt ('virtuemart_category_id', 0);
		if(is_array($virtuemart_category_id)) $virtuemart_category_id = reset($virtuemart_category_id);
		if(empty($virtuemart_category_id)) return;

		$virtuemart_product_id = vRequest::getInt ('virtuemart_product_id', 0);
		if(is_array($virtuemart_product_id)) $virtuemart_product_id = reset($virtuemart_product_id);
		if(empty($virtuemart_product_id)) return;

		// Check for request forgeries
		$table = $this->getTable ('product_categories');
		$table->load($virtuemart_product_id, 0, ' AND virtuemart_category_id = '.$virtuemart_category_id);
		$table->virtuemart_category_id = $virtuemart_category_id;

		$table->move ($direction,' virtuemart_category_id = '.$virtuemart_category_id);

		JFactory::getApplication ()->redirect ('index.php?option=com_virtuemart&view=product&virtuemart_category_id='.$virtuemart_category_id);
	}

	/**
	 * Store a product
	 *
	 * @author Max Milbers
	 * @param $product reference
	 * @param bool $isChild Means not that the product is child or not. It means if the product should be threated as child
	 * @return bool
	 */
	public function store (&$data) {

		vRequest::vmCheckToken();

		if(!vmAccess::manager('product.edit')){
			vmError('You are not a vendor or administrator, storing of product cancelled');
			return FALSE;
		}

		if ($data and is_object($data)) {
			$data = get_object_vars($data);
		}

		$isChild = FALSE;
		if(!empty($data['isChild'])) $isChild = $data['isChild'];

		if (isset($data['intnotes'])) {
			$data['intnotes'] = trim ($data['intnotes']);
		}

		// Setup some place holders
		$product_data = $this->getTable ('products');

		$data['new'] = '1';
		if(!empty($data['virtuemart_product_id'])){
			$product_data -> load($data['virtuemart_product_id']);
			$data['new'] = '0';
		}
		if( (empty($data['virtuemart_product_id']) or empty($product_data->virtuemart_product_id)) and !vmAccess::manager('product.create')){
			vmWarn('Insufficient permission to create product');
			return false;
		}

		$vendorId = vmAccess::isSuperVendor();
		$vM = VmModel::getModel('vendor');
		$ven = $vM->getVendor($vendorId);

		if(VmConfig::get('multix','none')!='none' and !vmAccess::manager('core')){

			if($ven->max_products!=-1){
				$this->setGetCount (true);
				//$this->setDebugSql(true);
				parent::exeSortSearchListQuery(2,'virtuemart_product_id',' FROM #__virtuemart_products',' WHERE ( `virtuemart_vendor_id` = "'.$vendorId.'" AND `published`="1") ');
				$this->setGetCount (false);
				if($ven->max_products<($this->_total+1)){
					vmWarn('You are not allowed to create more than '.$ven->max_products.' products');
					return false;
				}
			}
		}

		if(!vmAccess::manager('product.edit.state')){
			if( (empty($data['virtuemart_product_id']) or empty($product_data->virtuemart_product_id))){
				$data['published'] = 0;
			} else {
				$data['published'] = $product_data->published;
			}
		}

		//Set the decimals like product packaging
		foreach(self::$decimals as $decimal){
			if (array_key_exists ($decimal, $data)) {
				if(!empty($data[$decimal])){
					$data[$decimal] = str_replace(',','.',$data[$decimal]);
					//vmdebug('Store product '.$data['virtuemart_product_id'].', set $decimal '.$decimal.' = '.$data[$decimal]);
				} else {
					$data[$decimal] = null;
					$product_data->{$decimal} = null;
					//vmdebug('Store product '.$data['virtuemart_product_id'].', set $decimal '.$decimal.' = null');
				}
			}
		}

		if($ven->force_product_pattern>0 and empty($data['product_parent_id']) and $ven->force_product_pattern!=$data['virtuemart_product_id']){
			$data['product_parent_id'] = $ven->force_product_pattern;
		}

		//We prevent with this line, that someone is storing a product as its own parent
		if(!empty($data['product_parent_id']) and !empty($data['virtuemart_product_id']) and $data['product_parent_id'] == $data['virtuemart_product_id']){
			$data['product_parent_id'] = 0;
		}

		$product_data->has_prices = (isset($data['mprices']['product_price']) and count($data['mprices']['product_price']) > 0)? 1:0;

		if (!$isChild) {
			$product_data->has_shoppergroups = empty($data['virtuemart_shoppergroup_id'])? 0:1;
			$product_data->has_manufacturers = empty($data['virtuemart_manufacturer_id'])? 0:1;
			//$product_data->has_medias = !empty($data['virtuemart_media_id']) or !empty($data['media']['virtuemart_media_id'])? 1:0;
			$product_data->has_categories = empty($data['categories'])? 0:1;
			if(!empty($data['virtuemart_media_id']) or !empty($data['media']['virtuemart_media_id']) or !empty($data['media']['media_action'])){
				$product_data->has_medias = 1;
			} else {
				$product_data->has_medias = 0;
			}
		}


		vDispatcher::importVMPlugins('vmcustom');
		vDispatcher::trigger('plgVmBeforeStoreProduct',array(&$data, &$product_data));

		$stored = $product_data->bindChecknStore ($data, false);

		if(!$stored ){
			vmError('You are not an administrator or the correct vendor, storing of product cancelled');
			return FALSE;
		}

		$this->_id = $data['virtuemart_product_id'] = (int)$product_data->virtuemart_product_id;

		if (empty($this->_id)) {
			vmError('Product not stored, no id');
			return FALSE;
		}

		//We may need to change this, the reason it is not in the other list of commands for parents
		if (!$isChild) {
			$modelCustomfields = VmModel::getModel ('Customfields');
			$modelCustomfields->storeProductCustomfields ('product', $data, $product_data->virtuemart_product_id);
		}

		// Get old IDS
		$old_price_ids = $this->loadProductPrices($this->_id,array(0),false);

		if (isset($data['mprices']['product_price']) and count($data['mprices']['product_price']) > 0){



			foreach($data['mprices']['product_price'] as $k => $product_price){

				$pricesToStore = array();
				$pricesToStore['virtuemart_product_id'] = $this->_id;
				$pricesToStore['virtuemart_product_price_id'] = (int)$data['mprices']['virtuemart_product_price_id'][$k];

				if (!$isChild){
					//$pricesToStore['basePrice'] = $data['mprices']['basePrice'][$k];
					$pricesToStore['product_override_price'] = $data['mprices']['product_override_price'][$k];
					$pricesToStore['override'] = isset($data['mprices']['override'][$k])?(int)$data['mprices']['override'][$k]:0;
					$pricesToStore['virtuemart_shoppergroup_id'] = (int)$data['mprices']['virtuemart_shoppergroup_id'][$k];
					$pricesToStore['product_tax_id'] = (int)$data['mprices']['product_tax_id'][$k];
					$pricesToStore['product_discount_id'] = (int)$data['mprices']['product_discount_id'][$k];
					$pricesToStore['product_currency'] = (int)$data['mprices']['product_currency'][$k];
					$pricesToStore['product_price_publish_up'] = $data['mprices']['product_price_publish_up'][$k];
					$pricesToStore['product_price_publish_down'] = $data['mprices']['product_price_publish_down'][$k];
					$pricesToStore['price_quantity_start'] = (int)$data['mprices']['price_quantity_start'][$k];
					$pricesToStore['price_quantity_end'] = (int)$data['mprices']['price_quantity_end'][$k];
				}

				if (!$isChild and isset($data['mprices']['use_desired_price'][$k]) and $data['mprices']['use_desired_price'][$k] == "1") {

					$calculator = calculationHelper::getInstance ();
					if(isset($data['mprices']['salesPrice'][$k])){
						$data['mprices']['salesPrice'][$k] = str_replace(array(',',' '),array('.',''),$data['mprices']['salesPrice'][$k]);
					}
					$pricesToStore['salesPrice'] = $data['mprices']['salesPrice'][$k];
					$pricesToStore['product_price'] = $data['mprices']['product_price'][$k] = $calculator->calculateCostprice ($this->_id, $pricesToStore);
					unset($data['mprices']['use_desired_price'][$k]);
				} else {
					if(isset($data['mprices']['product_price'][$k]) ){
						$pricesToStore['product_price'] = $data['mprices']['product_price'][$k];
					}

				}

				if ($isChild) $childPrices = $this->loadProductPrices($this->_id,array(0),false);

				if ((isset($pricesToStore['product_price']) and $pricesToStore['product_price']!='' and $pricesToStore['product_price']!=='0') || (isset($childPrices) and count($childPrices)>1)) {

					if ($isChild) {

						if(is_array($old_price_ids) and count($old_price_ids)>1){

							//We do not touch multiple child prices. Because in the parent list, we see no price, the gui is
							//missing to reflect the information properly.
							$pricesToStore = false;
							$old_price_ids = array();
						} else {
							unset($data['mprices']['product_override_price'][$k]);
							unset($pricesToStore['product_override_price']);
							unset($data['mprices']['override'][$k]);
							unset($pricesToStore['override']);
						}

					}

					if($pricesToStore){
						$toUnset = array();
						if (!empty($old_price_ids) and count($old_price_ids) ) {
							foreach($old_price_ids as $key => $oldprice){
								if($pricesToStore['virtuemart_product_price_id'] == $oldprice['virtuemart_product_price_id'] ){
									$pricesToStore = array_merge($oldprice,$pricesToStore);
									$toUnset[] = $key;
								}
							}
						}
						$this->updateXrefAndChildTables ($pricesToStore, 'product_prices',$isChild);

						foreach($toUnset as $key){
							unset( $old_price_ids[ $key ] );
						}
					}
				}
			}
		}

		if (!empty($old_price_ids) and count($old_price_ids) ) {
			$oldPriceIdsSql = array();
			foreach($old_price_ids as $oldPride){
				$oldPriceIdsSql[] = $oldPride['virtuemart_product_price_id'];
			}
			$db = JFactory::getDbo();
			// delete old unused Prices
			$db->setQuery( 'DELETE FROM `#__virtuemart_product_prices` WHERE `virtuemart_product_price_id` in ("'.implode('","', $oldPriceIdsSql ).'") ');
			$err = ''; 
			try {
				$db->execute();
			} catch(Exception $e) {
				$err = $e->getMessage();
			}
			
			if(!empty($err)){
				vmWarn('In store prodcut, deleting old price error',$err);
			}
		}

		if (!empty($data['childs'])) {
			foreach ($data['childs'] as $productId => $child) {
				if(empty($productId)) continue;
				if($productId!=$data['virtuemart_product_id']){

					if(empty($child['product_parent_id'])) $child['product_parent_id'] = $data['virtuemart_product_id'];
					$child['virtuemart_product_id'] = $productId;

					if(!empty($child['product_parent_id']) and $child['product_parent_id'] == $child['virtuemart_product_id']){
						$child['product_parent_id'] = 0;
					}

					$child['isChild'] = $this->_id;
					$this->store ($child);
				}
			}
		}

		if (!$isChild) {

			$data = $this->updateXrefAndChildTables ($data, 'product_shoppergroups');

			$data = $this->updateXrefAndChildTables ($data, 'product_manufacturers');

			$storeCats = false;
			if (empty($data['categories']) or (!empty($data['categories'][0]) and $data['categories'][0]!="-2")){
				$storeCats = true;
			}

			if($storeCats){
				if (!empty($data['categories']) && count ($data['categories']) > 0) {
					if(VmConfig::get('multix','none')!='none' and !vmAccess::manager('managevendors')){

						if($ven->max_cats_per_product>=0){
							while($ven->max_cats_per_product<count($data['categories'])){
								array_pop($data['categories']);
							}
						}

					}
					$data['virtuemart_category_id'] = $data['categories'];
				} else {
					$data['virtuemart_category_id'] = array();
				}
				$data = $this->updateXrefAndChildTables ($data, 'product_categories');
			}

			// Update waiting list
			if (!empty($data['notify_users'])) {
				if ($data['product_in_stock'] > 0 && $data['notify_users'] == '1') {
					$waitinglist = VmModel::getModel ('Waitinglist');
					$waitinglist->notifyList ($data['virtuemart_product_id']);
				}
			}

			// Process the images
			$mediaModel = VmModel::getModel ('Media');
			$mediaModel->storeMedia ($data, 'product');

		}

		$cache = VmConfig::getCache('com_virtuemart_orderby_manus','callback');
		$cache->clean();

		vDispatcher::trigger('plgVmAfterStoreProduct',array(&$data, &$product_data));

		return $product_data->virtuemart_product_id;
	}

	public function updateXrefAndChildTables ($data, $tableName, $preload = FALSE) {

		vRequest::vmCheckToken();
		//First we load the xref table, to get the old data
		$product_table_Parent = $this->getTable ($tableName);
		//We must go that way, because the load function of the vmtablexarry
		// is working different.
		if($preload){
			$product_table_Parent->load($data['virtuemart_product_id']);
		}
		$product_table_Parent->bindChecknStoreNoLang ($data);

		return $data;

	}

	/**
	 * This function creates a child for a given product id
	 *
	 * @author Max Milbers
	 * @param int id of parent id
	 */
	public function createChild ($id) {

		if(!vmAccess::manager('product.create')){
			vmWarn('Insufficient permission to create product');
			return false;
		}

		$prodTable = $this->getTable ('products');

		$childs = $this->getProductChildIds ($id);
		vmdebug('createChild my $childs',$childs);
		if($childs){
			$lastCId = end($childs);
			reset($childs);
			if(!empty($lastCId)){
				$prodTable->load($lastCId);
			}

		} else {
			$prodTable->load($id);
			//$prodTable->slug = $prodTable->product_name;
		}

		$data = array('product_name' => $prodTable->product_name, 'slug' => $prodTable->slug, 'virtuemart_vendor_id' => (int)$prodTable->virtuemart_vendor_id, 'product_parent_id' => (int)$id);
		//$prodParentTable = $this->getTable ('products');
		$prodTable->reset();
		$prodTable->emptyCache();
		vmdebug('createChild my table',$data);
		$ok = $prodTable->bindChecknStore ($data);
		if(empty($ok)){
			return false;
		} else {
			$newId = $prodTable->virtuemart_product_id;
		}

		//$prodTable = $this->getTable ('products');
		$slug = $prodTable->slug;
		$langs = VmConfig::get('active_languages', array(VmConfig::$jDefLangTag));
		vmdebug('my langs',$langs);
		if ($langs and count($langs)>0){
			foreach($langs as $lang){
				if($lang==VmConfig::$vmlangTag) continue;
				$prodTable->reset();
				$prodTable->emptyCache();
				$prodTable->setLanguage($lang);
				//Disables the language fallback
				$prodTable->_ltmp = true;
				$prodTable->load($id);

				if($prodTable->_loaded and !$prodTable->_loadedWithLangFallback){
					$prodTable->virtuemart_product_id = $newId;
					//$prodTable->slug = $slug . '-' . $newId;
					$prodTable->checkCreateUnique('#__virtuemart_products_' . strtolower(strtr($lang,'-','_')),'slug');

					$prodTable->bindChecknStore($prodTable, false, true);
				}
			}
		}

		return $data['virtuemart_product_id'];
	}

	/**
	 * Creates a clone of a given product id
	 *
	 * @author Max Milbers
	 * @param int $virtuemart_product_id
	 */

	public function createCloneWithChildren ($id) {

		$relation = array();

		$newId = $this->createClone($id);
		$relation[$id] = $newId;
		if(empty($newId)) return false;

		if($children = $this->getProductChildIds($id)){
			foreach($children as $pid){
				$relation[$pid] = $this->createClone($pid, $newId);
			}
		}
vmdebug('createCloneWithChildren relation',$relation);
		$cM = VmModel::getModel('customfields');
		$customfields = $cM->getCustomEmbeddedProductCustomFields( array($newId), 0, -1, true);


		if ($customfields) {
			foreach ($customfields as $i=>$customfield) {
				if($customfield->field_type == 'C'){
					//$product = $this->getProductSingle ($newId, FALSE, 1, false, 0, false);

					//foreach ($product->customfields as $i=>$customfield) {
						if($customfield->field_type == 'C') {
							if(!empty($customfield->options)){
								$newOptions = array();
								foreach($customfield->options as $optKey=>$opt){
									$newOptions[$relation[$optKey]] = $opt;
								}
								$customfield->options = $newOptions;

								$data = get_object_vars($customfield);

								vmdebug('storeProductCustomfield in product model indChecknStore',$data['field'][$customfield->virtuemart_customfield_id]);
								$cM->storeProductCustomfield ('product', $data);
							}
							break;
						}
					//}
					break;
				}
			}
		}
		return $newId;
	}

	function map_old_to_new_indeces($n, $m) {
		return [$n => $m];
	}

	/**
	 * Creates a clone of a given product id
	 *
	 * @author Max Milbers
	 * @param int $virtuemart_product_id
	 */

	public function createClone ($id, $parentId = 0) {

		if(!vmAccess::manager('product.create')){
			vmWarn('Insufficient permission to create product');
			return false;
		}
		if(empty($id)){
			vmWarn('Cannot clone product with empty id');
			return false;
		}

		//We only want to clone not inherited properties
		//$product = $this->getProduct ($id, FALSE, FALSE, FALSE);
		$product = $this->getProductSingle ($id, FALSE, 1, false, 0, false);
		$product->field = $this->productCustomsfieldsClone ($id);
		$product->virtuemart_product_id = $product->virtuemart_product_price_id = 0;
		$product->mprices = $this->productPricesClone ($id);
		$product->virtuemart_shoppergroup_id = $product->shoppergroups;

		//We clone a child of a just cloned parent, keep the relation.
		if(!empty($parentId)) $product->product_parent_id = $parentId;

		if(VmConfig::get('CloneProductResetCreated', true)) {
			$product->created_on = 0;
			$product->created_by = 0;
		}

		$product->slug = $product->slug . '-' . $id;
		$product->originId = $id;
		$product->published=0;
		$product->product_sales=0;
		$product->product_ordered=0;

		$newId = $this->store ($product);
		//$product->virtuemart_product_id = $newId;
		vDispatcher::importVMPlugins ('vmcustom');
		$result=vDispatcher::trigger ('plgVmCloneProduct', array($product));

		$langs = VmConfig::get('active_languages', array(VmConfig::$jDefLangTag));
		if ($langs and count($langs)>1){
			$langTable = $this->getTable('products');
			foreach($langs as $lang){
				if($lang==VmConfig::$vmlangTag) continue;
				$langTable->reset();
				$langTable->emptyCache();
				$langTable->setLanguage($lang);
				//Disables the language fallback
				$langTable->_ltmp = true;
				$langTable->load($id);

				if($langTable->_loaded and !$langTable->_loadedWithLangFallback){
					if(!empty($langTable->virtuemart_product_id)){
						$langTable->virtuemart_product_id = $newId;
						$langTable->slug = $langTable->slug . '-' . $id;
						$langTable->bindChecknStore($langTable, false, true);
					}
				}
			}
		}

		return $newId;
	}

	private function productPricesClone ($virtuemart_product_id) {

		$db = JFactory::getDBO ();
		$q = "SELECT * FROM `#__virtuemart_product_prices`";
		$q .= " WHERE `virtuemart_product_id` = " . $virtuemart_product_id;
		$db->setQuery ($q);
		$prices = $db->loadAssocList ();

		if ($prices) {
			foreach ($prices as $k => $price) {
				unset($price['virtuemart_product_id'], $price['virtuemart_product_price_id']);
				//if(empty($mprices[$k])) $mprices[$k] = array();

				foreach ($price as $i => $value) {
					if(empty($mprices[$i])) $mprices[$i] = array();
					$mprices[$i][$k] = $value;
				}
			}
			return $mprices;
		}
		else {
			return NULL;
		}
	}

	/* look if whe have a product type */
	private function productCustomsfieldsClone ($virtuemart_product_id) {

		$cM = VmModel::getModel('customfields');
		$customfields = $cM->getCustomEmbeddedProductCustomFields(array($virtuemart_product_id),0,-1,true);

		if ($customfields) {
			foreach ($customfields as $i=>$customfield) {
				$cfield = get_object_vars($customfield);
				unset($cfield['virtuemart_product_id'], $cfield['virtuemart_customfield_id']);
				$customfields[$i] = $cfield;
			}
			return $customfields;
		}
		else {
			return NULL;
		}
	}

	/**
	 * removes a product and related table entries
	 *
	 * @author Max Milberes
	 */
	public function remove ($ids) {

		if(!vmAccess::manager('product.delete')){
			vmWarn('Insufficient permissions to delete product');
			return false;
		}

		$table = $this->getTable ($this->_maintablename);

		$cats = $this->getTable ('product_categories');
		$customfields = $this->getTable ('product_customfields');
		$manufacturers = $this->getTable ('product_manufacturers');
		$medias = $this->getTable ('product_medias');
		$prices = $this->getTable ('product_prices');
		$shop = $this->getTable ('product_shoppergroups');

		$rating = $this->getTable ('ratings');
		$review = $this->getTable ('rating_reviews');
		$votes = $this->getTable ('rating_votes');

		$ok = TRUE;
		foreach ($ids as $id) {

			$childIds = $this->getProductChildIds ($id);
			if (!empty($childIds)) {
				vmError (vmText::_ ('COM_VIRTUEMART_PRODUCT_CANT_DELETE_CHILD'));
				$ok = FALSE;
				continue;
			}

			if (!$table->delete ($id)) {
				$ok = FALSE;
			}

			if (!$cats->delete ($id, 'virtuemart_product_id')) {
				$ok = FALSE;
			}

			if (!$customfields->delete ($id, 'virtuemart_product_id')) {
				$ok = FALSE;
			}

			$db = JFactory::getDbo();
			$q = 'SELECT `virtuemart_customfield_id` FROM `#__virtuemart_product_customfields` as pc ';
			$q .= 'LEFT JOIN `#__virtuemart_customs`as c ON c.virtuemart_custom_id=pc.virtuemart_custom_id WHERE pc.`customfield_value` = "' . $id . '" AND `field_type`= "R"';
			$db->setQuery($q);
			$list = $db->loadColumn();

			if ($list) {
				$listInString = implode(',',$list);
				//Delete media xref
				$query = 'DELETE FROM `#__virtuemart_product_customfields` WHERE `virtuemart_customfield_id` IN ('. $listInString .') ';
				$db->setQuery($query);
				try {
					if(!$db->execute()){
						vmError( 'SQL Error' );
					}
				} catch (Exception $e) {
					vmError( $e->getMessage() );
				}
			}

			if (!$manufacturers->delete ($id, 'virtuemart_product_id')) {
				$ok = FALSE;
			}

			if (!$medias->delete ($id, 'virtuemart_product_id')) {
				$ok = FALSE;
			}

			if (!$prices->delete ($id, 'virtuemart_product_id')) {
				$ok = FALSE;
			}

			if (!$shop->delete ($id, 'virtuemart_product_id')) {
				$ok = FALSE;
			}

			if (!$rating->delete ($id, 'virtuemart_product_id')) {
				$ok = FALSE;
			}

			if (!$review->delete ($id, 'virtuemart_product_id')) {
				$ok = FALSE;
			}

			if (!$votes->delete ($id, 'virtuemart_product_id')) {
				$ok = FALSE;
			}

			// delete plugin on product delete
			// $ok must be set to false if an error occurs
			vDispatcher::importVMPlugins ('vmcustom');
			vDispatcher::trigger ('plgVmOnDeleteProduct', array($id, &$ok));
		}

		return $ok;
	}


	/**
	 * Gets the price for a variant
	 *
	 * @author Max Milbers
	 */
	public function getPrice ($product, $quantity, $ctype=-1) {

		if (!is_object ($product)) {
			$product = $this->getProduct ($product, TRUE, FALSE, TRUE,$quantity);
		}

		if (empty($product->customfields) and $product->customfields!=array() and !empty($product->allIds)) {
			$customfieldsModel = VmModel::getModel ('Customfields');
			$product->modificatorSum = null;
			$product->customfields = $customfieldsModel->getCustomEmbeddedProductCustomFields ($product->allIds,0,$ctype);
		}

		$calculator = calculationHelper::getInstance ();
		$prices = $calculator->getProductPrices ($product, TRUE, $quantity);

		return $prices;

	}


	/**
	 * Get the Order By Select List
	 *
	 * notice by Max Milbers html tags should never be in a model. This function should be moved to a helper or simular,...
	 *
	 * @author Max Milbers
	 * @access public
	 * @param $fieds from config Back-end
	 * @return $orderByList
	 * Order,order By, manufacturer and category link List to echo Out
	 **/
	function getOrderByList ($virtuemart_category_id = FALSE) {

		$getArray = vRequest::getGet(FILTER_SANITIZE_STRING);

		if (!isset($getArray['view'])) {
			$getArray['view'] = 'category';
		}
		if (!isset($getArray['virtuemart_category_id'])) {
			$getArray['virtuemart_category_id'] = 0;
		}

		$fieldLink = vmURI::getCurrentUrlBy('request', false, true, array('orderby','dir'));

		$orderDirLink = '';
		$orderDirConf = VmConfig::get ('prd_brws_orderby_dir');
		$orderDir = vRequest::getCmd ('dir', $orderDirConf);
		$orderDir = $this->checkFilterDir($orderDir);
		if ($orderDir != $orderDirConf ) {
			$orderDirLink .= '&dir=' . $orderDir;	//was '&order='
		}

		$orderbyTxt = '';
		$orderbyCfg = VmConfig::get ('browse_orderby_field');
		$orderby = vRequest::getString ('orderby', $orderbyCfg);
		$orderby = $this->checkFilterOrder ($orderby);

		if ($orderby != $orderbyCfg) {
			$orderbyTxt = '&orderby=' . $orderby;
		}

		$useCache = VmConfig::get('UseCachegetOrderByList',true);

		$manuList = '';
		if (VmConfig::get ('show_manufacturers',true)) {

			vmSetStartTime('mcaching');
			if($useCache){
				$cache = VmConfig::getCache('com_virtuemart_orderby_manus','callback');
				$cache->setCaching(true);

				$manuList = $cache->get( array( 'VirtueMartModelProduct', 'getManufacturerOrderByList' ),array($virtuemart_category_id, $fieldLink, $orderbyTxt, $orderDirLink));
				vmTime('Manufacturers Dropdown by Cache','mcaching');
			} else {
				$manuList = VirtueMartModelProduct::getManufacturerOrderByList($virtuemart_category_id, $fieldLink, $orderbyTxt, $orderDirLink);
				vmTime('Manufacturers Dropdown by function','mcaching');
			}

		}

		vmSetStartTime('orderBy');
		/*if($useCache){
			$cache = VmConfig::getCache('com_virtuemart_orderby','callback');
			$cache->setCaching(true);

			$orderByList = $cache->get( array( 'ShopFunctionsF','renderVmSubLayout'),(array('orderby',array('orderby' => $orderby, 'fieldLink' => $fieldLink, 'orderDir' =>$orderDir, 'orderbyTxt' => $orderbyTxt, 'orderDirLink' => $orderDirLink) )));

			vmTime('OrderByList by Cache','orderBy');
		} else {*/
			$orderByList = ShopFunctionsF::renderVmSubLayout('orderby',array('orderby' => $orderby, 'fieldLink' => $fieldLink, 'orderDir' =>$orderDir, 'orderbyTxt' => $orderbyTxt, 'orderDirLink' => $orderDirLink));
			vmTime('OrderByList by function','orderBy');
		//}


		return array('orderby'=> $orderByList, 'manufacturer'=> $manuList);
	}

	static public function getManufacturerOrderByList($virtuemart_category_id, $fieldLink, $orderbyTxt, $orderDirLink){

		$manuM = VmModel::getModel('manufacturer');
		vmSetStartTime('mcaching');

		$manuList = '';
		$manufacturers = $manuM ->getManufacturersOfProductsInCategory($virtuemart_category_id);
		if($manufacturers) $manuList = ShopFunctionsF::renderVmSubLayout('orderbymanu',array('manufacturers' => $manufacturers, 'fieldLink' => $fieldLink, 'orderbyTxt' => $orderbyTxt, 'orderDirLink' => $orderDirLink));
		return $manuList;
	}

// **************************************************
//Stocks
//
	/**
	 * Get the stock level for a given product
	 *
	 * @author RolandD
	 * @access public
	 * @param object $product the product to get stocklevel for
	 * @return array containing product objects
	 */
	public function getStockIndicator ($product) {

		/* Assign class to indicator */
		$stock_level = $product->product_in_stock - $product->product_ordered;
		$reorder_level = $product->low_stock_notification;
		$level = 'normalstock';
		$stock_tip = vmText::_ ('COM_VIRTUEMART_STOCK_LEVEL_DISPLAY_NORMAL_TIP');
		if ($stock_level <= $reorder_level) {
			$level = 'lowstock';
			$stock_tip = vmText::_ ('COM_VIRTUEMART_STOCK_LEVEL_DISPLAY_LOW_TIP');
		}
		if ($stock_level <= 0) {
			$level = 'nostock';
			$stock_tip = vmText::_ ('COM_VIRTUEMART_STOCK_LEVEL_DISPLAY_OUT_TIP');
		}
		$stock = new Stdclass();
		$stock->stock_tip = $stock_tip;
		$stock->stock_level = $level;
		return $stock;
	}


	public function updateStockInDB ($product, $amount, $signInStock, $signOrderedStock) {
		vmdebug('updateStockInDB start ', $signInStock, $signOrderedStock);
		$validFields = array('=', '+', '-');
		if (!in_array ($signInStock, $validFields)) {
			return FALSE;
		}
		if (!in_array ($signOrderedStock, $validFields)) {
			return FALSE;
		}

		$lproduct = $this->getProductSingle($product->virtuemart_product_id);
		if($lproduct->shared_stock){
			$productId = $lproduct->product_parent_id;
		} else {
			$productId = $product->virtuemart_product_id;
		}

		$amount = (float)$amount;
		$update = array();

		if ($signInStock != '=' or $signOrderedStock != '=') {

			if ($signInStock != '=') {
				$update[] = '`product_in_stock` = `product_in_stock` ' . $signInStock . $amount;

				if (strpos ($signInStock, '+') !== FALSE) {
					$signInStock = '-';
				}
				else {
					$signInStock = '+';
				}
				$update[] = '`product_sales` = `product_sales` ' . $signInStock . $amount;

			}
			if ($signOrderedStock != '=') {
				$update[] = '`product_ordered` = `product_ordered` ' . $signOrderedStock . $amount;
			}
			$q = 'UPDATE `#__virtuemart_products` SET ' . implode (", ", $update) . ' WHERE `virtuemart_product_id` = ' . (int)$productId;

			$db = JFactory::getDbo();
			$db->setQuery ($q);
			$db->execute ();
			//vmdebug('updateStockInDB executed query ', $q);
			//The low on stock notification comes now, when the people ordered.
			//You need to know that the stock is going low before you actually sent the wares, because then you ususally know it already yourself
			//note by Max Milbers
			if ($signInStock == '+' or $signOrderedStock == '+') {

				$q = 'SELECT (IFNULL(`product_in_stock`,"0")-IFNULL(`product_ordered`,"0")) < IFNULL(`low_stock_notification`,"0") '
				. 'FROM `#__virtuemart_products` '
				. 'WHERE `virtuemart_product_id` = ' . (int)$productId;
				$db->setQuery ( $q );
				//vmdebug('Check for low stock ',$q);
				if ($db->loadResult () == 1) {
					vmdebug('Check for low stock said therre is a low stock ');
					$this->lowStockWarningEmail( $productId) ;
				}
			}
		}

	}
	function lowStockWarningEmail($virtuemart_product_id) {

		if(VmConfig::get('lstockmail',TRUE)){

			/* Load the product details */
			$q = "SELECT l.product_name,product_in_stock,virtuemart_vendor_id FROM `#__virtuemart_products_" . VmConfig::$vmlang . "` l
				JOIN `#__virtuemart_products` p ON p.virtuemart_product_id=l.virtuemart_product_id
			   WHERE p.virtuemart_product_id = " . $virtuemart_product_id;
			$db = JFactory::getDbo();
			$db->setQuery ($q);
			$vars = $db->loadAssoc ();
			vmdebug('lowStockWarningEmail query result',$q,$vars);
			$url = JURI::root () . 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id;
			$link = '<a href="'. $url.'">'. $vars['product_name'].'</a>';
			$vars['subject'] = vmText::sprintf('COM_VIRTUEMART_PRODUCT_LOW_STOCK_EMAIL_SUBJECT',$vars['product_name']);
			$vars['mailbody'] =vmText::sprintf('COM_VIRTUEMART_PRODUCT_LOW_STOCK_EMAIL_BODY',$link, $vars['product_in_stock']);

			$virtuemart_vendor_id = 1;
			if(Vmconfig::get('multix','none')!=='none'){
				$virtuemart_vendor_id = $vars['virtuemart_vendor_id'];
			}

			$vendorModel = VmModel::getModel ('vendor');
			$vendor = $vendorModel->getVendor ($virtuemart_vendor_id);
			$vendorModel->addImages ($vendor);
			$vars['vendor'] = $vendor;

			$vars['vendorAddress']= shopFunctions::renderVendorAddress($virtuemart_vendor_id);
			$vars['vendorEmail'] = $vendorModel->getVendorEmail ($virtuemart_vendor_id);

			$vars['user'] =  $vendor->vendor_store_name ;
			shopFunctionsF::renderMail ('productdetails', $vars['vendorEmail'], $vars, 'productdetails', TRUE) ;
			vmdebug('lowStockWarningEmail email sent ',$q,$vars);
			return TRUE;
		} else {
			return FALSE;
		}

	}

	public function getUncategorizedChildren ($withParent) {

		if (!isset($this->_uncategorizedChildren[$this->_id])) {

			//Todo add check for shoppergroup depended product display
			$q = 'SELECT p.`virtuemart_product_id` FROM `#__virtuemart_products` as p
				LEFT JOIN `#__virtuemart_product_categories` as pc
				ON p.`virtuemart_product_id` = pc.`virtuemart_product_id` ';

			if ($withParent) {
				$q .= ' WHERE (p.`product_parent_id` = "' . $this->_id . '"  OR p.`virtuemart_product_id` = "' . $this->_id . '") ';
			}
			else {
				$q .= ' WHERE p.`product_parent_id` = "' . $this->_id . '" ';
			}

			$app = JFactory::getApplication ();
			if (VmConfig::isSite () && !VmConfig::get ('use_as_catalog', 0) && VmConfig::get ('stockhandle_products', false)) {
				$product_stockhandle = $this->getProductStockhandle();
				if ($product_stockhandle->disableit || VmConfig::get ('stockhandle', 'none') == 'disableit') {
					$q .= ' AND ( CASE
									WHEN (p.`product_stockhandle` = "0" AND "'. VmConfig::get('stockhandle','none') .'" = "disableit") OR (p.`product_stockhandle` = "disableit")
										THEN (p.`product_in_stock` - p.`product_ordered`) > "0"
									ELSE 1
								  END = 1 ) ';
				}
			} else if (VmConfig::isSite () && !VmConfig::get ('use_as_catalog', 0) && VmConfig::get ('stockhandle', 'none') == 'disableit') {
				$q .= ' AND (p.`product_in_stock` - p.`product_ordered`) > "0" ';
			}

			if (VmConfig::isSite ()) {
				$q .= ' AND p.`published`="1"';
			}

			$q .= ' GROUP BY p.`virtuemart_product_id` ORDER BY p.pordering ASC';
			$db = JFactory::getDbo();
			$db->setQuery ($q);
			$err = ''; 
			try {
				$r = $db->loadColumn();
				if($r and count($r)>0){
					$this->_uncategorizedChildren[$this->_id] = $r;
				} else {
					$this->_uncategorizedChildren[$this->_id] = array();
				}
			} catch (Exception $e) {
				$err = $e->getMessage(); 
			}
			if (!empty($err)) {
				vmError ('getUncategorizedChildren sql error ' . $err, 'getUncategorizedChildren sql error');
				vmdebug ('getUncategorizedChildren ' . $err);
				return FALSE;
			}

		}
		return $this->_uncategorizedChildren[$this->_id];
	}

	/**
	 * Check if the product has any children
	 *
	 * @author Max Milbers
	 * @param int $virtuemart_product_id Product ID
	 * @return bool True if there are child products, false if there are no child products
	 */
	public function checkChildProducts ($product_ids) {

		if($product_ids!=0){

			$db = JFactory::getDbo();
			if(!is_array($product_ids)) $product_ids = array($product_ids);
			$vmpid = implode('","',$product_ids);
			if(!empty($vmpid)){
				$q = 'SELECT COUNT(virtuemart_product_id) FROM `#__virtuemart_products` WHERE `product_parent_id` IN ('.$vmpid.');'; //  "' . $virtuemart_product_id . '"';
				$db->setQuery ($q);
				return $db->loadResult ();
			}
		}
		return FALSE;
	}

	function getProductChilds ($product_id) {

		if (empty($product_id)) {
			return array();
		}
		$db = JFactory::getDBO ();
		$db->setQuery (' SELECT p.virtuemart_product_id, l.product_name, p.published, p.product_in_stock, p.product_ordered, p.product_sku FROM `#__virtuemart_products` as p
			JOIN `#__virtuemart_products_' . VmConfig::$vmlang . '` as l ON p.virtuemart_product_id = l.virtuemart_product_id
			WHERE `product_parent_id` =' . (int)$product_id);
		return $db->loadObjectList ();

	}

	function getProductChildIds ($product_id, $extra = '') {

		if (empty($product_id)) {
			return array();
		}
		static $cache = array();

		$h = $product_id.'i';
		if($extra!==''){
			$h .= crc32($extra);
		}

		if(isset($cache[$h])){
			return $cache[$h];
		} else {
			$db = JFactory::getDBO ();
			$q = ' SELECT virtuemart_product_id FROM `#__virtuemart_products` WHERE `product_parent_id` =' . (int)$product_id.' '.$extra.' ORDER BY pordering, created_on ASC';
			$db->setQuery ($q);
			$cache[$h] = $db->loadColumn ();
		}

		return $cache[$h];

	}


	public function getAllProductChildIds($product_ids,&$childIds){

		if (empty($product_ids)) {
			return array();
		}

		if(!is_array($product_ids)) $product_ids = array($product_ids);

		if($productsWithChilds = self::checkChildProducts($product_ids)){

			if($productsWithChilds){
				foreach($product_ids as $product_id){
					if(empty($product_id)) continue;
					$tmp = self::getProductChildIds($product_id);
					if($tmp){
						if(!isset($childIds[$product_id])){
							$childIds[$product_id] = $tmp;
							foreach($tmp as $t){
								//prevent looop
								if($t=!$product_id){
									self::getAllProductChildIds($t,$childIds[$product_id]);
								}
							}
						}
					}
				}
			}

		}
	}


	static function getProductParentId ($product_id) {

		if (empty($product_id)) {
			return 0;
		}
		static $parentCache = array();
		$prTable = false;

		if(!isset($parentCache[$product_id])){
			//Check if product got already loaded
			$checkedProductKey= self::checkIfCachedSingle($product_id);

			if($checkedProductKey[0]){
				if(self::$_productsSingle[$checkedProductKey[1]]===false){
					$parentCache[$product_id] = false;
				} else if(isset(self::$_productsSingle[$checkedProductKey[1]]->product_parent_id)){
					$parentCache[$product_id] = self::$_productsSingle[$checkedProductKey[1]]->product_parent_id;
				}
				//vmdebug('getProductParentId self::$_products Cache',$product_id,$parentCache[$product_id]);
			}

			if(!isset($parentCache[$product_id])){
				if(!$prTable){
					$prTable = VmTable::getInstance('products');
				}
				$prTable->load($product_id);
				if(isset($prTable->product_parent_id)){
					$parentCache[$product_id] = $prTable->product_parent_id;
				}
				//vmdebug('getProductParentId executed sql for '.$product_id,$parentCache[$product_id]);
				//vmTrace('getProductParentId executed sql for '.$product_id);
			}

		} else {
			//vmdebug('getProductParentId $parentCache',$product_id,$parentCache[$product_id]);
		}
		//vmdebug('getProductParentId '.$product_id,$parentCache[$product_id]);
		return $parentCache[$product_id];
	}


	function sentProductEmailToShoppers () {

		$product_id = vRequest::getVar ('virtuemart_product_id', '');
		$vars = array();
		$vars['subject'] = vRequest::getVar ('subject');
		$vars['mailbody'] = vRequest::getVar ('mailbody');

		$order_states = vRequest::getInt ('statut');
		$productShoppers = $this->getProductShoppersByStatus ($product_id, $order_states);

		$productModel = VmModel::getModel ('product');
		$product = $productModel->getProduct ($product_id);

		$vendorModel = VmModel::getModel ('vendor');
		$vendor = $vendorModel->getVendor ($product->virtuemart_vendor_id);
		$vendorModel->addImages ($vendor);
		$vars['vendor'] = $vendor;
		$vars['vendorEmail'] = $vendorModel->getVendorEmail ($product->virtuemart_vendor_id);
		$vars['vendorAddress'] = shopFunctions::renderVendorAddress ($product->virtuemart_vendor_id);

		$orderModel = VmModel::getModel ('orders');
		foreach ($productShoppers as $productShopper) {
			$vars['user'] = $productShopper['name'];
			if (shopFunctionsF::renderMail ('productdetails', $productShopper['email'], $vars, 'productdetails', TRUE)) {
				$string = 'COM_VIRTUEMART_MAIL_SEND_SUCCESSFULLY';
			}
			else {
				$string = 'COM_VIRTUEMART_MAIL_NOT_SEND_SUCCESSFULLY';
			}
			// Update the order history  for each order
			foreach ($productShopper['order_info'] as $order_info) {
				$orderModel->_updateOrderHist ($order_info['order_id'], $order_info['order_status'], 1, $vars['subject'] . ' ' . $vars['mailbody']);
			}
			// todo: when there is an error while sending emails
			//vmInfo (vmText::sprintf ($string, $productShopper['email']));
		}

	}


	public function getProductShoppersByStatus ($product_id, $states, $filter_order = 'ou.email', $filter_order_Dir = 'ASC') {

		if (empty($states)) {
			return FALSE;
		}
		$orderstatusModel = VmModel::getModel ('orderstatus');
		$orderStates = $orderstatusModel->getOrderStatusNames ();

		foreach ($states as &$status) {
			if (!array_key_exists ($status, $orderStates)) {
				unset($status);
			}
		}
		if (empty($states)) {
			return FALSE;
		}

		$validFilter = array('ou.email','ou.first_name','o.order_number','order_date');
		if(!in_array($filter_order,$validFilter)){
			$filter_order = 'ou.email';
		}
		$q = 'SELECT ou.* , oi.product_quantity , o.order_number, o.order_status, o.created_on as order_date, oi.`order_status` AS order_item_status ,
		o.virtuemart_order_id FROM `#__virtuemart_order_userinfos` as ou
			JOIN `#__virtuemart_order_items` AS oi ON oi.`virtuemart_order_id` = ou.`virtuemart_order_id`
			JOIN `#__virtuemart_orders` AS o ON o.`virtuemart_order_id` =  oi.`virtuemart_order_id`
			WHERE ou.`address_type`="BT" AND oi.`virtuemart_product_id`=' . (int)$product_id;
		if (count ($orderStates) !== count ($states)) {
			$q .= ' AND oi.`order_status` IN ( "' . implode ('","', $states) . '") ';
		}
		$q .= '  ORDER BY '.$filter_order.' '.$filter_order_Dir;
		$db = JFactory::getDbo();
		$db->setQuery ($q);
		$productShoppers = $db->loadAssocList ();

		$shoppers = array();
		foreach ($productShoppers as $productShopper) {
			$key = $productShopper['email'];
			if (!array_key_exists ($key, $shoppers)) {
				$shoppers[$key]['phone'] = !empty($productShopper['phone_1']) ? $productShopper['phone_1'] : (!empty($productShopper['phone_2']) ? $productShopper['phone_2'] : '-');
				$name = '';
				if(isset($productShopper['first_name'])){
					$name = $productShopper['first_name'];
				}
				if(isset($productShopper['last_name'])){
					$name .= ' ' .$productShopper['last_name'];
				}
				$shoppers[$key]['name'] = trim($name);
				$shoppers[$key]['email'] = $productShopper['email'];
				$shoppers[$key]['mail_to'] = 'mailto:' . $productShopper['email'];
				$shoppers[$key]['nb_orders'] = 0;
			}
			$i = $shoppers[$key]['nb_orders'];
			$shoppers[$key]['order_info'][$i]['order_number'] = $productShopper['order_number'];
			$shoppers[$key]['order_info'][$i]['order_id'] = $productShopper['virtuemart_order_id'];
			$shoppers[$key]['order_info'][$i]['order_status'] = $productShopper['order_status'];
			$shoppers[$key]['order_info'][$i]['order_item_status_name'] = $orderStates[$productShopper['order_item_status']]['order_status_name'];
			$shoppers[$key]['order_info'][$i]['quantity'] = $productShopper['product_quantity'];
			$shoppers[$key]['order_info'][$i]['order_date'] = $productShopper['order_date'];
			$shoppers[$key]['nb_orders']++;
		}
		return $shoppers;
	}

	/**
	 *
	 * @author Max Milbers
	 */
	static public function addProductToRecent ($productId) {

		$session = JFactory::getSession();
		$products_ids = $session->get( 'vmlastvisitedproductids', array(), 'vm' );
		$key = array_search( $productId, $products_ids );
		if($key !== FALSE) {
			unset($products_ids[$key]);
		}
		array_unshift( $products_ids, $productId );
		$products_ids = array_unique( $products_ids );

		$maxSize = (int)VmConfig::get('max_recent_products', 10);
		if(count( $products_ids )>$maxSize) {
			array_splice( $products_ids, $maxSize );
		}

		return $session->set( 'vmlastvisitedproductids', $products_ids, 'vm' );
	}

	/**
	 * Gives ids the recently by the shopper visited products
	 *
	 * @author Max Milbers
	 */
	static public function getRecentProductIds ($nbr = 3) {

		$session = JFactory::getSession();
		$ids = $session->get( 'vmlastvisitedproductids', array(), 'vm' );
		if(count( $ids )>$nbr) {
			array_splice( $ids, $nbr );
		}
		return $ids;
	}
}
// No closing tag