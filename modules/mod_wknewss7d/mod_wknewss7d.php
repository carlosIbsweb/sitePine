<?php

/**
 * @subpackage  mod_wknews
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */

// Acesso ao Joomla
defined('_JEXEC') or die;

// Controller
require_once dirname(__FILE__).'/helper.php';



// Get the document object.
$doc = JFactory::getDocument();

//Load Jquery
JHTML::_("jquery.framework", true);

$doc->addStyleSheet(JUri::base(true).'/modules/mod_wknewss7d/assets/css/wkcolumns.css');
$doc->addStyleSheet(JUri::base(true).'/modules/mod_wknewss7d/assets/css/style.css');
$doc->addStyleSheet(JUri::base(true).'/modules/mod_wknewss7d/assets/css/wkanimate.css');
$doc->addStyleSheet(JUri::base(true).'/modules/mod_wknewss7d/assets/css/font-awesome.min.css');
$doc->addStyleSheet(JUri::base(true).'/modules/mod_wknewss7d/assets/css/owl.carousel.min.css');
$doc->addStyleSheet(JUri::base(true).'/modules/mod_wknewss7d/assets/css/owl.theme.default.min.css');
$doc->addScript(JUri::base(true).'/modules/mod_wknewss7d/assets/js/owl.carousel.min.js');
$doc->addScript(JUri::base(true).'/modules/mod_wknewss7d/assets/js/wknews.js');

$script = '
jQuery(function($){
	$( window ).load(function(){
		$(document).wknews({
			"modId": '.$module->id.'
		});
	})
	
})

';

$doc->addScriptDeclaration($script);


$style = $params->get('imgfloat');
$items = modWknewss7dHelper::getList($params);
modWknewss7dHelper::setStyle($params,$style,$module->id,$params->get('sliderItems'));
$col = modWknewss7dHelper::col($params);
$folderPath = JPATH_ROOT.'/cache/'.$params->get('folder').'/';
$folder = JUri::base(true).'/cache/'.$params->get('folder').'/';
$imgWidth = $params->get('imgWidth');
$imgHeight = $params->get('imgHeight');
$blockImgWidth = $params->get('blockImgWidth');
$exImagem = $params->get('eximagem');
$exContent = $params->get('excontent');
//Slider
$contentSus = $params->get('exsuspended') == 1 ? null : ' susp ';
$contentType = $params->get('format') != 'links' && $contentSus == null ? ' suspended'.$contentSus : '';
$contentOverlay =  $contentSus == null ? '<span class="wk-news-overlay"></span>' : null;
$wkNewsFormat = $params->get('format') == 'slider' ? ' wk-news-slider-'.$module->id.' owl-theme wk-news-slider' : null;

//Meu sufixo de classe de módulo.
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));


//Carregando meu arquivo default.
require JModuleHelper::getLayoutPath('mod_wknewss7d', $params->get('layout', 'default'));

