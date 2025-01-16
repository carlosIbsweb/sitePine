<?php

/**
 * @subpackage  mod_wkcontact
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */

// Acesso ao Joomla

defined('_JEXEC') or die;
?>
<div id="wkContact-<?= $module->id; ?>" data-formid="<?= $module->id;?>" data-type="<?= $type;?>" data-recaptcha="<?= $params->get('recaptcha');?>" class="<?= $moduleclass_sfx;?>">
	<div class="wk-content"></div>
</div>

<span class="wkrequired" data-wkrequiremen="<?= JText::_('MOD_WKCONTACT_WKREQUIREDMEN');?>"></span>