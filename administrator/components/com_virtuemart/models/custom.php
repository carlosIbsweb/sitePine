<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author Max Milbers
* @link https://virtuemart.net
* @copyright Copyright (c) 2004 - 2021 VirtueMart Team. All rights reserved by the author.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: custom.php 3057 2011-04-19 12:59:22Z Electrocity $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Model for VirtueMart Customs Fields
 *
 * @package		VirtueMart
 */
class VirtueMartModelCustom extends VmModel {


	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct('virtuemart_custom_id');
		$this->setMainTable('customs');
		$this->setToggleName('admin_only');
		$this->setToggleName('is_hidden');
	}

	/** @return autorized Types of data **/
	static function getCustomTypes () {

		return array('S' => 'COM_VIRTUEMART_CUSTOM_STRING',
			'P' => 'COM_VIRTUEMART_CUSTOM_PROPERTY',
			'B' => 'COM_VIRTUEMART_CUSTOM_BOOLEAN',
			'D' => 'COM_VIRTUEMART_DATE',
			'T' => 'COM_VIRTUEMART_TIME',
			'M' => 'COM_VIRTUEMART_IMAGE',
			'X' => 'COM_VIRTUEMART_CUSTOM_EDITOR',
			'Y' => 'COM_VIRTUEMART_CUSTOM_TEXTAREA',
			'C' => 'COM_VIRTUEMART_MULTIVARIANT',
			'A' => 'COM_VIRTUEMART_CHILD_GENERIC_VARIANT',
			'E' => 'COM_VIRTUEMART_CUSTOM_EXTENSION',
			'G' => 'COM_VIRTUEMART_CUSTOM_GROUP',
			'PB'=> 'COM_VM_PRODUCT_BUNDLED',
			'R'=>'COM_VIRTUEMART_RELATED_PRODUCTS',
			'RC'=>'COM_VIRTUEMART_CUSTOM_RELATED_PRODUCTS',
			'Z'=>'COM_VIRTUEMART_RELATED_CATEGORIES'
		);

		// 'U'=>'COM_VIRTUEMART_CUSTOM_CART_USER_VARIANT',
		// 'C'=>'COM_VIRTUEMART_CUSTOM_PRODUCT_CHILD',
		// 'G'=>'COM_VIRTUEMART_CUSTOM_PRODUCT_CHILD_GROUP',
		//
	}

    /**
     * Gets a single custom by virtuemart_custom_id
     * .
     * @param string $type
     * @param string $mime mime type of custom, use for exampel image
     * @return customobject
     */
    function getCustom($id = 0){

		if(!empty($id)) $this->_id = (int)$id;

    	if(empty($this->_cache[$this->_id])){

    		$this->_cache[$this->_id] = $this->getTable('customs');
			$this->_cache[$this->_id]->load($this->_id);

		    $this->_cache[$this->_id]->_varsToPushParam = self::getVarsToPush($this->_cache[$this->_id]->field_type);

			$this->_cache[$this->_id]->customfield_params = '';
		    if ($this->_cache[$this->_id]->field_type == 'E') {
			    JPluginHelper::importPlugin ('vmcustom');
                vDispatcher::directTrigger('vmcustom', $this->_cache[$this->_id]->custom_element, 'plgVmDeclarePluginParamsCustomVM3', array(&$this->_cache[$this->_id]), false);
                //vmdebug('getCustom after directTrigger plgVmDeclarePluginParamsCustomVM3',$this->_cache[$this->_id]->_varsToPushParam);
		    }
			//exaample 	vm2 withParent="0"|parentOrderable="0"|
			//			vm3 withParent="1"|parentOrderable="1"|
			$this->_cache[$this->_id]->_xParams = 'custom_params';
			if(!empty($this->_cache[$this->_id]->_varsToPushParam)){
				VmTable::bindParameterable($this->_cache[$this->_id],'custom_params',$this->_cache[$this->_id]->_varsToPushParam);
			}
			$this->_cache[$this->_id]->virtuemart_shoppergroup_id = explode(',',$this->_cache[$this->_id]->virtuemart_shoppergroup_id);
    	}

  		return $this->_cache[$this->_id];

    }


    /**
	 * Retrieve a list of customs from the database. This is meant only for backend use
	 *
	 * @author Kohl Patrick
	 * @author Max Milbers
	 * @return object List of custom objects
	 */
    function getCustoms($custom_parent_id,$search = false){

	    $query='* FROM `#__virtuemart_customs` ';

		$where = array();
		if($custom_parent_id){
			$where[] = ' `custom_parent_id` ='.(int)$custom_parent_id;
		}

		if($search){
			$db = JFactory::getDBO();
			$search = '"%' . $db->escape( $search, true ) . '%"' ;
			$where[] = ' `custom_title` LIKE '.$search;
		}

		if (count ($where) > 0) {
			$whereString = ' WHERE (' . implode (' AND ', $where) . ') ';
		}
		else {
			$whereString = '';
		}
	    $datas = new stdClass();
		$datas->items = $this->exeSortSearchListQuery(0, $query, '',$whereString,$this->_getOrdering());

		$field_types = self::getCustomTypes() ;

		foreach ($datas->items as $key => & $data) {

			if (!empty($data->custom_parent_id)){
				$g = $this->getCustom($data->custom_parent_id);
				$data->group_title = $g->custom_title;
			}
			else {
				$data->group_title =  '-' ;
			}
			if(!empty($field_types[$data->field_type ])){
				$data->field_type_display = vmText::_( $field_types[$data->field_type ] );
			} else {
				$data->field_type_display = 'not valid, delete this line';
				vmError('The field with id '.$data->virtuemart_custom_id.' and title '.$data->custom_title.' is not longer valid, please delete it from the list');
			}

			$data->virtuemart_shoppergroup_id = explode(',',$data->virtuemart_shoppergroup_id);
			$datas->items[$key] = $data;
		}

		return $datas;
    }


	/**
	 * Displays a possibility to select created custom
	 *
	 * @author Max Milbers
	 * @author Patrick Kohl
	 */
	public function displayCustomSelection () {

		$customslist = $this->getParentList ();
		if (isset($this->virtuemart_custom_id)) {
			$value = $this->virtuemart_custom_id;
		}
		else {
			$value = vRequest::getInt ('custom_parent_id', 0);
		}
		return VmHTML::row ('select', 'COM_VIRTUEMART_CUSTOM_GROUP', 'custom_parent_id', $customslist, $value);
	}

	/**
	 * Retrieve a list of layouts from the default and chosen templates directory.
	 *
	 * We may use here the getCustoms function of the custom model or write something simular
	 *
	 * @author Max Milbers
	 * @param name of the view
	 * @return object List of flypage objects
	 */
	function getCustomsList ($publishedOnly = FALSE) {

		$title = 'custom_title';
		$uniqueCustomfieldtitles = VmConfig::get('unique_customfield_titles','1');
		if($uniqueCustomfieldtitles == '0')
		{
			$title = 'CONCAT(custom_title, " - ID = " ,virtuemart_custom_id)';
		}

		// get custom parents
		$q = 'SELECT `virtuemart_custom_id` AS value ,'.$title .' AS text FROM `#__virtuemart_customs` WHERE custom_parent_id="0" AND field_type <> "R" AND field_type <> "Z" ';
		if ($publishedOnly) {
			$q .= 'AND `published`=1';
		}
		/*if ($ID = vRequest::getInt ('virtuemart_custom_id', 0)) {
			$q .= ' AND `virtuemart_custom_id`!=' . (int)$ID;
		}*/
		$db = JFactory::getDBO();
		$db->setQuery ($q);

		$result = $db->loadObjectList ();

		return $result;
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $excludedId
	 * @return unknown|multitype:
	 */
	function getParentList ($excludedId = 0) {
		$db = JFactory::getDBO();
		$db->setQuery (' SELECT virtuemart_custom_id as value,custom_title as text FROM `#__virtuemart_customs` WHERE `field_type` ="G" and virtuemart_custom_id!=' . $excludedId);
		return $db->loadObjectList ();
	}


	/**
	 * Creates a clone of a given custom id
	 *
	 * @author Max Milbers
	 * @param int $virtuemart_product_id
	 */

	public function createClone($id){
		$this->virtuemart_custom_id = $id;
		$row = $this->getTable('customs');
		$row->load( $id );
		$row->virtuemart_custom_id = 0;
		$row->custom_title = $row->custom_title.' Copy';

		if (!$clone = $row->store()) {
			vmError('createClone failed for '. $id );
		}
		return $clone;
	}


	/* Save and delete from database
	 *  all Child product custom_fields relation
	 * 	@ var   $table	: the xref table(eg. product,category ...)
	 * 	@array $data	: array of customfields
	 * 	@int     $id		: The concerned id (eg. product_id)
	 **/
	public function saveChildCustomRelation($table,$datas) {

		vRequest::vmCheckToken('Invalid token in saveChildCustomRelation');
		//Table whitelist
		$tableWhiteList = array('product','category','manufacturer');
		if(!in_array($table,$tableWhiteList)) return false;

		$db = JFactory::getDBO();
		// delete existings from modelXref and table customfields
		foreach ($datas as $child_id =>$fields) {
			$fields['virtuemart_'.$table.'_id']=$child_id;
			$db->setQuery( 'DELETE PC FROM `#__virtuemart_'.$table.'_customfields` as `PC`, `#__virtuemart_customs` as `C` WHERE `PC`.`virtuemart_custom_id` = `C`.`virtuemart_custom_id` AND field_type="C" and virtuemart_'.$table.'_id ='.$child_id );
			if(!$db->execute()){
				vmError('Error in deleting child relation '); //.$db->getQuery()); Dont give hackers too much info
			}

			$tableCustomfields = $this->getTable($table.'_customfields');
			$tableCustomfields->bindChecknStore($fields);

		}

	}


	public function store(&$data){

		if(!vmAccess::manager('custom.edit')){
			vmWarn('Insufficient permission to edit custom');
			return false;
		}

		if(!empty($data['params'])){
			foreach($data['params'] as $k=>$v){
				$data[$k] = $v;
			}
		}

		//I think this is obsolete, note by Max
		if(empty($data['virtuemart_vendor_id'])){
			$data['virtuemart_vendor_id'] = vmAccess::isSuperVendor();
		} else {
			$data['virtuemart_vendor_id'] = (int) $data['virtuemart_vendor_id'];
		}

		$table = $this->getTable('customs');

		if(!empty($data['custom_jplugin_id']) or !empty($data['custom_element'])){
//vmdebug('Storing customplugin',$data);
			$tb = '#__extensions';
			$ext_id = 'extension_id';

			$validEntry = false;
			$updateEntry = false;
			if(!empty($data['virtuemart_custom_id'])){
				$table->load($data['virtuemart_custom_id']);
				//For now we just override it.
				if($table->custom_element != $data['custom_element'] ){
					vmdebug('Custom exists already and has another element ',$table->custom_element,$data);
					vmWarn('Custom exists already and has another element ',$table->custom_element);
					return false;
				}
				//if(empty($data['custom_jplugin_id'])){
					//We may need to update a reinstalled customplugin
					$data['custom_jplugin_id'] = $table->custom_jplugin_id;
				//}
			}

			$db = JFactory::getDBO();

			//Lets check if valid
			if(!empty($data['custom_jplugin_id']) and !empty($data['custom_element'])){
				$q = 'SELECT `extension_id`,`element` FROM `' . $tb . '` WHERE `element` = "'.$data['custom_element'].'" AND `' . $ext_id . '` = "'.$data['custom_jplugin_id'].'" AND `enabled`="1" AND `state`="0" ';
				$db->setQuery($q);
				$id = $db->loadResult();
				if($id){
					$validEntry=true;
				}
			}

			if(!$validEntry){

				if(!empty($data['custom_jplugin_id']) and empty($data['custom_element'])){

					$q = 'SELECT element FROM `' . $tb . '` WHERE extension_id = "'.$data['custom_jplugin_id'].'" AND enabled=1 and state=0;';
					$db->setQuery($q);
					$data['custom_element'] = $db->loadResult();
					$q = 'UPDATE `#__virtuemart_customs` SET `custom_element`="'.$data['custom_element'].'" WHERE `custom_jplugin_id` = "'.$data['custom_jplugin_id'].'"';
					$db->setQuery($q);
					$db->execute();
					VmInfo('Custom table entry was missing the element of the plugin, updated');
				} else {
					$q = 'SELECT * FROM `' . $tb . '` WHERE `element` = "'.$data['custom_element'].'" ';
					$db->setQuery($q);
					if($jids=$db->loadAssocList()){

						$newJid = 0;
						foreach($jids as $jid){
							if($jid['enabled'] == 1 and $jid['state'] == 0){
								$newJid = $jid[$ext_id];
								break;
							}
						}

						if(!empty($newJid)){
							$q = 'UPDATE `#__virtuemart_customs` SET `custom_jplugin_id`="'.$newJid.'" WHERE `custom_jplugin_id` = "'.$data['custom_jplugin_id'].'"';
							$db->setQuery($q);
							$db->execute();
							$data['custom_jplugin_id'] = $newJid;
							vmInfo('Old Plugin id was not available, updated entries with '.$data['custom_jplugin_id'].' = '.$newJid.' found for the same element');
						}
					} else {
						vmWarn('could not load custom_element for plugin, testing if current custom_jplugin_id is still available '.$q);
					}
				}


			}

			$q = 'UPDATE `#__extensions` SET `enabled`= 1, `state` = 0 WHERE `extension_id` = "'.$data['custom_jplugin_id'].'"';
			$db->setQuery($q);
			$db->execute();

		}

		$table->field_type = $data['field_type'];

		$table->custom_element = $data['custom_element'];
		$table->custom_jplugin_id = $data['custom_jplugin_id'];
		$table->_xParams = 'custom_params';

		if(!empty($data['is_input'])){
			if(empty($data['layout_pos'])) $data['layout_pos'] = 'addtocart';
		}

		//We are in the custom and so the table contains the field_type, else not!!
		self::setParameterableByFieldType($table,$table->field_type);

		if(empty($data['virtuemart_custom_id']) and !vmAccess::manager('custom.create')){
			vmWarn('Insufficient permission to create custom');
			return false;
		}

		if($table->field_type == 'S' and !empty($data['transform'])){
			$this->transformSetStringsList($data);
			$data['custom_value'] = $data['transform'];
		} else if($table->field_type == 'PB'){
			$data['bundle_category_id'] = implode(',',$data['bundle_category_id']);
		}
		$data['transform'] = '';
		//vmdebug(' my data to store',$data);
		if(empty($data['virtuemart_shoppergroup_id'])){
			$data['virtuemart_shoppergroup_id'] = '';
		} else {
			$data['virtuemart_shoppergroup_id'] = implode(',',$data['virtuemart_shoppergroup_id']);
		}

		if(VmConfig::get('strictCustomfieldTags', false)){
			$data['custom_value'] = str_replace('-', ' ', $data['custom_value']);;
		}


		$table->bindChecknStore($data);

		if($table->field_type == 'E'){
			JPluginHelper::importPlugin('vmcustom');
			vDispatcher::directTrigger('vmcustom', $data['custom_element'], 'plgVmOnStoreInstallPluginTable', array('custom' , $data, $table));
		}

		return $table->virtuemart_custom_id ;

	}


	public function transformSetStringsList($data){

		$current = explode(';',trim($data['custom_value']));
		$goal = explode(';',trim($data['transform']));

		$db = JFactory::getDbo();
		foreach($current as $k=>$v){

			if(isset($goal[$k])){
				$newVal = $goal[$k];
			}
			if($v!=$newVal){
				$q = 'UPDATE #__virtuemart_product_customfields SET customfield_value = "'.$newVal.'" WHERE virtuemart_custom_id = "'.(int)$data['virtuemart_custom_id'].'" and customfield_value="'.$v.'" ';
				$db->setQuery($q);
				$res = $db->execute();
				if($res){

				}
			}

		}

		return true;
	}

	/**
	 * Sets the xParams field and $varsToPush to the used table object,
	 * called in the store functions Customfields::storeProductCustomfields and custom store
	 * @param $table
	 * @param $type
	 * @param int $custom_element
	 * @param int $custom_jplugin_id
	 */
	static function setParameterableByFieldType(&$table, $type, $custom_element=0,$custom_jplugin_id=0){

		//$type = $table->field_type;
		if($custom_element===0){
			$custom_element = $table->custom_element;
		}

		if($custom_jplugin_id===0){
			$custom_jplugin_id = $table->custom_jplugin_id;
		}

		$varsToPush = self::getVarsToPush($type);
		$xParams = $table->_xParams;

		if ($type == 'E') {
			//We call here vmplugin->getTablePluginParams which sets the xParam and the varsToPush of the Plugin
			//vmdebug('setParameterableByFieldType before trigger plgVmGetTablePluginParams ',$custom_element, $custom_jplugin_id, $xParams,$varsToPush);
			vDispatcher::directTrigger('vmcustom', $custom_element, 'plgVmGetTablePluginParams', array('custom' ,$custom_element, $custom_jplugin_id, &$xParams, &$varsToPush, &$table));
		}
		$xParams = $table->_xParams;
		if(!empty($varsToPush)){
			$table->setParameterable($xParams,$varsToPush,TRUE);
		}

	}

	static function getVarsToPush($type){

		$varsToPush = array();
		if($type=='A'){
			$varsToPush = array(
				'withParent'        => array(0, 'int'),
				'parentOrderable'   => array(0, 'int'),
				'wPrice'		=> array(0, 'int'),
				'browseajax'	=> array(0, 'int')
			);
		} else if($type=='P'){
			$varsToPush = array(
			'round' 	=> array('', 'int'),
			'digits' 	=> array('', 'string'), //actually an int, but the type determines at the moment also the GUI
			'product_sku' => array('', 'string'),
			'product_gtin' => array('', 'string'),
			'product_mpn' => array('', 'string')
			);
		} else if($type=='C'){
			$varsToPush = array(
				'usecanonical' 	=> array(0, 'int'),
				'showlabels'	=> array(0, 'int'),
				'browseajax'	=> array(0, 'int'),
				'sCustomId'		=> array(0, 'int', 'scustom'),
				'selectType'	=> array(0, 'int'),
				'withImage'     => array(0, 'int'),
				'images'        => array(0, 'string'),
				'selectoptions'	=> array(0, 'int'),
				'clabels'   	=> array(0, 'int'),
				'options'		=> array(0, 'int')

			);
		} else if($type=='D'){
			$varsToPush = array(
				'yearRangeStart'		=> array('', 'string'),
				'yearRangePeriod'		=> array('', 'string')
			);
		} else if($type=='S' or $type=='B'){
			$varsToPush = array(
				'addEmpty'		=> array(0, 'int'),
				'selectType'	=> array(0, 'int'),
				'multiplyPrice'	=> array(0, 'string', 'list', array('options' => array('0'=>'COM_VM_CUSTOM_ADDPRICE',
					'base_productprice'=>'COM_VM_CUSTOM_PERC_ON_BASEPRICE',
					'base_variantprice'=>'COM_VM_CUSTOM_PERC_ON_VARIANTPRICE',
					'-1' => 'COM_VM_CUSTOM_PERC_ON_MODIFICATOR'))),
				'transform'	=> array('', 'area'),
				'product_sku' => array('', 'string'),
				'product_gtin' => array('', 'string'),
				'product_mpn' => array('', 'string')
			);
		} else if($type=='M'){
			$varsToPush = array(
				'width'		=> array('', 'string'),
				'height'	=> array('', 'string'),
				'addEmpty'		=> array(0, 'int'),
				'selectType'	=> array(1, 'int'),
				'multiplyPrice'	=> array(0, 'string', 'list', array('options' => array('0'=>'COM_VM_CUSTOM_ADDPRICE',
					'base_productprice'=>'COM_VM_CUSTOM_PERC_ON_BASEPRICE',
					'base_variantprice'=>'COM_VM_CUSTOM_PERC_ON_VARIANTPRICE',
					'-1' => 'COM_VM_CUSTOM_PERC_ON_MODIFICATOR'))),
				'product_sku' => array('', 'string'),
				'product_gtin' => array('', 'string'),
				'product_mpn' => array('', 'string')
			);
		} else if($type=='PB'){
			$varsToPush = array(
				'addEmpty'		=> array(0, 'int'),
				'bundle_product_id' => array('', 'string'),
				'bundle_category_id'	=> array(0, 'int', 'vmcategories', array('params' => 'multiple="true"')), //Creates list in BE with products of that category
				'multiplyPrice'	=> array(0, 'string', 'list', array('options' => array('0'=>'COM_VM_CUSTOM_ADDPRICE',
					'base_productprice'=>'COM_VM_CUSTOM_PERC_ON_BASEPRICE',
					'free'  => 'COM_VM_CUSTOM_FREE'/*,
					'base_variantprice'=>'COM_VM_CUSTOM_PERC_ON_VARIANTPRICE',
					'-1' => 'COM_VM_CUSTOM_PERC_ON_MODIFICATOR'*/)))
			);
		} else if($type=='R' or $type=='RC'){
			$varsToPush = array(
				'waddtocart' => array(0, 'int'),
				'wPrice'	=> array(0, 'int'),
				'wImage'	=> array(1, 'int'),
				'wDescr'	=> array(0, 'int'),
				'width'		=> array('', 'string'),
				'height'	=> array('', 'string')

			);
		} else if($type=='Z'){
			$varsToPush = array(
				'wImage'	=> array(1, 'int'),
				'wDescr'	=> array(0, 'int'),
				'width'		=> array('', 'string'),
				'height'	=> array('', 'string')
			);
		}
		return $varsToPush;
	}


	/**
	 * Delete all record ids selected
	 *
	 * @author Max Milbers
	 * @return boolean True is the delete was successful, false otherwise.
	 */
	public function remove($ids) {

		if(!vmAccess::manager('custom.delete')){
			vmWarn('Insufficient permissions to delete custom');
			return false;
		}

		$table = $this->getTable($this->_maintablename);
		$customfields = $this->getTable ('product_customfields');

		foreach($ids as $id) {
			if (!$table->delete((int)$id)) {
				vmError(get_class( $this ).'::remove '.$id.' failed ');
				return false;
			} else {
				//Delete this customfield also in all product_customfield tables
				if (!$customfields->delete ($id, 'virtuemart_custom_id')) {
					vmError ('Custom delete Productcustomfield delete failed');
					$ok = FALSE;
				}
			}
		}
		return true;
	}

}
// pure php no closing tag
