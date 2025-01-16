<?php

/**
 * @subpackage  mod_wkcontact
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */


// Acesso ao Joomla
defined('_JEXEC') or die;

// Controller
require_once dirname(__FILE__).'/helper.php';

// Get the document object.
$doc = JFactory::getDocument();
$item = modS7dformHelper::getItems($params);
$modal = $params->get('awType');

// Load jquery.
if ($params->get('loadjquery', '1')) {
	JHTML::_("jquery.framework", true);
}
// Load bostrap.
if ($params->get('loadboostrap', '1')) {
	$doc->addStyleSheet(JUri::base(true).'/modules/mod_s7dform/assets/css/bootstrap.css');
}

$cAlign = $params->get('cAlign') == 'right' ? 'float:right' : ($params->get('cAlign') == 'center' ? 'margin:0 auto' : 'float:left');

$doc->addStyleSheet(JUri::base(true).'/modules/mod_s7dform/assets/css/font-awesome.min.css');
$doc->addStyleSheet(JUri::base(true).'/modules/mod_s7dform/assets/css/style.css');
$doc->addStyleSheet(JUri::base(true).'/modules/mod_s7dform/assets/css/awAnimate.css');
$doc->addStyleSheet(JUri::base(true).'/modules/mod_s7dform/assets/css/aw-loading.css');
$doc->addScript(JUri::base(true).'/modules/mod_s7dform/assets/js/jquery.mask.min.js');
$doc->addScript(JUri::base(true).'/modules/mod_s7dform/assets/js/jquery.validate.min.js');
$doc->addScript(JUri::base(true).'/modules/mod_s7dform/assets/js/scripts.js?'.uniqid());


//Meu sufixo de classe de módulo.
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

//Carregando meu arquivo default.
require JModuleHelper::getLayoutPath('mod_s7dform', $params->get('layout', 'default'));

