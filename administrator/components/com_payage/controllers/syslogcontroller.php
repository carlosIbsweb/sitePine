<?php
/********************************************************************
Product		: Payage
Date		: 10 March 2021
Copyright	: Les Arbres Design 2014-2021
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

Class PayageControllerSyslog extends JControllerLegacy
{
function __construct()
	{
	parent::__construct();
	}

function display($cachable = false, $urlparams = false)
{
	$log_model = $this->getModel('syslog');
	$log_list = $log_model->getList();
	$pagination = $log_model->getPagination();

	$view = $this->getView('syslog', 'html');
	$view->log_list = $log_list;
	$view->pagination = $pagination;
	$view->display();
}

function edit()
{
	$jinput = JFactory::getApplication()->input;
	$log_model = $this->getModel('syslog');
	$cid = $jinput->get('cid',  array(0 => 0), 'ARRAY');
	$id = (int) $cid[0];
	$log_data = $log_model->getOne($id);
	if ($log_data === false)
		{												// an error has been enqueued
		$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=syslog');
		return;
		}

	$view = $this->getView('syslog', 'html');
	$view->log_data = $log_data;
	$view->edit();
}

function remove()
{
	$log_model = $this->getModel('syslog');
	$log_model->delete();
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=syslog', $msg);
}

function cancel()
{
	$this->setRedirect(LAPG_COMPONENT_LINK.'&controller=syslog');
}

}