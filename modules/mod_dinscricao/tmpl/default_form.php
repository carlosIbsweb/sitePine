<?php

/**
 * @subpackage  mod_dinscricao
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */
define( '_JEXEC', 1 );
define( 'DS', '/' );

define( 'JPATH_BASE', $_SERVER['DOCUMENT_ROOT']);
require_once ( JPATH_BASE .DS. 'includes' .DS. 'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
$mainframe = JFactory::getApplication('site');
$mainframe->initialise();
jimport( 'joomla.user.user');
jimport( 'joomla.session.session');
jimport( 'joomla.user.authentication');
jimport( 'joomla.application.module.helper' );

//Dados do usuário logado
$user = JFactory::getUser(); 
$jid = $user->id; 
$jname = $user->name; 
$jguest = $user->guest; 

jimport( 'joomla.application.module.helper' );

$db = JFactory::getDBO();
$db->setQuery("SELECT params FROM #__modules WHERE module = 'mod_dinscricao' and id = ".$_GET['formid']);

$module = $db->loadObject();
$moduleParams = new JRegistry();
$moduleParams->loadString($module->params);

?>

<form action="" id="dform" class="dcontact-form">
  <?= $moduleParams->get('form');?>
  <div class="col-md-12">
  <div class="col-md-6 row">
      <div class="g-recaptcha" data-sitekey="6LeT5hkUAAAAAIOTYZmY6z3ZmTwsLTDW2PmfGB2H"></div>
      <div class="dstatus"></div>
      <button type="submit" class="btn btn-primary denvia">Enviar</button>
     </div>
    <div class="col-md-6 fcontact row">
      <img src="/modules/mod_dinscricao/assets/image/fcontact.jpg" alt="Image fundo"/>
    </div>

  </div> 
</form>

<script src="https://www.google.com/recaptcha/api.js" type="text/javascript"></script>  
<script type="text/javascript" src="/modules/mod_dinscricao/assets/js/jquery.mask.min.js"></script>
<script type="text/javascript">
jQuery(function($){
	var SPMaskBehavior = function (val) {
  return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
},
spOptions = {
  onKeyPress: function(val, e, field, options) {
      field.mask(SPMaskBehavior.apply({}, arguments), options);
    }
};

$('.masktel').mask(SPMaskBehavior, spOptions);
});
</script>

