<?php
/**
*
* Calc controller
*
* @package	VirtueMart
* @subpackage Calc
* @author Max Milbers, jseros
* @link https://virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: paymentmethod.php 10585 2022-02-07 13:50:28Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Calculator Controller
 *
 * @package    VirtueMart
 * @subpackage Calculation tool
 * @author Max Milbers
 */
class VirtuemartControllerPaymentmethod extends VmController {

	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	public function __construct() {
		vmLanguage::loadJLang('com_virtuemart_orders',TRUE);
		parent::__construct();

	}


	function save($data = 0){
		$data = vRequest::getPost();

		$data['payment_name'] = vRequest::getHtml('payment_name','');
		$data['payment_desc'] = vRequest::getHtml('payment_desc','');
		if(isset($data['params'])){
			$data['params'] = vRequest::getHtml('params','');
		}

		parent::save($data);
	}

	/**
	 * Clone a payment
	 *
	 * @author Valérie Isaksen
	 */
	public function ClonePayment() {

		$app = JFactory::getApplication();
		$model = VmModel::getModel('paymentmethod');


		$cids = vRequest::getInt($this->_cidName, vRequest::getInt('virtuemart_payment_id'));
		if(!is_array($cids)) $cids = array($cids);

		foreach($cids as $cid){
			if ($model->createClone($cid)) $msg = vmText::_('COM_VIRTUEMART_PAYMENT_CLONED_SUCCESSFULLY');
			else {
				vmWarn('COM_VIRTUEMART_PAYMENT_NOT_CLONED_SUCCESSFULLY');
			}
		}

		$app->redirect('index.php?option=com_virtuemart&view=paymentmethod');
	}

}
// pure php no closing tag
