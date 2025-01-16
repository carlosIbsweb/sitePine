<?php

/**
 * @subpackage	mod_s7d_scroll
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Acesso
defined('_JEXEC') or die;

?>

<?php foreach($items->items as $item): ?>
    <div class="item" style="width:<?= $params->get('gwidth');?>px; height:<?= $params->get('gheight');?>px">
	    <?php if($params->get('ecat') == 1):?>
	        <span><?= modS7dScroll::getCategory($item->catid); ?></span>
	    <?php endif; ?>
        <a href="<?= JRoute::_(ContentHelperRoute::getArticleRoute($item->id.':'.$item->alias, $item->catid)); ?>">
            <img src="<?= json_decode($item->images)->image_intro;?>" style="width:<?= $params->get('gwidth')+60;?>px; height: auto" alt="Owl Image">
            <?php if($params->get('etit') == 1): ?>
             	<p><?= $item->title; ?></p>
            <?php endif; ?>
        </a>
    </div>
<?php endforeach; ?>
<?php if(empty($items->items) && $params->get('srel') == 1): ?>
	<p>Não há produtos relacionados!</p>
<?php endif; ?>