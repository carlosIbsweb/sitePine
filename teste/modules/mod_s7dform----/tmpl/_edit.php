<?php

/**
 * @subpackage  mod_wkcontact
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */


// Acesso ao Joomla
defined('_JEXEC') or die;

$moduleId = 'awForm-'.$module->id;
$awEditId = 'awForm-'.$_GET['awId'];

?>

<form action="" method="post" id="<?= $moduleId;?>" class="aw-form-edit">
	<div class="page-header">
		<h2><?= $params->get('awUpTitle');?></h2>
		
			<?php 
			if(awRenderEdit::getItem('awToken',$params->get('db')) && !empty($params->get('validFields'))):
			if(($params->get('awUpDex') && !$params->get('awUpDblock')) || awLogin::getDado($params->get('awUpDName'),$params->get('db'),$_GET['awToken']) != $params->get('awUpDValue')): ?>
				<div class="awUpHeader">
					<a href="#" class="aw-upD" data-aw-edit-token="<?= $_GET['awToken'];?>" data-confirm="<?= modS7dformHelper::getMenRex($params->get('awUpDconfirm'),$params->get('db'),$_GET['awToken']);?>"><?= $params->get('awUpDbtn');?></a>
				</div>
		<?php 
	endif;
		endif;?>
	</div>
	<div class="aw-form-fields">
		<?php awRender::getDados($params->get('s7dform'),modS7dformHelper::awCaptchaAjax(null,$params->get('awcaptcha'),$module->id),$module->id,$params->get('db'),$params); ?>
	</div>
	<div class="aw-form-status"></div>
	<input type="hidden" name="awCurrent" value="<?= JUri::current();?>" >
	<input type="hidden" name="awEditToken" value="<?= $_GET['awToken'];?>" >
</form>


<script type="text/javascript">
	jQuery(function($){
		$( document ).ready(function(){
			$( '#<?= $moduleId;?>' ).valid({
				formId: '#<?= $moduleId;?>',
				idForm: '<?= $module->id;?>',
				awEditId: '#<?= $awEditId;?>',
				idEdit: '<?= $_GET["awId"];?>',
				edit: true
			})

			$( document ).awMask();
		})
	})
</script>


