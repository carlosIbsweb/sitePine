<?php

/**
 * @subpackage	mod_wkpopup
 * @copyright	Copyright (C) 2017 - Web Keys.
 * @license		GNU/GPL
 */

// Acesso ao Joomla
defined('_JEXEC') or die;

?>

<?php if(empty($item->pSession)): ?>
	<div class="wkp-modal" data-<?= $item->popup;?>="<?= $item->popup != 'custom' ? $item->url : 1;?>" data-link="<?= $item->link;?>" data-target="<?= $item->target;?>" data-alt="<?= $item->imgName;?>" data-durationin="<?= $item->durationIn;?>" data-durationout="<?= $item->durationOut;?>"></div>
	<?php if($item->popup == "custom"): ?>
		<div class="wkp-mcustom"><?= $item->url;?></div>
	<?php endif; ?>
	<?= $item->imgFull; ?>
<?php endif; ?>