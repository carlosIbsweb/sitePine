<?php
/********************************************************************
Product		: Payage
Date		: 24 August 2020
Copyright	: Les Arbres Design 2014-2020
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class PayageControllerReport extends JControllerLegacy
{

function __construct()
	{
	parent::__construct();
	$this->app = JFactory::getApplication();
	$this->jinput = JFactory::getApplication()->input;
	}

function display($cachable = false, $urlparams = false)
{
	$function = $this->jinput->get('function','report_menu', 'STRING');
	$view = $this->getView('reports', 'html');
    if (!method_exists($view,$function))
        {
        $this->app->enqueueMessage("Unknown report function $function", 'error');
    	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=report');
        return;
        }
	$report_model = $this->getModel('report');
	$payment_model = $this->getModel('payment');
    $account_model = $this->getModel('account');
    if ($function != 'report_menu')
        {
        $view->model_result = $report_model->$function();       // the view may want to test the result
        $view->list_data  = $report_model->list_data;
        $view->chart_data = $report_model->chart_data;
		$view->pagination =	$report_model->getPagination();
        $view->app_list      = $payment_model->get_app_array();
        $view->currency_list = $payment_model->get_currency_array();
        $view->account_list  = $account_model->get_account_array();
        }
    $view->$function();
}

//--------------------------------------------------------
// Cancel back to the report menu
//
function cancel()
{
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=report');
}

//--------------------------------------------------------
// Cancel back to the payments list
//
function cancellist()
{
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=payment');
}

}