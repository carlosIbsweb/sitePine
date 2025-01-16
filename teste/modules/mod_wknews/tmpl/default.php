<?php

/**
 * @subpackage	mod_wknews
 * @copyright	Copyright (C) 2017 - Web Keys.
 * @license		GNU/GPL
 */

// Acesso ao Joomla
defined('_JEXEC') or die;
?>

<div id="wk-news-<?= $module->id;?>" class="wk-news <?= $contentType.$contentSus .$moduleclass_sfx;?>">
	<?php 
	switch ($params->get('format')) {
		case 'galeria':
			include('_slider.php'); 
			break;
		
		default:
			include('_normal.php');
			break;
	}
	 ?>
</div>