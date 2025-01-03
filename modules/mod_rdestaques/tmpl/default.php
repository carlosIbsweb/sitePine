<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_rdestaques
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<?php if($modelo != 'recentlist'): ?>
	<h5 class="title_section"><?= $module->title;?></h5>
<?php endif; ?>
<?php
	switch ($modelo) {
	 	case 'front':
	 		require('_frontpage.php');
	 		break;
	 	case 'grid':
	 		require('_grid.php');
	 	break;
	 	case 'list':
	 		require('_list.php');
	 	break;
	 	case 'recentlist':
	 		require('_recentlist.php');
	 	break;
	 	case 'bloglist':
	 		require('_bloglist.php');
	 	break;
	 	default:
			require('_list.php');
	 		break;


	 }

	 echo $params->get('title');