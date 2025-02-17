<?php
/**
 *
 * Handle the category view
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers
* @link https://virtuemart.net
 * @copyright Copyright (c) 2004 - 2019 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 10585 2022-02-07 13:50:28Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Handle the category view
 *
 * @package VirtueMart
 * @author Max Milbers
 */
class VirtuemartViewCategory extends VmView {

	public $combineTags = true;
	public function display($tpl = null) {

		//For BC, we convert first the new config param names to the old ones
		//Attention, this will be removed around 2020.
		if(VmConfig::get('legacylayouts',false)){
			VmConfig::set('show_featured', VmConfig::get('featured'));
			VmConfig::set('show_discontinued', VmConfig::get('discontinued'));
			VmConfig::set('show_topTen', VmConfig::get('topten'));
			VmConfig::set('show_recent', VmConfig::get('recent'));
			VmConfig::set('show_latest', VmConfig::get('latest'));

			VmConfig::set('featured_products_rows', VmConfig::get('featured_rows'));
			VmConfig::set('discontinued_products_rows', VmConfig::get('discontinued_rows'));
			VmConfig::set('topTen_products_rows', VmConfig::get('topten_rows'));
			VmConfig::set('recent_products_rows', VmConfig::get('recent_rows'));
			VmConfig::set('latest_products_rows', VmConfig::get('latest_rows'));
			VmConfig::set('omitLoaded_topTen', VmConfig::get('omitLoaded_topten'));
			VmConfig::set('showCategory', VmConfig::get('showcategory'));
		}


		$this->show_prices  = (int)VmConfig::get('show_prices',1);

		$document = JFactory::getDocument();

		$this->app = JFactory::getApplication();
		$pathway = $this->app->getPathway();

		if( ShopFunctionsF::isFEmanager('product.edit') ){
			$add_product_link = JURI::root() . 'index.php?option=com_virtuemart&tmpl=component&view=product&task=edit&virtuemart_product_id=0&manage=1' ;
			$add_product_link = $this->linkIcon($add_product_link, 'COM_VIRTUEMART_PRODUCT_FORM_NEW_PRODUCT', 'edit', false, false);
		} else {
			$add_product_link = "";
		}
		$this->assignRef('add_product_link', $add_product_link);

		$menus	= $this->app->getMenu();
		$menu = $menus->getActive();

		//vmdebug('My active menu item',$menu);
		if(!empty($menu->id)){
			$itemId = $menu->id;
		} else {

			$itemId = vRequest::getInt('Itemid',false);
			if($itemId){
				$menus->setActive($itemId);
				$menu = $menus->getActive();
			}
			if(empty($menu->id)){
				$menu = $menus->getDefault();
				vmdebug('$menu = $menus->getDefault',$itemId);
			}
		}

		if(empty($menu)){
			$menu = new stdClass;
			$menuParams = new JRegistry();
		} else {
			$menuParams = $menu->getParams();
		}

		$stf_itemid = $menuParams->get('stf_itemid',false);
		if(!empty($stf_itemid) ){
			$mstf=$menus->getItem($stf_itemid);
			if(!empty($mstf)){
				$menu = $mstf;
			}
		}

		ShopFunctionsF::setLastVisitedItemId($itemId);
		$this->Itemid = $itemId;

		$this->productModel = VmModel::getModel('product');
		$this->keyword = $this->productModel->keyword;

		$this->virtuemart_manufacturer_id = vRequest::getInt('virtuemart_manufacturer_id', -1 );
		if($this->virtuemart_manufacturer_id ===-1 and isset($menu->query['virtuemart_manufacturer_id'])){
			$this->virtuemart_manufacturer_id = $menu->query['virtuemart_manufacturer_id'];
			vRequest::setVar('virtuemart_manufacturer_id',$this->virtuemart_manufacturer_id);
		}
		//vmdebug('caetgory view $this->virtuemart_manufacturer_id',$this->virtuemart_manufacturer_id,$menu->query['virtuemart_manufacturer_id'],vRequest::getInt('virtuemart_manufacturer_id', -1 ),$_REQUEST);
		$this->categoryId = vRequest::getInt('virtuemart_category_id', -1);
		if($this->categoryId === -1 and isset($menu->query['virtuemart_category_id'])){
			$this->categoryId = $menu->query['virtuemart_category_id'];
			vRequest::setVar('virtuemart_category_id',$this->categoryId);
		} else if ( $this->categoryId === -1 and $this->virtuemart_manufacturer_id === -1 and empty($this->keyword)){

			$this->categoryId = ShopFunctionsF::getLastVisitedCategoryId();
		}

		if(empty($this->categoryId) and $this->virtuemart_manufacturer_id===''){

			vmInfo(vmText::_('COM_VIRTUEMART_MANU_NOT_FOUND'));

			$this->handle404();
		}

		if ($this->categoryId === -1 and $this->virtuemart_manufacturer_id){
			$this->categoryId = 0;
		}

		$this->setCanonicalLink($tpl,$document,$this->categoryId,$this->virtuemart_manufacturer_id);


		if($this->categoryId===-1) $this->categoryId = 0;
		if($this->virtuemart_manufacturer_id===-1) $this->virtuemart_manufacturer_id = 0;

		$prefix = '';



		if(empty($this->keyword)) $this->keyword = false;

		if(		(isset($menu->query['virtuemart_category_id']) and $menu->query['virtuemart_category_id']!=$this->categoryId) or
		(isset($menu->query['virtuemart_manufacturer_id']) and $menu->query['virtuemart_manufacturer_id']!=$this->virtuemart_manufacturer_id) or
		!empty($this->keyword ) ) {
			$prefix = 'stf_';
		}

		$paramNames = array('itemid'=>'',
		'categorylayout' => VmConfig::get('categorylayout', 0),
		'show_store_desc' => VmConfig::get('show_store_desc',1),
		'showcategory_desc' => VmConfig::get('showcategory_desc', 1),
		'showcategory' => VmConfig::get('showcategory',1),
		'categories_per_row' => VmConfig::get('categories_per_row',3),
		'showproducts' => VmConfig::get('showproducts',1),
		'showsearch' => VmConfig::get('showsearch',0),
		'productsublayout' => VmConfig::get('productsublayout', 0),
		'products_per_row' => VmConfig::get('products_per_row', 3),
		'featured' => VmConfig::get('featured',1),
		'featured_rows' => VmConfig::get('featured_rows',1),
		'discontinued' => VmConfig::get('discontinued',0),
		'discontinued_rows' => VmConfig::get('discontinued_rows',1),
		'latest' => VmConfig::get('latest',1),
		'latest_rows' => VmConfig::get('latest_rows',1),
		'topten' => VmConfig::get('topten',1),
		'topten_rows' => VmConfig::get('topten_rows',1),
		'recent' => VmConfig::get('recent',0),
		'recent_rows' => VmConfig::get('recent_rows',1));

		$categoryModel = VmModel::getModel('category');

		// set search and keyword
		if ($this->productModel->keyword){
			$pathway->addItem(strip_tags(htmlspecialchars_decode($this->keyword)));
		}

		$category = $categoryModel->getCategory($this->categoryId, false);
		$this->assignRef('category', $category);

		foreach($paramNames as $k => $v){
			if(!isset($category->{$k}) or $category->{$k}==''){
				$this->{$k} = $menuParams->get($prefix.$k,$v);
			} else if(isset($category->{$k})){
				$this->{$k} = $category->{$k};
			}
		}

		$this->storefront = $menuParams->get('storefront',0);

		$this->perRow = $this->products_per_row = empty($category->products_per_row)? $menuParams->get($prefix.'products_per_row',$paramNames['products_per_row']):$category->products_per_row;

		$vendorId = $category->virtuemart_vendor_id;
		if(empty($vendorId)) $vendorId = 1; //If we are in the root category, the id is empty

		//No redirect here, for category id = 0 means show ALL categories! note by Max Milbers
		if ((!empty($this->categoryId) and $this->categoryId!==-1 ) and (empty($category->slug) or !$category->published)) {

			$this->handle404();
		}

		$ratingModel = VmModel::getModel('ratings');
		$this->productModel->withRating = $this->showRating = $ratingModel->showRating();

		$this->vmPagination = '';
		$this->orderByList = '';

		$this->searchcustom = '';
		$this->searchCustomValues = '';	//deprecated
		$this->searchCustomValuesAr = array ();

		$app = JFactory::getApplication();


		if(!empty($this->keyword) or $this->showsearch){

			$this->getSearchCustom();
			$this->searchAllCats = $app->getUserStateFromRequest('com_virtuemart.customfields.searchAllCats','searchAllCats',false);
		}


		shopFunctionsF::setLastVisitedCategoryId($this->categoryId);
		shopFunctionsF::setLastVisitedManuId($this->virtuemart_manufacturer_id);

		//We need to load the cart here, to get correct discounts
		if(!VmConfig::get('use_as_catalog',false)) $cart = VirtuemartCart::getCart();

		$imgAmount = VmConfig::get('prodimg_browse',1);
		$dynamic = vRequest::getInt('dynamic',false);
		$id = vRequest::getInt('virtuemart_product_id',false);
		$legacy = VmConfig::get('legacylayouts',1);

		$this->products = array();

		if ($dynamic and $id) {
			$p = $this->productModel->getProduct ($id);
			$this->products['products'][] = $p;
			$this->productModel->addImages($this->products['products'], $imgAmount );
			$this->orderByList = array('orderby' => '', 'manufacturer' => '');
			if($legacy) {
				$this->vmPagination = $this->productModel->getPagination($this->perRow);
			}
		} else {

			//The search must be executed first
			if(!empty($this->keyword) or !empty($this->productModel->searchcustoms)) {
				vmdebug('Lets load the search',$this->keyword,$this->productModel->searchcustoms);
				// Load the products in the given category
				$ids = $this->productModel->sortSearchListQuery (TRUE, $this->categoryId);
				VirtueMartModelProduct::$_alreadyLoadedIds = array_merge(VirtueMartModelProduct::$_alreadyLoadedIds,$ids);
				$this->vmPagination = $this->productModel->getPagination($this->perRow);
				$this->orderByList = $this->productModel->getOrderByList($this->categoryId);
				$this->products['products'] = $this->productModel->getProducts ($ids);
				$this->productModel->addImages($this->products['products'], $imgAmount );
			}

			if($legacy) {

				if($this->showproducts){
					$opt = array('products');
				} else {
					$opt = array();
				}
			} else {
				$sequence = VmConfig::get('ProductGroupsSequence','');
				if(empty($sequence)){
					$opt = array('featured', 'discontinued', 'latest', 'topten', 'recent');
				} else {
					$opt = explode(',',$sequence);
				}

				if($this->showproducts and empty($this->keyword) and empty($this->productModel->searchcustoms)){
					$opt[] = 'products';
				}
			}

			foreach( $opt as $o ) {
				$o = trim($o);
				if($o == 'products') {
					VirtueMartModelProduct::$omitLoaded = VmConfig::get('omitLoaded');
					$ids = $this->productModel->sortSearchListQuery( TRUE, $this->categoryId );
					VirtueMartModelProduct::$_alreadyLoadedIds = array_merge( VirtueMartModelProduct::$_alreadyLoadedIds, $ids );
					$this->vmPagination = $this->productModel->getPagination( $this->perRow );
					$this->orderByList = $this->productModel->getOrderByList( $this->categoryId );

					$this->products['products'] = $this->productModel->getProducts( $ids );
					$this->productModel->addImages( $this->products['products'], $imgAmount );
				} else {
					//Lets check, if we use the new Frontpages settings
					VirtueMartModelProduct::$omitLoaded = VmConfig::get( 'omitLoaded_'.$o );
					if(!empty( $this->{$o} ) and !empty( $this->{$o.'_rows'} )) {
						$this->products[$o] = $this->productModel->getProductListing( $o, $this->perRow*$this->{$o.'_rows'} );
						$this->productModel->addImages( $this->products[$o], $imgAmount );
					}
				}
			}

		}

		if ($this->products) {

			$this->currency = CurrencyDisplay::getInstance( );
			$display_stock = VmConfig::get('display_stock',1);
			$showCustoms = VmConfig::get('show_pcustoms',1);

			//Unset the empty product groups to avoid errors with old layouts
			foreach($this->products as $pType => $productSeries) {
				if($productSeries===false) unset($this->products[$pType]);
			}

			if($display_stock or $showCustoms){

				if(!$showCustoms){
					foreach($this->products as $pType => $productSeries){
						foreach($productSeries as $i => $productItem){
							$this->products[$pType][$i]->stock = $this->productModel->getStockIndicator($productItem);
						}
					}
				} else {

					foreach($this->products as $pType => $productSeries) {
						shopFunctionsF::sortLoadProductCustomsStockInd($this->products[$pType],$this->productModel);
					}
				}
			}
		}

		// Add feed links
		if ($this->products  && VmConfig::get('feed_cat_published', 0)==1) {

			$link = vmURI::getCurrentUrlBy('get').'&format=feed&limitstart=';

			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$document->addHeadLink(JRoute::_($link . '&type=rss', false), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$document->addHeadLink(JRoute::_($link . '&type=atom', false), 'alternate', 'rel', $attribs);
		}

		$this->showBasePrice = (vmAccess::manager() or vmAccess::isSuperVendor());


		// Add the category name to the pathway
		if ($category->parents) {
			foreach ($category->parents as $c){
				if(!empty($c->virtuemart_category_id) and !empty($c->category_name) and !empty($c->published)){
					$pathway->addItem(strip_tags(vmText::_($c->category_name)),JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$c->virtuemart_category_id, FALSE));
				} else vmdebug('parent category is empty',$category->parents);
			}
		}
		$catImgAmount = VmConfig::get('catimg_browse',1);
		$categoryModel->addImages($category,$catImgAmount);

		if($this->showcategory){

			$category->children = $categoryModel->getChildCategoryList( $vendorId, $this->categoryId, $categoryModel->getDefaultOrdering(), $categoryModel->_selectedOrderingDir );
			$categoryModel->addImages($category->children,$catImgAmount);
			//Whatever fallback
			$category->haschildren = $category->has_children;

		} else {
			$category->children = false;
		}

		if (VmConfig::get('enable_content_plugin', 0)) {
			shopFunctionsF::triggerContentPlugin($category, 'category','category_description');
		}

		if(empty($category->category_template)){
			$category->category_template = VmConfig::get('categorytemplate');
		}

		if(!empty($this->categorylayout)){
			$category->category_layout = $this->categorylayout;
		}

		vmJsApi::jPrice();

		$this->productsLayout = 'products'; //VmConfig::get('productsublayout','products');
		if(!empty($this->productsublayout)){
			$this->productsLayout = $this->productsublayout;
		}


		VmTemplate::setVmTemplate($this,$category->category_template,0,$category->category_layout);


		$customtitle = '';
		$metadesc = '';
		$metakey = '';
		$metarobot = '';
		$metaauthor = '';

		$metadesc = $menuParams->get('menu-meta_description');
		$metakey = $menuParams->get('menu-meta_keywords');
		$metarobot = $menuParams->get('robots');
		$customtitle = $menuParams->get('page_title');


		if(($this->storefront and empty($prefix)) or $this->show_store_desc or empty($this->categoryId)){

			$vendorModel = VmModel::getModel('vendor');
			if(!empty($category->virtuemart_vendor_id)){
				$vendorModel->setId($category->virtuemart_vendor_id);
			} else {
				$vendorModel->setId(1);;
			}
			$this->vendor = $vendorModel->getVendor();
		}

		$this->manu_descr = '';

		if(($this->storefront and empty($prefix)) or (empty($this->categoryId) and empty($this->virtuemart_manufacturer_id)) ){

			if(empty($this->vendor->customtitle)){
				if(empty($customtitle)) {
					$customtitle = vmText::sprintf('COM_VIRTUEMART_HOME',$this->vendor->vendor_store_name);
				}
			} else {
				$customtitle = $this->vendor->customtitle;
			}

			if(!empty($this->vendor->metadesc)) $metadesc = $this->vendor->metadesc;
			if(!empty($this->vendor->metakey)) $metakey = $this->vendor->metakey;
			if(!empty($this->vendor->metarobot)) $metarobot = $this->vendor->metarobot;
			if(!empty($this->vendor->metaauthor)) $metaauthor = $this->vendor->metaauthor;

		} else {
			if(empty($this->categoryId)){
				$metaObj = VmModel::getModel('manufacturer')->getManufacturer($this->virtuemart_manufacturer_id);
				$this->manu_descr = $metaObj->mf_desc;
			} else {
				$metaObj = $category;
			}

			if (!empty($metaObj->metadesc)) {
				$metadesc = $metaObj->metadesc;
			}
			if (!empty($metaObj->metakey)) {
				$metakey = $metaObj->metakey;
			}
			if (!empty($metaObj->metarobot)) {
				$metarobot = $metaObj->metarobot;
			}
			if(!empty($metaObj->customtitle)){
				$customtitle = $metaObj->customtitle;
			}

			if ($this->app->getCfg('MetaAuthor') == '1' and !empty($category->metaauthor)) {
				$metaauthor = $category->metaauthor;
			}

		}

		if(empty($metadesc)){
			$description = '';
			if(!empty($this->categoryId)){
				$description=$category->category_description;
				$name=$category->category_name;
			} else if(!empty($this->virtuemart_manufacturer_id)){
				$metaObj = VmModel::getModel('manufacturer')->getManufacturer($this->virtuemart_manufacturer_id);
				$description=$metaObj->mf_desc;
				$name=$metaObj->mf_name;
			}
			$qdesc =  strip_tags(html_entity_decode($description,ENT_QUOTES)) ;
			$qdesc = str_replace(array("\n", "\r","\t"), "", $qdesc);
			$qdesc = str_replace(array("  "), " ", $qdesc);
			$qdesc = shopFunctionsF::limitStringByWord($qdesc,120);
			$metadesc = $category->category_name . ". ". $qdesc . ' ' .vmText::_('COM_VIRTUEMART_READ_MORE');
		}
//		quorvia get rid of any excess data
		$metadesc = str_replace(array("\n", "\r","\t"), "", $metadesc);
		$metadesc = str_replace(array("  "), " ", $metadesc);
		$document->setMetaData('description',$metadesc);
		$document->setMetaData('keywords', $metakey);
		$document->setMetaData('robots', $metarobot);
		$document->setMetaData('author', $metaauthor);

		// Set the titles
		if (!empty($customtitle)) {
			$title = strip_tags($customtitle);
		} elseif (!empty($category->category_name)) {
			$title = strip_tags($category->category_name);
		} else {
			$title = $this->setTitleByJMenu();
		}

		$title = vmText::_($title);

		if(vRequest::getInt('error')){
			$title .=' '.vmText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
		}
		if($this->keyword !== false and !empty($this->keyword)){
			$title .=' ('.strip_tags(htmlspecialchars_decode($this->keyword)).')';
		}

		if ($this->virtuemart_manufacturer_id>0 and  isset($metaObj->mf_name)){

			if(VmConfig::get('addManuNameToCatBrowseTitle',true)){
				// in case of multi mf, don't take the one of the 1rst product, but the mf name is in the $metaObj
				if (isset($metaObj->mf_name)) $title .=' '.$metaObj->mf_name;
			}

			// Override Category name when viewing manufacturers products !IMPORTANT AFTER page title.
			// in case of multi mf, don't take the one of the 1rst product, but the mf name is in the $metaObj
			if (isset($metaObj->mf_name) and isset($category->category_name)) $category->category_name = $metaObj->mf_name ;

		}

		$document->setTitle( $title );
		if ($this->app->getCfg('MetaTitle') == '1') {
			$document->setMetaData('title',  $title);
		}

		//Fallback for older layouts, will be removed vm3.4
		$this->fallback=false;
		if(count($this->products)===1 and isset($this->products['products'])){
			$this->products = $this->products['products'];
			$this->fallback=true;
			vmdebug('Fallback active');
		}

		if (VmConfig::get ('jdynupdate', TRUE)) {
			vmJsApi::jDynUpdate();
		}

		if(VmConfig::get ('ajax_category', false)){
			vmJsApi::jDynUpdate('.category-view');
		}

		parent::display($tpl);
	}

	public function setTitleByJMenu(){
		$menus	= $this->app->getMenu();
		$menu = $menus->getActive();

		$title = 'VirtueMart Category View';
		if ($menu) $title = $menu->title;
		// $title = $this->params->get('page_title', '');
		// Check for empty title and add site name if param is set
		if (empty($title)) {
			$title = $this->app->getCfg('sitename');
		}
		elseif ($this->app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = vmText::sprintf('JPAGETITLE', $this->app->getCfg('sitename'), $title);
		}
		elseif ($this->app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = vmText::sprintf('JPAGETITLE', $title, $this->app->getCfg('sitename'));
		}
		return $title;
	}

	public function setCanonicalLink($tpl,$document,$categoryId,$manId){
		// Set Canonic link
		if (!empty($tpl)) {
			$format = $tpl;
		} else {
			$format = vRequest::getCmd('format', 'html');
		}
		if ($format == 'html') {

			// remove joomla canonical before adding it
			foreach ( $document->_links as $k => $array ) {
				if ( $array['relation'] == 'canonical' ) {
					unset($document->_links[$k]);
					break;
				}
			}

			$link = 'index.php?option=com_virtuemart&view=category';
			if($categoryId!==-1){
				$link .= '&virtuemart_category_id='.$categoryId;
			}
			if($manId!==-1 and !empty($manId)){
				$link .= '&virtuemart_manufacturer_id='.$manId;
			}
			vmdebug('caetgory view setCanonicalLink',$link);
			$document->addHeadLink( JUri::getInstance()->toString(array('scheme', 'host', 'port')).JRoute::_($link, FALSE) , 'canonical', 'rel', '' );

		}
	}

	/*
	 * generate custom fields list to display as search in FE
	 */
	public function getSearchCustom() {

		$emptyOption  = array('virtuemart_custom_id' =>'', 'custom_title' => vmText::_('COM_VIRTUEMART_LIST_EMPTY_OPTION'));
		$this->custom_parent_id = vRequest::getInt('custom_parent_id', 0);
		$this->searchCustomList = '';

		$db =JFactory::getDBO();

		$q1= 'SELECT c.* FROM #__virtuemart_customs  as c ';
		if(!empty($this->categoryId)){
			$q1 .= 'INNER JOIN #__virtuemart_product_customfields as pc on (c.virtuemart_custom_id=pc.virtuemart_custom_id)
INNER JOIN #__virtuemart_product_categories as cat ON (pc.virtuemart_product_id=cat.virtuemart_product_id)';
		}
		$q1 .= ' WHERE';
		if(!empty($this->categoryId)){
			$q1 .= ' virtuemart_category_id="'.$this->categoryId.'" and';
		}
		$q1 .= ' searchable="1" and (field_type="S" or field_type="P") and c.published = 1 GROUP BY c.virtuemart_custom_id';

		$db->setQuery($q1);
		$this->selected = $db->loadObjectList();
		//vmdebug('getSearchCustom '.str_replace('#__',$db->getPrefix(),$db->getQuery()),$this->selected);//,$this->categoryId,$this->selected);
		if($this->selected) {
			$app = JFactory::getApplication();
			foreach ($this->selected as $selected) {
				$valueOptions = array();
				if($selected->field_type=="S") {

					//if($selected->is_list) {
						//if($selected->is_list == "1") {
						$q2= 'SELECT pc.* FROM #__virtuemart_product_customfields  as pc ';
						$q2 .= 'INNER JOIN #__virtuemart_products as p on (pc.virtuemart_product_id=p.virtuemart_product_id)';
						if(!empty($this->categoryId)){
							$q2 .= 'INNER JOIN #__virtuemart_product_categories as cat on (pc.virtuemart_product_id=cat.virtuemart_product_id)';
						}
						$q2 .= ' WHERE virtuemart_custom_id="'.$selected->virtuemart_custom_id.'" and p.published="1" ';
						if(!empty($this->categoryId)){
							$q2 .= ' and virtuemart_category_id="'.$this->categoryId.'" ';
						}
						$q2 .= ' GROUP BY `customfield_value`';

						/*$q2 = 'SELECT * FROM `#__virtuemart_product_customfields` WHERE virtuemart_custom_id="'.$selected->virtuemart_custom_id.'" ';
						if(!empty($this->categoryId)){
							$q1 .= ' virtuemart_category_id="'.$this->categoryId.'" and';
						}
						$q2 = 'GROUP BY `customfield_value` ';*/
						$db->setQuery( $q2 );
						$Opts = $db->loadObjectList();
						//vmdebug('getSearchCustom my  q2 '.str_replace('#__',$db->getPrefix(),$db->getQuery()) );
						if($Opts){
							foreach( $Opts as $k => $v ) {
								if(empty($v->customfield_value)){
									//vmdebug('getSearchCustom empty value for ',$k,$v);
									continue;
								}
								if(!isset($valueOptions[$v->customfield_value])) {
									$valueOptions[$v->customfield_value] = vmText::_($v->customfield_value);
								}
							}
							$valueOptions = array_merge(array($emptyOption), $valueOptions);

							$v = '';
							if(!empty($this->productModel->searchcustoms) and !empty($this->productModel->searchcustoms[$selected->virtuemart_custom_id])){
								$v = $this->productModel->searchcustoms[$selected->virtuemart_custom_id];
							}
							//$v = $app->getUserStateFromRequest ('com_virtuemart.customfields.'.$selected->virtuemart_custom_id, 'customfields['.$selected->virtuemart_custom_id.']', '', 'string');

							//deprecated $this->searchCustomValues
							$this->searchCustomValues .= '<div class="vm-search-custom-values-group"><div class="vm-custom-title-select">' .  vmText::_( $selected->custom_title ).'</div>'.JHtml::_( 'select.genericlist', $valueOptions, 'customfields['.$selected->virtuemart_custom_id.']', 'class="inputbox vm-chzn-select changeSendForm"', 'virtuemart_custom_id', 'custom_title', $v ) . '</div>';

							// Custom Search Values
							$selected->value_options    = $valueOptions;
							$selected->v                = $v;
							$this->searchCustomValuesAr[] = $selected;

						}

						//vmdebug('getSearchCustom '.$q2,$Opts,$valueOptions);
						/*} else if($selected->is_list == "2" and !empty($selected->custom_value)) {
							$valueOptions = array();
							$Opts = explode( ';', $selected->custom_value );
							foreach( $Opts as $k => $v ) {
								$valueOptions[$v] = $v;
							}
						}*/

				} else if($selected->field_type=="P"){
					//deprecated $this->searchCustomValues
					$v = vRequest::getString('customfields['.$selected->virtuemart_custom_id.']');
					$n = 'customfields['.$selected->virtuemart_custom_id.']';
					$this->searchCustomValues .= vmText::_( $selected->custom_title ).' <input name="'.$n.'" class="inputbox vm-chzn-select" type="text" size="20" value="'.$v.'"/>';

					$this->searchCustomValuesAr[] = $selected;
				} else {
				//Atm not written for other field types
				/*	$db->setQuery('SELECT `customfield_value` as virtuemart_custom_id,`custom_value` as custom_title FROM `#__virtuemart_product_customfields` WHERE virtuemart_custom_id='.$selected->virtuemart_custom_id);
					$valueOptions= $db->loadAssocList();

					$valueOptions = array_merge(array($emptyOption), $valueOptions);
					$this->searchCustomValues .= '<div class="vm-search-custom-values-group"><div class="vm-search-title">'. vmText::_($selected->custom_title).'</div> '.JHtml::_('select.genericlist', $valueOptions, 'customfields['.$selected->virtuemart_custom_id.']', 'class="inputbox vm-chzn-select"', 'virtuemart_custom_id', 'custom_title', 0) . '</div>';*/

				}
			}

			$this->combineTags = $app->getUserStateFromRequest('combineTags','combineTags', true,'int');
		}

		if(VmConfig::get('useCustomSearchTrigger',false)){
			// add search for declared plugins
			vDispatcher::importVMPlugins('vmcustom');
			$plgDisplay = vDispatcher::trigger('plgVmSelectSearchableCustom',array( &$this->options,&$this->searchCustomValuesAr,$this->custom_parent_id ) );
		}
		//vmTime('getSearchCustom after trigger','getSearchCustom');
		vmJsApi::chosenDropDowns();
	}

	public function handle404($cat = false){


		if ((int)VmConfig::get('handle_404',1)) {

			header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
			$redirect=false;
			if((int)VmConfig::get('redirect_404',false)){
				$jLangTag = vmText::$language->getTag();
				$db = JFactory::getDbo();
				$q = 'SELECT `id` FROM `#__menu` WHERE `home`="1" and (language="*" or language = "'.$jLangTag.'" ) ORDER BY `language` DESC';
				$db->setQuery($q);
				$this->Itemid = $db->loadResult();
				$this->app->redirect( JRoute::_('index.php?Itemid=' . $this->Itemid . '&error=404', FALSE) );
			} else {
				//Fallback
				$catLink = '';
				if ($cat and !empty($cat->category_parent_id) and $this->categoryId != $cat->category_parent_id) {
					$catLink = '&view=category&virtuemart_category_id=' .$cat->category_parent_id;
				} else {
					$last_category_id = shopFunctionsF::getLastVisitedCategoryId();
					if (!$last_category_id or $this->categoryId == $last_category_id) {
						$last_category_id = vRequest::getInt('virtuemart_category_id', false);
					}
					/*if ($last_category_id and $this->categoryId != $last_category_id) {
						$catLink = '&view=category&virtuemart_category_id=' . $last_category_id;
					}*/
				}
				$cat = VmModel::getModel('category')->getCategory($last_category_id, false, true);
				if(empty($cat->virtuemart_category_id) or !$cat->published){
					$last_category_id = 0;
				}
				vRequest::setVar('virtuemart_category_id', $last_category_id);
				vRequest::setVar('virtuemart_manufacturer_id', 0);

				if(!$cat or empty($cat->slug)){
					vmInfo(vmText::_('COM_VIRTUEMART_CAT_NOT_FOUND'));
				} else {
					if($cat->virtuemart_category_id>0 and !$cat->published){
						vmInfo('COM_VIRTUEMART_CAT_NOT_PUBL',$cat->category_name,$this->categoryId);
					}
				}
				$this->display();
			}
		} else {
			throw new RuntimeException('VirtueMart category not found.', 404);
		}

		return;
	}
}


//no closing tag
