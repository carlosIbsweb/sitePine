<?php
/**
 *
 * Controller for the front end User maintenance
 *
 * @package	VirtueMart
 * @subpackage User
 * @author Max Milbers, Maik Künnemann,StAn
 * @link https://virtuemart.net
 * @copyright Copyright (c) 2004 - 2020 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: user.php 10585 2022-02-07 13:50:28Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

/**
 * VirtueMart Component Controller
 *
 * @package		VirtueMart
 */
class VirtueMartControllerUser extends JControllerLegacy
{

	public function __construct()
	{
		parent::__construct();
		$this->useSSL = vmURI::useSSL();
		$this->useXHTML = false;
		vmLanguage::loadJLang('com_virtuemart_shoppers',TRUE);
	}

	/**
	 * Override of display to prevent caching
	 *
	 * @return  JController  A JController object to support chaining.
	 */
	public function display($cachable = false, $urlparams = array()){

		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$viewName = vRequest::getCmd('view', 'user');
		$viewLayout = vRequest::getCmd('layout', 'default');

		$view = $this->getView($viewName, $viewType, '', array('layout' => $viewLayout));
		$view->assignRef('document', $document);

		$cart = VirtueMartCart::getCart();
		$cart->_fromCart = false;
		$cart->setCartIntoSession();
		$view->display();

		return $this;
	}


	function editAddressCart(){

		$view = $this->getView('user', 'html');
		$view->setLayout('edit_address');

		$cart = VirtueMartCart::getCart();
		$cart->_fromCart = true;

		$new = vRequest::getInt('new',false);
		if($new){
			$sess = JFactory::getSession();
			$vmAdminId = $sess->get('vmAdminID','');
			if(!empty($vmAdminId)){
				$adminId = vmCrypt::decrypt($vmAdminId);
				vmdebug('Shoppergroup switcher activ',$vmAdminId,$adminId);
				if($adminId){
					if(vmAccess::manager('user',$adminId)) {

						vmdebug( 'Lets register a new user by our admin' );
						$newUser = JFactory::getUser( 0 );
						$sess->set( 'user', $newUser );

						//update cart data
						$cart = VirtueMartCart::getCart();
						$cart->BT = 0;
						$cart->ST = 0;
						$cart->STsameAsBT = 1;
						$cart->selected_shipto = 0;
						$cart->virtuemart_shipmentmethod_id = 0;
						//$cart->saveAddressInCart($data, 'BT');
					}
				}
			}
		}

		$cart->setCartIntoSession();
		// Display it all
		$view->display();

	}


	/**
	 * This is the save function for the normal user edit.php layout.
	 *
	 * @author Max Milbers
	 */
	function saveUser(){

		$cart = VirtueMartCart::getCart();

		if($cart->_fromCart or $cart->getInCheckOut()){
			$msg = $this->saveData($cart);
		} else {
			$msg = $this->saveData(false);
		}

		if($cart->_fromCart or $cart->getInCheckOut()){

			$task = '';
			//vmdebug('saveUser _fromCart',(int)$cart->_fromCart,(int)$msg);
			if(!$msg){
				$this->setRedirect(JRoute::_('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=BT'.$task, FALSE) );
			} else {
				if ($cart->getInCheckOut()){
					$task = '&task=checkout';

				}
				$this->setRedirect(JRoute::_('index.php?option=com_virtuemart&view=cart'.$task, FALSE) );
			}
		} else {
			$layout = vRequest::getCmd('layout','edit');
			$this->setRedirect( JRoute::_('index.php?option=com_virtuemart&view=user&layout='.$layout, FALSE) );
		}

	}

	function saveAddressST(){
		$cart = VirtueMartCart::getCart();
		$msg = $this->saveData($cart);
		$layout = 'edit';// vRequest::getCmd('layout','edit');
		$this->setRedirect( JRoute::_('index.php?option=com_virtuemart&view=user&layout='.$layout, FALSE) );

	}

	/**
	 * Save the user info. The saveData function don't use the userModel store function for anonymous shoppers, because it would register them.
	 * We make this function private, so we can do the tests in the tasks.
	 *
	 * @author Max Milbers
	 * @author Valérie Isaksen
	 *
	 * @param boolean Defaults to false, the param is for the userModel->store function, which needs it to determine how to handle the data.
	 * @return String it gives back the messages.
	 */
	private function saveData($cartObj) {

		$mainframe = JFactory::getApplication();

		$msg = true;
		$data = vRequest::getPost(FILTER_SANITIZE_STRING);
		$register = isset($_REQUEST['register']);

		$userModel = VmModel::getModel('user');
		$currentUser = JFactory::getUser();

		if(empty($data['address_type'])){
			$data['address_type'] = vRequest::getCmd('addrtype','BT');
		}

		//if($cartObj){
			//if($cartObj->_fromCart or $cartObj->getInCheckOut()){

				$cart = VirtueMartCart::getCart();
				$prefix= '';
				if ($data['address_type'] == 'STaddress' || $data['address_type'] =='ST') {
					$prefix = 'shipto_';
					$cart->STsameAsBT = 0;
				}
				$cart->saveAddressInCart($data, $data['address_type'],true,$prefix);
			//}
		//}

		if(isset($data['vendor_accepted_currencies'])){
			// Store multiple selectlist entries as a ; separated string
			if (array_key_exists('vendor_accepted_currencies', $data) && is_array($data['vendor_accepted_currencies'])) {
				$data['vendor_accepted_currencies'] = implode(',', $data['vendor_accepted_currencies']);
			}

			$data['vendor_store_name'] = vRequest::getHtml('vendor_store_name');
			$data['vendor_store_desc'] = vRequest::getHtml('vendor_store_desc');
			$data['vendor_terms_of_service'] = vRequest::getHtml('vendor_terms_of_service');
			$data['vendor_letter_css'] = vRequest::getHtml('vendor_letter_css');
			$data['vendor_letter_header_html'] = vRequest::getHtml('vendor_letter_header_html');
			$data['vendor_letter_footer_html'] = vRequest::getHtml('vendor_letter_footer_html');
		}

		if($data['address_type'] == 'ST' and !$currentUser->guest){
			$ret = $userModel->storeAddress($data);
			$msg = (is_array($ret)) ? $ret['message'] : $ret;
			if($cartObj and !empty($ret)){
				$cartObj->selected_shipto = $ret;
				$cartObj->setCartIntoSession(true);
			}
		} else {
					//find URL to redirect if captcha is wrong:
					if($cartObj and $cartObj->_fromCart) {
						$redirect_url = 'index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=BT';
					} elseif($cartObj and $cartObj->getInCheckOut()) {
						$redirect_url = 'index.php?option=com_virtuemart&view=user&task=editaddresscheckout&addrtype=BT';
					} else {
						$redirect_url = 'index.php?option=com_virtuemart&view=user&task=edit&addrtype=BT';
					}
					//redirect is done in checkCaptcha via setRedirect
					if(!$this->checkCaptcha($redirect_url)) return;
			

			if($currentUser->guest!=1 or !$cartObj or ($currentUser->guest==1 and $register) ){

				$switch = false;
				if($currentUser->guest==1 and $register){
					$userModel->setId(0);
					$superUser = vmAccess::isSuperVendor();
					if($superUser>1){
						$data['virtuemart_vendor_user_id'] = $superUser;
					}
					$switch = true;
				}

				$cart = VirtueMartCart::getCart();
				if(!empty($cart->vendorId) and $cart->vendorId!=1){
					$data['virtuemart_vendor_user_id'] = $cart->vendorId;
				}

				if(!$cartObj and !isset($data['virtuemart_shoppergroup_id']) and vmAccess::manager('user.edit')){
					$data['virtuemart_shoppergroup_id'] = array();
				}

				//important for user registration mail, by Yagendoo
				if(empty($data['language'])) $data['language'] = VmConfig::$vmlangTag;

				$ret = $userModel->store($data);

				if($switch){ //and VmConfig::get ('oncheckout_change_shopper')){
					//update session
					$current = JFactory::getUser($ret['newId']);
					$session = JFactory::getSession();
					$session->set('user', $current);
				}
				$msg = (is_array($ret)) ? $ret['message'] : $ret;
			}

			if($currentUser->guest==1 and ($register or !$cartObj )){

				$usersConfig = JComponentHelper::getParams( 'com_users' );
				$useractivation = $usersConfig->get( 'useractivation' );

				if (is_array($ret) and $ret['success'] and !$useractivation) {
					// Username and password must be passed in an array
					$credentials = array('username' => $ret['user']->username,
						'password' => $ret['user']->password_clear
					);
					$return = $mainframe->login($credentials);
				} else if(VmConfig::get('oncheckout_only_registered',0)){
					$layout = vRequest::getCmd('layout','edit');
					$this->redirect( JRoute::_('index.php?option=com_virtuemart&view=user&layout='.$layout, FALSE) );
				}
			}
		}
		
		if(isset($ret['success'])){
			return $ret['success'];
		} else {
			return $msg;
		}
	}


	/**
	 * Action cancelled; return to the previous view
	 *
	 * @author Max Milbers
	 */
	function cancel()
	{

		$cart = VirtueMartCart::getCart();
		if($cart->_fromCart){
			$cart->setOutOfCheckout();
			$this->setRedirect( JRoute::_('index.php?option=com_virtuemart&view=cart', FALSE)  );
		} else {
			$return = JURI::base();
			$this->setRedirect( $return );
		}

	}


	function removeAddressST(){

		$virtuemart_userinfo_id = vRequest::getInt('virtuemart_userinfo_id');
		$virtuemart_user_id = vRequest::getInt('virtuemart_user_id');

		//Lets do it dirty for now
		$userModel = VmModel::getModel('user');
		vmdebug('removeAddressST',$virtuemart_user_id,$virtuemart_userinfo_id);
		$userModel->setId($virtuemart_user_id[0]);
		$userModel->removeAddress($virtuemart_userinfo_id);

		$cart = VirtueMartCart::getCart();
		if($cart->_fromCart){
			$cart->setOutOfCheckout();
			$this->setRedirect( JRoute::_('index.php?option=com_virtuemart&view=cart', FALSE)  );
		} else {
			$this->setRedirect( JRoute::_('index.php?option=com_virtuemart&view=user&task=edit&virtuemart_user_id[]='.$virtuemart_user_id[0], $this->useXHTML,$this->useSSL) );
		}

	}

	/**
	 * Check the Joomla ReCaptcha Plg
	 *
	 * @author Maik Künnemann
	 * @Stan
	 */
	function checkCaptcha($retUrl){

		
			$msg = shopFunctionsF::checkCaptcha('reg_captcha');
			//stAn - strict checking for captcha:
			if($msg !== TRUE){

				//timeout or wrong captcha detected
				//in j3.8+ joomla core captchas trigger an exception instead of FALSE
				$data = vRequest::getPost();
				$data['address_type'] = vRequest::getVar('addrtype','BT');

				$cart = VirtueMartCart::getCart();
				$prefix= '';
				if ($data['address_type'] == 'STaddress' || $data['address_type'] =='ST') {
					$prefix = 'shipto_';
				}
				$cart->saveAddressInCart($data, $data['address_type'],true,$prefix);
				//$errmsg = vmText::_('PLG_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL');
				//$errmsg = $e->getMessage();
				$this->setRedirect (JRoute::_ ($retUrl . '&captcha=1', FALSE), $msg);
				return FALSE;

			}
		
		//no captcha enabled
		return TRUE;

	}

}
// No closing tag
