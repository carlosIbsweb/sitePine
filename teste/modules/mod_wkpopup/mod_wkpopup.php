<?php

/**
 * @subpackage  mod_wkpopup
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */

// Acesso ao Joomla
defined('_JEXEC') or die;

// Controller
require_once dirname(__FILE__).'/helper.php';

$item = modWkpopupHelper::getItems($params);

$swhite = $item->swbgc ? JUri::base(true).'modules/mod_wkpopup/assets/images/wkbt-closeb.png' : JUri::base(true).'modules/mod_wkpopup/assets/images/wkbt-close.png';

// Get the document object.
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base(true).'/modules/mod_wkpopup/assets/css/style.css?1');
$doc->addStyleSheet(JUri::base(true).'/modules/mod_wkpopup/assets/css/wkanimate.css');
if(empty($item->pSession)):
	$doc->addScript(JUri::base(true).'/modules/mod_wkpopup/assets/js/wkpopup.min.js');
	$js = '
		jQuery( window ).load(function(){
			jQuery().wkpopup({
				mwidth     	: '.$item->width.',
				animateIn  	: "animated '.$item->animateIn.'",
				animateOut 	: "animated '.$item->animateOut.'",
				bshadow 	: '.$item->shadow.',
				imgclose 	: '.JUri::base(true).'"/modules/mod_wkpopup/assets/images/wkbt-close.png",
				imgcloseb 	: "/'.$swhite.'",
			});
		});
	';
	$doc->addScriptDeclaration($js);
endif;

$wkStyle = ".wkp-content-inner {border-radius:6px;} .wkp-content {border-radius:6px}";
$wkStyleBorder = ".wkp-content {padding: 4px; background: rgba(255, 255, 255, 0.23);}";

$wkStyleBackground = ".wkp-modalbg{background:".$item->stylebg."}";
$doc->addStyleDeclaration($wkStyleBackground);
if($params->get('bradius') == 1):
	$doc->addStyleDeclaration($wkStyle);
endif;
if($params->get("border") == 1):
	$doc->addStyleDeclaration($wkStyleBorder);
endif;

//Meu sufixo de classe de módulo.
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

//Carregando meu arquivo default.
require JModuleHelper::getLayoutPath('mod_wkpopup', $params->get('layout', 'default'));

