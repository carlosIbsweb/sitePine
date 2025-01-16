<?php
/********************************************************************
Product		: Payage
Date		: 10 March 2021
Copyright	: Les Arbres Design 2014-2021
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class PayageControllerAbout extends JControllerLegacy
{

function display($cachable = false, $urlparams = false)
{
	$view = $this->getView('about', 'html');
	$account_model = $this->getModel('account');
	$view->gateway_list = $account_model->getGatewayList();
	$view->purchase_id = $account_model->get_plugin_tid();
	$view->display();
}

function save_about()
{
    if (!JFactory::getUser()->authorise('core.admin'))
		{
        $msg = JText::_('JGLOBAL_AUTH_ACCESS_DENIED').': '.JText::_('JACTION_ADMIN_GLOBAL').' '.JText::_('JONLY');
    	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=about',$msg,'error');
		return;
		}
    $jinput = JFactory::getApplication()->input;
    $purchase_id = $jinput->get('purchase_id', '', 'STRING');
	$account_model = $this->getModel('account');
    if (!empty($purchase_id) and strlen($purchase_id) != 32)
        {
		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::_('COM_PAYAGE_PURCHASE_ID_32'), 'error');
		$view = $this->getView('about', 'html');
		$view->purchase_id = $purchase_id;
		$view->gateway_list = $account_model->getGatewayList();
		$view->display();
        }
    else
        {
		$account_model->save_plugin_tid($purchase_id);
		@unlink(JPATH_ROOT.'/administrator/components/com_payage/latest_plg_payage.xml');
		$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=about');
        }
}
	
function trace_on()
{
	$account_model = $this->getModel('account');
	$gateway_list = $account_model->getGatewayList();
    if (LAPG_trace::tracing())
    	LAPG_trace::delete_trace_file();
	LAPG_trace::init_trace($gateway_list);
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=about');
}

function trace_off()
{
	LAPG_trace::delete_trace_file();
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=about');
}

function cancel()
{
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=payment');
}

}