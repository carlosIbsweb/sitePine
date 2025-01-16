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

<aside class="widget">
<h3 class="widget_title"><?= $module->title;?></h3>
<div class="widget_custom_posts">
    <ul>
    <?php foreach($list as $k=> $item): ?>
    <?php
        $image = ModRdestaquesHelper::tImage(json_decode($item->images)->image_intro,$folder,$item->id.$module->id,90,60);
    ?>
        <li>
            <div class="entry_thumbnail">
                <a href="<?= JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid)); ?>">
                <?php if(!empty($image) && $eximagem != 0): ?>
                    <img src="<?= $image;?>" alt="<?= !empty(json_decode($item->images)->image_intro_alt) ? json_decode($item->images)->image_intro_alt : 'Intro Image';?>">
                <?php endif; ?>
                </a>
            </div>
            <h3><a href="<?= JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid)); ?>"><?= $item->title;?></a></h3>
            <?php if($pDate == 1): ?>
                <div class="entry_meta">
                    <span><i class="fa fa-clock-o"></i> <?= ModRdestaquesHelper::diffDates($item->publish_up);?></span>
                </div>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
</aside>