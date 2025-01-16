<?php

/**
 * @subpackage  mod_wkcontact
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 ibgp@ibsweb.com.br, rafael@ibgp.net.br
 */


// Acesso ao Joomla
defined('_JEXEC') or die;

$moduleId = 'awForm-'.$module->id;
$awEditId = 'awForm-'.$_GET['awId'];

echo $params->get('db');

if(modS7dFormHelper::confirmEmail($params->get('db'),$_GET['confirmarEmail'])) {
	echo '<div class="confirmEmailAw">'.modS7dFormHelper::awMessages($params->get('menConfirmSucess'),'success').'</div>';
}else{
	echo '<div class="confirmEmailAw">'.modS7dFormHelper::awMessages('Token inválido','danger').'</div>';
}

?>


<script type="text/javascript">
	jQuery(function($){
		$( document ).ready(function(){
			$('.confirmEmailAw').hide().appendTo('body').fadeIn('slow');
		})
	})
</script>


