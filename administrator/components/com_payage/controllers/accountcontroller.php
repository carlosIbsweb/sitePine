<?php
/********************************************************************
Product		: Payage
Date		: 24 January 2022
Copyright	: Les Arbres Design 2014-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class PayageControllerAccount extends JControllerLegacy
{

function __construct()
	{
	parent::__construct();
	$this->registerTask('apply', 'save');
	$this->registerTask('save2copy', 'save');
	}

function display($cachable = false, $urlparams = false)
{
	$view = $this->getView('account', 'html');
	$account_model = $this->getModel('account');
	$view->gateway_list = $account_model->getGatewayList();
	$view->account_list = $account_model->getList();
    $view->pagination = $account_model->getPagination();
	$view->display();
}

function account_choice()
{
	$view = $this->getView('account', 'html');
	$account_model = $this->getModel('account');
	$view->setModel($account_model);
	$view->choice();
}

function new_account()	// coming back from the account_choice page with a gateway_type
{
	$jinput = JFactory::getApplication()->input;
	$gateway_type = $jinput->get('gateway_type','', 'STRING');
	$account_model = $this->getModel('account');
	$gateway_info = $account_model->getGatewayInfo($gateway_type);
	$gateway_model = PayageHelper::getGatewayInstance($gateway_type);
	if ($gateway_model === false)
		{
		$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=account',JText::sprintf('COM_PAYAGE_GATEWAY_BAD_INSTALL',$gateway_name));
		return;
		}
	$view = $this->getView('account', 'html');
	$view->gateway_info = $gateway_info;
	$gateway_model->initData($gateway_info);
	$gateway_model->initOrdering();
	$view->common_data = $gateway_model->common_data;
	$view->specific_data = $gateway_model->specific_data;
	$view->setModel($gateway_model,true);
	$view->edit();
}

function edit()
{
    $user = JFactory::getUser();
 	if (!$user->authorise('core.edit', LAPG_COMPONENT))		// Create and edit gateway accounts
        {
        $msg = JText::_('JGLOBAL_AUTH_ACCESS_DENIED').': '.JText::_('COM_PAYAGE_CREATE_EDIT_GATEWAYS');
    	$this->setRedirect(LAPG_COMPONENT_LINK."&controller=account", $msg, 'error');
        return;
        }

	$account_model = $this->getModel('account');
	$jinput = JFactory::getApplication()->input;
	$cid = $jinput->get('cid',  array(), 'ARRAY');
	$id = (int) $cid[0];
	$account_data = $account_model->getOne($id);
	if ($account_data === false)
		{												// an error has been enqueued
		$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=account');
		return;
		}

	$gateway_info = $account_model->getGatewayInfo($account_model->common_data->gateway_type);
	if (empty($gateway_info))
		{
		$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=account',JText::sprintf('COM_PAYAGE_GATEWAY_BAD_INSTALL',''));
		return;
		}
	
	$gateway_model = PayageHelper::getGatewayInstance($account_model->common_data->gateway_type);
	if ($gateway_model === false)
		{
		$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=account',JText::sprintf('COM_PAYAGE_GATEWAY_BAD_INSTALL',$gateway_name));
		return;
		}
	$view = $this->getView('account', 'html');
	$view->gateway_info = $gateway_info;
	$view->common_data = $account_model->common_data;
	$view->specific_data = $account_model->specific_data;
	$view->translations = $account_model->translations;
	$view->setModel($gateway_model,true);
	$view->edit();
}

function save()
{
    $user = JFactory::getUser();
 	if (!$user->authorise('core.edit', LAPG_COMPONENT))		// Create and edit gateway accounts
        {
        $msg = JText::_('JGLOBAL_AUTH_ACCESS_DENIED').': '.JText::_('COM_PAYAGE_CREATE_EDIT_GATEWAYS');
    	$this->setRedirect(LAPG_COMPONENT_LINK."&controller=account", $msg, 'error');
        return;
        }
	$jinput = JFactory::getApplication()->input;
	$task = $jinput->get('task', '', 'STRING');					// 'save' or 'apply'
	$gateway_type = $jinput->get('gateway_type', '', 'STRING');
	$gateway_model = PayageHelper::getGatewayInstance($gateway_type);
	if ($gateway_model === false)
		{
		$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=account',JText::sprintf('COM_PAYAGE_GATEWAY_BAD_INSTALL',$gateway_name));
		return;
		}
	$gateway_model->getPostData();

	if ($task == 'save2copy')
		$gateway_model->common_data->id = 0;
	
	$valid = $gateway_model->check_post_data();
	if ($valid)
		if ($gateway_model->store() && ($task == 'save'))
			{
			$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=account',JText::_('COM_PAYAGE_SAVED'));
			return;
			}

// task=apply or 'save2copy' or a validation error - re-display the view

	if ($valid)
		JFactory::getApplication()->enqueueMessage(JText::_('COM_PAYAGE_SAVED'));
	$view = $this->getView('account', 'html');
	$account_model = $this->getModel('account');
	$gateway_info = $account_model->getGatewayInfo($gateway_type);
	$view->gateway_info = $gateway_info;
	$view->common_data = $gateway_model->common_data;
	$view->specific_data = $gateway_model->specific_data;
	$view->translations = $gateway_model->translations;
	$view->setModel($gateway_model,true);
	$view->edit();
}

function test()
{
	$jinput = JFactory::getApplication()->input;
	$gateway_type = $jinput->get('gateway_type', '', 'STRING');
	$account_id = $jinput->get('id', '', 'STRING');
	$gateway_model = PayageHelper::getGatewayInstance($gateway_type);
	if ($gateway_model === false)
		{
		$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=account',JText::sprintf('COM_PAYAGE_GATEWAY_BAD_INSTALL',$gateway_name));
		return;
		}
	$gateway_model->getPostData();
	$gateway_model->Gateway_test();			// tests communication and enqueues a message

	$view = $this->getView('account', 'html');
	$gateway_info = $gateway_model->getGatewayInfo($gateway_type);
	$view->gateway_info = $gateway_info;
	$view->common_data = $gateway_model->common_data;
	$view->specific_data = $gateway_model->specific_data;
	$view->setModel($gateway_model,true);
	$view->edit();
}

function publish()
{
	$account_model = $this->getModel('account');
	$jinput = JFactory::getApplication()->input;
	$cids = $jinput->get( 'cid', array(), 'ARRAY' );
	foreach($cids as $cid)
		{
		$account_data = $account_model->getOne($cid);
		$gateway_model = PayageHelper::getGatewayInstance($account_data->gateway_type);
		if ($gateway_model === false)
			{
			$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=account',JText::sprintf('COM_PAYAGE_GATEWAY_BAD_INSTALL',$gateway_name));
			return;
			}
		if (method_exists($gateway_model,'Gateway_activate'))
			{
			$gateway_model->getOne($cid);
			$gateway_model->Gateway_activate(LAPG_AC_PUBLISH);	// invoke any gateway-specific actions for publishing the gateway
			}
		$account_model->publish($cid, 1);			// set it to published
		}
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=account');
}

function unpublish()
{
	$account_model = $this->getModel('account');
	$jinput = JFactory::getApplication()->input;
	$cids = $jinput->get( 'cid', array(), 'ARRAY' );
	foreach($cids as $cid)
		{
		$account_data = $account_model->getOne($cid);
		$gateway_model = PayageHelper::getGatewayInstance($account_data->gateway_type);
		if ($gateway_model && method_exists($gateway_model,'Gateway_activate'))
			{
			$gateway_model->getOne($cid);
			$gateway_model->Gateway_activate(LAPG_AC_UNPUBLISH);	// invoke any gateway-specific actions for unpublishing the gateway
			}
		$account_model->publish($cid, 0);			// set it to unpublished
		}
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=account');
}

function remove()
{
    $user = JFactory::getUser();
 	if (!$user->authorise('core.edit', LAPG_COMPONENT))		// Create and edit gateway accounts
        {
        $msg = JText::_('JGLOBAL_AUTH_ACCESS_DENIED').': '.JText::_('COM_PAYAGE_CREATE_EDIT_GATEWAYS');
    	$this->setRedirect(LAPG_COMPONENT_LINK."&controller=account", $msg, 'error');
        return;
        }
	$account_model = $this->getModel('account');
	$jinput = JFactory::getApplication()->input;
	$cids = $jinput->get('cid', array(), 'ARRAY' );
	foreach($cids as $cid)
		{
		$account_data = $account_model->getOne($cid);
		$gateway_model = PayageHelper::getGatewayInstance($account_data->gateway_type);
		if ($gateway_model === false)
			{                                  // probably the gateway addon has been uninstalled
			$account_model->delete($cid);      // delete the account anyway
			continue;                          // don't call Gateway_activate() but continue the loop
			}
		$gateway_model->getOne($cid);
		if (method_exists($gateway_model,'Gateway_activate'))
			$gateway_model->Gateway_activate(LAPG_AC_DELETE);	// invoke any gateway-specific actions for deleting the gateway
		$account_model->delete($cid);
		}
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=account');
}
		
function orderup()
{
	$jinput = JFactory::getApplication()->input;
	$cid = $jinput->get('cid',  array(0 => 0), 'ARRAY');
	$id = (int) $cid[0];
	$account_model = $this->getModel('account');
	$account_model->move($id, -1);
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=account');
}

function orderdown()
{
	$jinput = JFactory::getApplication()->input;
	$cid = $jinput->get('cid',  array(0 => 0), 'ARRAY');
	$id = (int) $cid[0];
	$account_model = $this->getModel('account');
	$account_model->move($id, 1);
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=account');
}

function saveorder()
{
	$jinput = JFactory::getApplication()->input;
	$cid 	= $jinput->get( 'cid', array(), 'ARRAY' );
	$order 	= $jinput->get( 'order', array(), 'ARRAY' );
	Joomla\Utilities\ArrayHelper::toInteger($cid);
	Joomla\Utilities\ArrayHelper::toInteger($order);
	$account_model = $this->getModel('account');
	$account_model->saveorder($cid, $order);
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=account');
}

//--------------------------------------------------------
// Cancel back to the account list
//
function cancel()
{
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=account');
}

//--------------------------------------------------------
// Cancel back to the payments list
//
function cancellist()
{
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=payment');
}

}