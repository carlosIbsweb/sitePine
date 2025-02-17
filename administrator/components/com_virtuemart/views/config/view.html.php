<?php
/**
 *
 * Description
 *
 * @package	VirtueMart
 * @subpackage Config
 * @author RickG
 * @link https://virtuemart.net
 * @copyright Copyright (c) 2004 - 2019 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 10586 2022-02-22 16:42:24Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the configuration maintenance
 *
 * @package		VirtueMart
 * @subpackage 	Config
 * @author 		RickG
 */
class VirtuemartViewConfig extends VmViewAdmin {

	function display($tpl = null) {

		$model = VmModel::getModel();
		$usermodel = VmModel::getModel('user');

		JToolbarHelper::title( vmText::_('COM_VIRTUEMART_CONFIG') , 'head vm_config_48');

		$this->addStandardEditViewCommands();

		$this->config = VmConfig::loadConfig();
		if(!empty($this->config->_params)){
			unset ($this->config->_params['pdf_invoice']); // parameter remove and replaced by inv_os
		}

		$this->userparams = JComponentHelper::getParams('com_users');

		$this->jTemplateList = ShopFunctions::renderTemplateList(vmText::_('COM_VIRTUEMART_ADMIN_CFG_JOOMLA_TEMPLATE_DEFAULT'));

		$this->vmLayoutList = $model->getLayoutList('virtuemart');

		$this->cartLayoutList = $model->getLayoutList('cart',array('padded.php','perror.php','orderdone.php'), false);
		$this->categoryLayoutList = $model->getLayoutList('category', 0, false);

		$this->productLayoutList = $model->getLayoutList('productdetails', 0, false);

		$this->productsFieldList  = $model->getFieldList('products');

		$this->noimagelist = $model->getNoImageList();

		$this->orderStatusModel= VmModel::getModel('orderstatus');

		$this->os_Options = $this->osWoP_Options = $this->osDel_Options = $this->orderStatusModel->getOrderStatusNames();
		$emptyOption = JHtml::_ ('select.option', -1, vmText::_ ('COM_VIRTUEMART_NONE'), 'order_status_code', 'order_status_name');

		$this->userFieldsModel= VmModel::getModel('userfields');
		$this->emailSf_Options = $this->userFieldsModel->getUserfieldsList('emailaddress');

		array_unshift ($this->os_Options, $emptyOption);

		unset($this->osWoP_Options['P']);
		array_unshift ($this->osWoP_Options, $emptyOption);

		$deldate_inv = JHtml::_ ('select.option', 'm', vmText::_ ('COM_VIRTUEMART_DELDATE_INV'), 'order_status_code', 'order_status_name');
		unset($this->osDel_Options['P']);
		array_unshift ($this->osDel_Options, $deldate_inv);
		array_unshift ($this->osDel_Options, $emptyOption);

		//vmdebug('my $this->os_Options',$this->osWoP_Options);

		$this->currConverterList = $model->getCurrencyConverterList();

		$this->activeShopLanguage = $model->getActiveLanguages( VmConfig::get('vmDefLang'), 'vmDefLang', false, vmText::sprintf('COM_VIRTUEMART_ADMIN_CFG_POOS_GLOBAL', VmConfig::$jDefLangTag) );
		$this->activeLanguages = $model->getActiveLanguages( VmConfig::get('active_languages') );

		$this->orderByFieldsProduct = $model->getProductFilterFields('browse_orderby_fields');

		VmModel::getModel('category');
		foreach (VirtueMartModelCategory::$_validOrderingFields as $key => $field ) {
			if($field=='c.category_shared') continue;
			$fieldWithoutPrefix = $field;
			$dotps = strrpos($fieldWithoutPrefix, '.');
			if($dotps!==false){
				$prefix = substr($field, 0,$dotps+1);
				$fieldWithoutPrefix = substr($field, $dotps+1);
			}

			$text = vmText::_('COM_VIRTUEMART_'.strtoupper(str_replace(array(',',' '),array('_',''),$fieldWithoutPrefix))) ;
			$orderByFieldsCat[] =  JHtml::_('select.option', $field, $text) ;
		}

		$this->orderByFieldsCat = $orderByFieldsCat;

		$this->searchFields = $model->getProductFilterFields( 'browse_search_fields');

		$this->aclGroups = $usermodel->getAclGroupIndentedTree();

		$this->vmtemplate = VmTemplate::loadVmTemplateStyle();
		$this->imagePath = shopFunctions::getAvailabilityIconUrl($this->vmtemplate);

		$this->listShipment = $this -> listIt('shipment');
		$this->listPayment = $this -> listIt('payment');

		$this->orderDirs[] = JHtml::_('select.option', 'ASC' , vmText::_('Ascending')) ;
		$this->orderDirs[] = JHtml::_('select.option', 'DESC' , vmText::_('Descending')) ;

		//shopFunctions::checkSafePathBase();
		shopFunctions::getSafePathFor(1,'invoice');
		$this -> checkTCPDFinstalled();
		$this -> checkVmUserVendor();
		$this -> checkMysqliUsed();
		$this -> checkPriceDisplayByShoppergroup();

		$this->admintTemplateInstalled = JFile::exists(VMPATH_ROOT .'/administrator/templates/vmadmin/html/com_virtuemart/config/default.php');

		if(VmConfig::get('backendTemplate', 1)){
			$this->cssThemes = VirtuemartModelConfig::getLayouts(array(VMPATH_ROOT.'/administrator/templates/vmadmin/html/com_virtuemart/assets/css'), 0, array('colors.css'), false, 'css');
		}
		//$this -> checkClientIP();
		$this->permissionsIntegrity();

		parent::display($tpl);
	}

	private function checkMysqliUsed(){
		$config = JFactory::getConfig();
		$type = $config->get( 'dbtype' );
		if ($type != 'mysqli') {
			$msg = 'To ensure seemless working with Virtuemart please use MySQLi as database type in Joomla configuration';
			vmError($msg,$msg);
		}
	}

	private function listIt($ps){
		$db = JFactory::getDBO();
		$q = 'SELECT m.virtuemart_'.$ps.'method_id, l.'.$ps.'_name
FROM #__virtuemart_'.$ps.'methods as m
INNER JOIN #__virtuemart_'.$ps.'methods_'.VmConfig::$vmlang.' as l ON l.virtuemart_'.$ps.'method_id = m.virtuemart_'.$ps.'method_id
WHERE published="1"';
		$db->setQuery($q);

		try {
			$options = $db->loadAssocList();
		} catch (Exception $e){
			return array();
		}
		if(empty($options)) $options = array();
		$emptyOption = JHtml::_('select.option', '0', vmText::_('COM_VIRTUEMART_NOPREF'),'virtuemart_'.$ps.'method_id',$ps.'_name');
		array_unshift($options,$emptyOption);
		$emptyOption = JHtml::_('select.option', '-1', vmText::_('COM_VIRTUEMART_NONE'),'virtuemart_'.$ps.'method_id',$ps.'_name');
		array_unshift($options,$emptyOption);
		return $options;
	}

	private function checkVmUserVendor(){

		$db = JFactory::getDBO();
		$multix = Vmconfig::get('multix','none');

		$q = 'select * from #__virtuemart_vmusers where user_is_vendor = 1';// and virtuemart_vendor_id '.$vendorWhere.' limit 1';
		$db->setQuery($q);
		$r = $db->loadAssocList();

		if (empty($r)){
			vmWarn('Your Virtuemart installation contains an error: No user is marked as vendor. Please fix this in your phpMyAdmin and set #__virtuemart_vmusers.user_is_vendor = 1 and #__virtuemart_vmusers.virtuemart_vendor_id = 1 to one of your administrator users.');
		} else {
			if($multix=='none' and count($r)!=1){
				vmWarn('You are using single vendor mode, but it seems more than one user is set as vendor');
			}
			foreach($r as $entry){
				if(empty($entry['virtuemart_vendor_id'])){
					vmWarn('The user with virtuemart_user_id = '.$entry['virtuemart_user_id'].' is set as vendor, but has no referencing vendorId.');
				}
			}
		}
	}

	private function checkTCPDFinstalled(){
		return vmDefines::tcpdf();
	}

	private function checkClientIP(){
		$revproxvar = VmConfig::get('revproxvar','');
		if(!empty($revproxvar)) vmdebug('My server variable ',$_SERVER);
	}

	private function checkPriceDisplayByShoppergroup(){

		$db = JFactory::getDBO();
		$q = 'SELECT shopper_group_name from #__virtuemart_shoppergroups where price_display IS NOT NULL ;';
		$db->setQuery($q);

		$this->shopgrp_price = $db->loadAssoc();
		/*if($db->loadAssoc()){
			$this->shopgrp_price = true;
		} else $this->shopgrp_price = false;*/


	}

	public static function getTip($label){
		$lang = vmLanguage::getLanguage();
		if($lang->hasKey($label.'_TIP')){
			return $label.'_TIP';
		} else if ($lang->hasKey($label.'_EXPLAIN')) {
			return $label.'_EXPLAIN';
		} else {
			return '';
		}
	}

	static $options = array();
	static public function rowShopFrontSet($params, $label, $name, $name2, $name3 = 0, $default = 1, $attrs='class="inputbox"'){

		//$lang =vmLanguage::getLanguage();
		$tip = self::getTip($label);
		if($tip){
			$label = '<span class="hasTooltip" title="'.htmlentities(vmText::_($tip)).'">'.vmText::_($label).'</span>' ;
		} else {
			$label = vmText::_($label);
		}

		$h = '<tr>';
		$h .= '<td class="key">
				'.$label.'
			</td>';
		//$h .= '<td style="text-align: center;">'.VmHtml::checkbox($name, $params->get($name, 1)).'</td>';
		$h .= '<td style="text-align: center;">'.JHtml::_ ('Select.genericlist', self::$options, $name, '', 'value', 'text', $params->get($name, 1)).'</td>';


		$h .= '<td>'.VmHtml::input($name2, $params->get($name2, $default),$attrs,'',4,4).'</td>';
		$h .= '<td >';
		if($name3 !== 0) $h .= JHtml::_ ('Select.genericlist', self::$options, $name3, '', 'value', 'text', $params->get($name3, 1));
		$h .= "</td>\n</tr>";
		return $h;
	}


	/**
	 * Writes a line  for the price configuration
	 *
	 * @author Max Milberes
	 * @param string $name
	 * @param string $langkey
	 */
	static function writePriceConfigLine ($array, $name, $langkey) {

		if(is_object($array)) $array = get_object_vars($array);
		if(!isset($array[$name])) $array[$name] = 0;
		if(!isset($array[$name . 'Text'])) $array[$name . 'Text'] = 0;
		if(!isset($array[$name . 'Rounding'])) $array[$name . 'Rounding'] = -1;

		$tip = self::getTip($langkey);
		$html =
		'<tr>
			<td class="key">';
		if ($tip){
			$html .= '
				<span class="editlinktip hasTooltip" title="' . vmText::_ ($tip) . '">
					<label>' . vmText::_ ($langkey) . '</label>
				</span>';
		} else {
			$html .= '
				<span class="editlinktip noTip">
					<label>' . vmText::_ ($langkey) . '</label>
				</span>';
		}
		$html .='
			</td>

			<td>' .
		VmHTML::checkbox ($name, $array[$name]) . '
			</td>
			<td align="center">' .
		VmHTML::checkbox ($name . 'Text', $array[$name . 'Text']) . '
			</td>
			<td align="center">
			<input type="text" value="' . $array[$name . 'Rounding'] . '" class="uk-form-width-xsmall" size="4" name="' . $name . 'Rounding">
			</td>
		</tr>';
		return $html;
	}

	function permissionsIntegrity(){
		$db = JFactory::getDBO();
		$q = 'select sum(lft)+sum(rgt) as mysum, count(rgt) as `n` from #__usergroups where 1=1';
		$db->setQuery($q);
		$res = $db->loadAssoc();
		$mysum = (int)$res['mysum'];
		$n = (int)($res['n']) * 2;
		$n = $n* ($n+1) / 2;
		if ($n !== $mysum) {
			JFactory::getApplication()->enqueueMessage('Serious security issue detected: Your #__usergroups table includes incorrect datas and permissions reported at backend might not be same as permissions applied on frontend. Please adjust your #__usergroups per clean joomla installation or consult your developer', 'error');
		}
	}
}
// pure php no closing tag
