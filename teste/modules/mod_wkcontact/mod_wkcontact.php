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

$item = modWkcontactHelper::getItems($params);

// Get the document object.
$doc = JFactory::getDocument();

// Load jquery.
if ($params->get('loadjquery', '1')) {
	JHTML::_("jquery.framework", true);
}

$doc->addStyleSheet(JUri::base(true).'/modules/mod_wkcontact/assets/css/style.css');
$doc->addStyleSheet(JUri::base(true).'/modules/mod_wkcontact/elements/assets/css/wkcontact-render.min.css');
$doc->addStyleSheet(JUri::base(true).'/modules/mod_wkcontact/assets/css/wkanimate.css');
$doc->addStyleSheet(JUri::base(true).'/modules/mod_wkcontact/assets/css/wkcolumn.css');
$doc->addScript(JUri::base(true).'/modules/mod_wkcontact/assets/js/wkcontact.js');
$doc->addScript(JUri::base(true).'/modules/mod_wkcontact/assets/js/jquery.serializeObject.js');

$js = '
jQuery(function($){
	jQuery.noConflict();
	$( window ).load(function(){
		$().wkcontact({
			contactId : '.$module->id.',
			url : "'.JUri::base(true).'/modules/mod_wkcontact/tmpl/default_form.php?formId='.$module->id.'",
			loadImage : "'.JUri::base(true).'/modules/mod_wkcontact/assets/images/loadingfixed.gif",
		});
	$(".wkstatus").appendTo(".wk-submit");
	});
});
';

$doc->addScriptDeclaration($js);

//Selecionando o tipo de contato.
$type = $params->get('wkContactType');

//Meu sufixo de classe de módulo.

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

//Carregando meu arquivo default.
require JModuleHelper::getLayoutPath('mod_wkcontact', $params->get('layout', 'default'));