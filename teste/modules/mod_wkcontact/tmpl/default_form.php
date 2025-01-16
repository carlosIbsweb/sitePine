<?php



/**

* @subpackage  mod_wkcontact

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

$db->setQuery("SELECT params FROM #__modules WHERE module = 'mod_wkcontact' and published = '1' and id = ".$_GET['formId']);



$module = $db->loadObject();

$moduleParams = new JRegistry();

$moduleParams->loadString($module->params);

   

//Recuperando coluna do input 

$cols = $moduleParams->get('cols');

$wkcols = str_replace(" ","",$cols);



// Get the document object.

$doc = JFactory::getDocument();


//Divisão de url
$d = Juri::base();

$s = $_SERVER['PHP_SELF'];

$base = explode(Juri::base(true), Juri::base());


?>



<form action="" id="wkform-<?= $_GET['formId'];?>" class="wkcontact-form" data-formid="<?= $_GET['formId'];?>" enctype="multipart/form-data">

      <div class="wkFormContent wk-row">

         <?= str_replace("requiredwk","data-requiredwk",$moduleParams->get('form'));?>

      </div>

      <?php if($moduleParams->get('recaptcha') == 1): ?>

      <div class="wk-col-sm-12 wkCaptcha">

         <div class="g-recaptcha" data-sitekey="<?= $moduleParams->get('sitekey');?>" data-theme="<?= $moduleParams->get('reTheme');?>" data-size="<?= $moduleParams->get('reSize');?>"></div>

      </div>

      <?php endif; ?>

      <input type="hidden" name="wkformid" value="<?= $_GET['formId'];?>">

      <div class="wk-submit-button wk-col-sm-12 wk-row"></div>

   <div class="wkstatus wk-col-sm-12" style="<?= $moduleParams->get('recaptcha') ? 'margin: 10px 0' : null;?>"></div>

</form>

<script src="https://www.google.com/recaptcha/api.js" type="text/javascript"></script>  

<script type="text/javascript" src="<?=  $base[1];?>modules/mod_wkcontact/assets/js/jquery.mask.min.js"></script>

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

   

  //Máscara telefone

  var masArr = $.makeArray($('#wkform-<?= $_GET["formId"];?> input[type="tel"]'));

  

  $.each(masArr,function( ind, val){

    if($( this ).data('mask')){

      $( this ).mask(SPMaskBehavior, spOptions);

    } 

  });

});  

</script>



<script>

jQuery(function($){

  $( document ).ready(function(){

    

    var arr = [<?= $wkcols;?>];

    

    $.each(arr,function( index, value){

      var val = '<div class="wk-col-'+(index+1)+' wk-col-sm-'+value+'"></div>';



      $("#wkform-<?= $_GET['formId'];?> .wkFormContent").append( val );

      var arr = $.makeArray($('#wkform-<?= $_GET["formId"];?> [data-col="'+(index+1)+'ª (Col-'+value+')"]').parent('div'));

      $(arr).appendTo('#wkform-<?= $_GET["formId"];?> .wk-col-'+(index+1));

    });

    

    //Submit button abaixo do captcha

    $("#wkform-<?= $_GET['formId'];?> .wk-submit").prependTo("#wkform-<?= $_GET['formId'];?> .wk-submit-button");

   

   //Removendo a mensagem required caso não tenha arquivos requiridos.

   if($("#wkform-<?= $_GET['formId'];?> [data-requiredwk]").length == 0){

      //removendo mensagem required.

      $("#wkform-<?= $_GET['formId'];?> .wkrequired").remove();

    }

    

    $(".wk-checkbox").remove();

    $(".wk-radio").remove();

  });

})

</script>