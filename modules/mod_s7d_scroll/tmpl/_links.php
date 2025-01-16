<?php

/**
 * @subpackage	mod_s7d_scroll
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Acesso
defined('_JEXEC') or die;

?>
<?php foreach(array_chunk($items->links,2) as $item): ?>
	<?php 
		list($image, $link) = $item;
		$mlink = explode(";",$link);
	?>
    <div class="item">
        <a href="<?= $mlink[0]; ?>" target="<?= $mlink[1];?>">
            <img src="<?= $image;?>" alt="Owl Image">
        </a>
    </div>
<?php endforeach; ?>