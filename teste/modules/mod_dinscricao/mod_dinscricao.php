 <?php

/**
 * @subpackage  mod_dinscricao
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */

// Acesso ao Joomla
defined('_JEXEC') or die;

// Controller
require_once dirname(__FILE__).'/helper.php';

$item = modDinscricaoHelper::getItems($params);

// Get the document object.
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base(true).'/modules/mod_dinscricao/assets/css/style.css');
$doc->addStyleSheet(JUri::base(true).'/modules/mod_dinscricao/assets/css/wkanimate.css');
$doc->addScript(JUri::base(true).'/modules/mod_dinscricao/assets/js/dinscricao.js');
$doc->addScript(JUri::base(true).'/modules/mod_dinscricao/assets/js/jquery.dpaula.min.js');

$doc->addScript('https://www.google.com/recaptcha/api.js');

$js = '

jQuery( window ).load(function(){
	jQuery().dcontact();
});

grecaptcha.render("drecaptcha", {
    sitekey: "6LeT5hkUAAAAAIOTYZmY6z3ZmTwsLTDW2PmfGB2H",
    callback: function(response) {
        console.log(response);
    }
});
';

$doc->addScriptDeclaration($js);

//Meu sufixo de classe de módulo.
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

//Carregando meu arquivo default.
require JModuleHelper::getLayoutPath('mod_dinscricao', $params->get('layout', 'default'));

