<?php

/**
 * @subpackage  mod_dcontact
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */

// Acesso ao Joomla
defined('_JEXEC') or die;

// Controller
require_once dirname(__FILE__).'/helper.php';

$item = modDcontactHelper::getItems($params);

// Get the document object.
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base(true).'/modules/mod_dcontact/assets/css/style.css');
$doc->addScript(JUri::base(true).'/modules/mod_dcontact/assets/js/dcontact.js');
$doc->addScript('https://www.google.com/recaptcha/api.js');

$js = '
var jq = jQuery;
jq( document ).ready(function(){
	jq(".seldcontact").change(function(){
		jq().dcontact(jq(this).val());
	});
	jq().dcontact();
});

';

$doc->addScriptDeclaration($js);

//Meu sufixo de classe de módulo.
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

//Carregando meu arquivo default.
require JModuleHelper::getLayoutPath('mod_dcontact', $params->get('layout', 'default'));

