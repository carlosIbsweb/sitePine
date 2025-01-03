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

<!-- Article list view -->
<div class="article_list_view">
     <!-- Blog post -->
    <?php foreach($list as $k=> $item): ?>
	<?php
		$image = ModRdestaquesHelper::tImage(json_decode($item->images)->image_intro,$folder,$item->id.$module->id,720,550);
	?>
        <article class="item">
            <?php if(!empty($image)): ?>
                <div class="item_header">
                    <a href="post_standard.html">
                        <img src="<?= $image;?>" alt="<?= !empty(json_decode($item->images)->image_intro_alt) ? json_decode($item->images)->image_intro_alt : 'Intro Image';?>">
                    </a>
                </div>
            <?php endif; ?>
            <div class="item_content">
                <h3><a href="<?= JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid)); ?>"><?= $item->title;?></a></h3>
                <p><?= $item->description;?></p>
                <div class="item_post_share">
                    <span class="item_post_share_button"><i class="fa fa-share-square-o"></i></span>
                    <div class="item_post_share_content">
                        <a class="facebook" href="https://www.facebook.com/" target="_blank"><i class="fa fa-facebook"></i></a>
                        <a class="twitter" href="https://www.twitter.com/" target="_blank"><i class="fa fa-twitter"></i></a>
                        <a class="pinterest" href="https://www.pinterest.com/" target="_blank"><i class="fa fa-pinterest"></i></a>
                    </div>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</div>