<?php
/********************************************************************
Product		: Payage
Date		: 5 August 2022
Copyright	: Les Arbres Design 2014-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class PayageControllerPayment extends JControllerLegacy
{

function display($cachable = false, $urlparams = false)
{
    $this->environment_check();
	$payment_model = $this->getModel('payment');
    $account_model = $this->getModel('account');
	$view = $this->getView('payment', 'html');
	$view->payment_list  = $payment_model->getList();;
	$view->pagination    = $payment_model->getPagination();;
    $view->app_list      = $payment_model->get_app_array();
    $view->currency_list = $payment_model->get_currency_array();
    $view->account_list  = $account_model->get_account_array();
	$view->display();
}

function unconfirmed()
{
	$payment_model = $this->getModel('payment');
	$view = $this->getView('payment', 'html');
	$view->payment_list  = $payment_model->getUnconfirmedList();
    $view->total_unconfirmed = $payment_model->total_unconfirmed;
	$view->pagination    = $payment_model->getPagination();
	$view->unconfirmed();
}

function detail()
{
	$jinput = JFactory::getApplication()->input;
	$cid = $jinput->get('cid',  array(), 'ARRAY');
	$id = (int) $cid[0];
	$payment_model = $this->getModel('payment');
	$payment_data = $payment_model->getOne($id);
	if (empty($payment_data))
		{
	   	$app = JFactory::getApplication();
		$app->enqueueMessage(JText::sprintf('COM_PAYAGE_PAYMENT_X_NOT_FOUND',$id), 'error');   
		$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=payment');
		return;
		}     
	$account_model = $this->getModel('account');
	$account_data = $account_model->getOne($payment_data->account_id);
	if ($account_model->common_data)
		$gateway_info = $account_model->getGatewayInfo($account_model->common_data->gateway_type);
	else
		{
		$gateway_info = array();
		$account_data = $account_model->initData($gateway_info);
		}	
	$view = $this->getView('payment', 'html');
	$view->payment_data = $payment_data;
	$view->account_data = $account_data;
	$view->gateway_info = $gateway_info;
	$view->edit();
}

function unconfirmed_detail()
{
	$jinput = JFactory::getApplication()->input;
	$cid = $jinput->get('cid',  array(), 'ARRAY');
	$id = (int) $cid[0];
	$payment_model = $this->getModel('payment');
	$payment_data = $payment_model->getOne($id);
	$account_model = $this->getModel('account');
    if ($payment_model->data->account_id != 0)
        {
        $account_data = $account_model->getOne($payment_model->data->account_id);
        $gateway_info = $account_model->getGatewayInfo($account_model->common_data->gateway_type);
        }
    else
        {
        $gateway_info = array();
        $gateway_info['type'] = '';
        $gateway_info['shortName'] = JText::_('JNONE');
        $gateway_info['defaultButton'] = '';
        $gateway_info['defaultTitle'] = '';
        $account_data = $account_model->initData($gateway_info);
        }
	
	$view = $this->getView('payment', 'html');
	$view->payment_data = $payment_data;
	$view->account_data = $account_data;
	$view->gateway_info = $gateway_info;
	$view->edit();
}

// returns full details of a specified field
function full_details()
{
	$jinput = JFactory::getApplication()->input;
	$id = $jinput->get('id', 0, 'INT');
	$column = $jinput->get('column', '', 'STRING');
	if (!in_array($column,array('gw_transaction_details', 'app_transaction_details')))
		return;
	$payment_model = $this->getModel('payment');
	$payment_data = $payment_model->getOne($id);
	echo '<h3>'.$column.'</h3>';
	echo '<pre>'.print_r($payment_data->$column,true).'</pre>';
}

function remove()
{
    $user = JFactory::getUser();
    if (!$user->authorise('core.admin'))
        {
		$msg = JText::_('JGLOBAL_AUTH_ACCESS_DENIED').': '.JText::_('JACTION_ADMIN_GLOBAL').' '.JText::_('JONLY');
		$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=payment', $msg, 'error');
        return;
        }
	$payment_model = $this->getModel('payment');
	$payment_model->delete();
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=payment');
}

function cancel()
{
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=payment');
}

function cancel_unconfirmed()
{
	$this->setRedirect(LAPG_COMPONENT_LINK.'&task=unconfirmed&controller=payment');
}

function status_refund()
{
	$this->change_status(LAPG_STATUS_REFUNDED);
}

function status_success()
{
	$this->change_status(LAPG_STATUS_SUCCESS);
}

function status_pending()
{
	$this->change_status(LAPG_STATUS_PENDING);
}

function status_failed()
{
	$this->change_status(LAPG_STATUS_FAILED);
}

function change_status($new_status)
{
    $user = JFactory::getUser();
 	if (!$user->authorise('core.edit.state', LAPG_COMPONENT))		// Change payment status
        {
		$msg = JText::_('JGLOBAL_AUTH_ACCESS_DENIED').': '.JText::_('COM_PAYAGE_CHANGE_PAYMENT_STATUS');
		$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=payment', $msg, 'error');
        return;
        }
	$jinput = JFactory::getApplication()->input;
	$id = $jinput->get('id', 0, 'INT');
	$payment_model = $this->getModel('payment');
	$payment_data = $payment_model->getOne($id);
	$payment_model->change_status($new_status);
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=payment&task=detail&cid[]='.$id);
	
// call the application, if it supplied an app_update_path

	if (empty($payment_data->app_update_path))
		return;

	if (!file_exists($payment_data->app_update_path))
		{
		LAPG_trace::trace("Payment update file ".$payment_data->app_update_path." does not exist, unable to update payment ".$payment_data->pg_transaction_id);
		$payment_data = $payment_model->getOne($id);	// change_status() doesn't update $payment_model->data
		$payment_model->add_history_entry("App Update: no file");
		$payment_model->store();
		return;
		}

	LAPG_trace::trace("User initiated update. Calling payment_update() for tid ".$payment_data->pg_transaction_id.' '.$payment_data->app_update_path);
	require_once $payment_data->app_update_path;
	payment_update($payment_data->pg_transaction_id);
}

function download()
{
	$jinput = JFactory::getApplication()->input;
	$id = $jinput->get('id', 0, 'INT');
	$payment_model = $this->getModel('payment');
	$payment_data = $payment_model->getOne($id);
	$output = "Payage ".PayageHelper::getComponentVersion().", Joomla ".JVERSION.", PHP ".PHP_VERSION." (".PHP_OS.")";
	$output .= " - Payment ".$payment_data->id." exported at ".date('Y-m-d H:i')."\n";
	$output .= print_r($payment_data,true);
	LAPG_admin::file_download($output, 'payment_'.$id.'.txt', 'text/plain');	
	exit;
}

function export()
{
	$payment_model = $this->getModel('payment');
	$payment_model->export_list();
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=payment');
}

// -------------------------------------------------------------------------------
// Check for various environmental issues that might cause trouble
//
function environment_check()
{
    $warnings = array();
   	$app = JFactory::getApplication();
		
// Check that we have (at least) the en-GB language file
  
    if (!file_exists(JPATH_ROOT.'/administrator/components/com_payage/language/en-GB/en-GB.com_payage.ini'))
        $warnings[] = 'Some important files are missing. Please re-install Payage.';

	if (!empty($warnings))
		$app->enqueueMessage(implode('<br />',$warnings), 'notice');        
}

}