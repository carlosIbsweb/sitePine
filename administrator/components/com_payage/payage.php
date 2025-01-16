<?php
/********************************************************************
Product		: Payage
Date		: 24 August 2020
Copyright	: Les Arbres Design 2014-2020
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

// Check for ACL access

if (!JFactory::getUser()->authorise('core.manage', 'com_payage'))
    {
	$app = JFactory::getApplication();
    $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
	return;
    }

require_once JPATH_ADMINISTRATOR.'/components/com_payage/helpers/payage_helper.php';
require_once JPATH_ADMINISTRATOR.'/components/com_payage/helpers/admin_helper.php';
require_once JPATH_ADMINISTRATOR.'/components/com_payage/helpers/db_helper.php';
require_once JPATH_ADMINISTRATOR.'/components/com_payage/helpers/trace_helper.php';
require_once JPATH_ADMINISTRATOR.'/components/com_payage/models/account.php';

// load our css

$document = JFactory::getDocument();
if (substr(JVERSION,0,1) == '3')
    $document->addStyleSheet(JURI::base().'components/com_payage/assets/payage_j3.css?'.filemtime(JPATH_ADMINISTRATOR.'/components/com_payage/assets/payage_j3.css'));
if (substr(JVERSION,0,1) == '4')
    $document->addStyleSheet(JURI::base().'components/com_payage/assets/payage_j4.css?'.filemtime(JPATH_ADMINISTRATOR.'/components/com_payage/assets/payage_j4.css'));

$jinput = JFactory::getApplication()->input;
$controller = $jinput->get('controller','payment', 'STRING');
$task = $jinput->get('task','display', 'STRING');
	
// create an instance of the controller and tell it to execute $task

$classname = 'PayageController'.$controller;
require_once JPATH_ADMINISTRATOR.'/components/com_payage/controllers/'.$controller.'controller.php';

$controller = new $classname();
$controller->execute($task);
$controller->redirect();

