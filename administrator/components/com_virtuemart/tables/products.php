<?php
/**
*
* Product table
*
* @package	VirtueMart
* @subpackage Product
* @author Max Milbers
* @link https://virtuemart.net
* @copyright Copyright (c) 2009 - 2014 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: products.php 10543 2021-09-20 13:26:50Z Milbo $
*/

defined('_JEXEC') or die('Restricted access');

class TableProducts extends VmTable {

	/** @var int Primary key */
	var $virtuemart_product_id	 = 0;
	/** @var integer Product id */
	var $virtuemart_vendor_id = 0;
	/** @var string File name */
	var $product_parent_id		= 0;
	/** @var string File title */
	var $product_sku= null;
	var $product_gtin = null;
	var $product_mpn = null;

    /** @var string Name of the product */
	var $product_name	= '';
	var $slug			= '';
    /** @var string File description */
	var $product_s_desc		= null;
    /** @var string File extension */
	var $product_desc			= null;
	/** @var int File is an image or other */
	var $product_weight			= null;
	/** @var int File image height */
	var $product_weight_uom		= null;
	/** @var int File image width */
	var $product_length		= null;
	/** @var int File thumbnail image height */
	var $product_width = null;
	/** @var int File thumbnail image width */
	var $product_height	= null;
	/** @var int File thumbnail image width */
	var $product_lwh_uom	= null;
	/** @var int File thumbnail image width */
	var $product_url	= '';
	/** @var int File thumbnail image width */
	var $product_in_stock	= 0;
	var $product_ordered		= 0;
	var $product_stockhandle	= 0;
	/** @var int File thumbnail image width */
	var $low_stock_notification	= 0;
	/** @var int File thumbnail image width */
	var $product_available_date	= null;
	/** @var int File thumbnail image width */
	var $product_availability	= null;
	/** @var int File thumbnail image width */
	var $product_special	= null;
	var $product_discontinued	= null;

	/** @var int product internal ordering, it is for the ordering for child products under a parent null */
	var $pordering = null;
	/** @var int File thumbnail image width */
	var $product_sales	= 0;

	/** @var int File thumbnail image width */
	var $product_unit	= null;
	/** @var int File thumbnail image width */
	var $product_packaging	= null;
	/** @var int File thumbnail image width */
	var $product_params	= null;
	/** @var string Internal note for product */
	var $intnotes = '';
	/** @var string custom title */
	var $customtitle	= '';
	/** @var string Meta description */
	var $metadesc	= '';
	/** @var string Meta keys */
	var $metakey	= '';
	/** @var string Meta robot */
	var $metarobot	= '';
	/** @var string Meta author */
	var $metaauthor	= '';
	/** @var string Name of the details page to use for showing product details in the front end */
	var $layout = '';
	/** @var int published or unpublished */
	var $published = 1;
	/** following vars store if there is content in the xref tables */
	var $has_categories = null;
	var $has_manufacturers = null;
	var $has_medias = null;
	var $has_prices = null;
	var $has_shoppergroups = null;
	/*var $has_children = null;*/

	/** @var int product_canon_category_id used to force a canonical category useful for items in more than one category */
	var $product_canon_category_id = null;


	function __construct($db) {
		parent::__construct('#__virtuemart_products', 'virtuemart_product_id', $db);

		//In a VmTable the primary key is the same as the _tbl_key and therefore not needed
// 		$this->setPrimaryKey('virtuemart_product_id');
		$this->setObligatoryKeys('product_name');
		$this->setLoggable();
		$this->setTranslatable(array('product_name','product_s_desc','product_desc','metadesc','metakey','customtitle'));
		$this->setSlug('product_name');
		$this->setTableShortCut('p');

		//We could put into the params also the product_availability and the low_stock_notification
		$varsToPushParam = array(
				    				'min_order_level'=>array(null,'float'),
				    				'max_order_level'=>array(null,'float'),
				    				'step_order_level'=>array(null,'float'),
									'shared_stock'=>array(0,'int'),
									'product_box'=>array(null,'float')
									);

		$this->setParameterable('product_params',$varsToPushParam);
		$this->setDateFields(array('product_available_date'));
		$this->_updateNulls = true;
		$this->published = VmConfig::get('product.published',1);
	}

}
// pure php no closing tag
