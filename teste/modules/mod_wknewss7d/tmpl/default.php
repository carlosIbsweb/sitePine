<?php

/**
 * @subpackage	mod_wknewss7d
 * @copyright	Copyright (C) 2017 - Web Keys.
 * @license		GNU/GPL
 */

// Acesso ao Joomla
defined('_JEXEC') or die;
?>

<div id="wk-news-<?= $module->id;?>" class="wk-news <?= $contentType.$contentSus;?>">
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