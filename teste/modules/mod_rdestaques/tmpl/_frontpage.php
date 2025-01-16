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

<div class="row">
<?php foreach($list as $k=> $item): ?>
	<?php if($k === 0): ?>
   	<?php
		$image = ModRdestaquesHelper::tImage(json_decode($item->images)->image_intro,$folder,$item->id.$module->id,720,550);
	?>
	    <div class="col col_6_of_12">
	        <div class="article_standard_view">
	            <article class="item">
	            	<?php if(!empty($image)): ?>
	                	<div class="item_header">
	                    	<a href="<?= !empty(json_decode($item->images)->image_intro_alt) ? json_decode($item->images)->image_intro_alt : 'Intro Image';?>">
	                    	  <img src="<?= $image;?>" alt="<?= !empty(json_decode($item->images)->image_intro_alt) ? json_decode($item->images)->image_intro_alt : 'Intro Image';?>">
	                    	</a>
	                	</div>
	                <?php endif; ?>
	                <div class="item_content">
	                    <h3><a href="<?= JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid)); ?>"><?= $item->title;?></a></h3>
	                </div>
	            </article>
	        </div>
	    </div>
    <?php endif; ?>
<?php endforeach; ?>
	<div class="col col_6_of_12">
	    <!-- Article list view -->
	    <div class="article_tiny_view">
	    <?php foreach($list as $k=> $item): ?>
	    	<?php if($k != 0):?>
	    		<?php
					$image = ModRdestaquesHelper::tImage(json_decode($item->images)->image_intro,$folder,$item->id.$module->id,150,117);
				?>
	        <article class="item">
	        	<?php if(!empty($image)): ?>
	            	<div class="item_header">
	                	<a href="<?= JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid)); ?>"><img src="<?= $image; ?>" alt="<?= !empty(json_decode($item->images)->image_intro_alt) ? json_decode($item->images)->image_intro_alt : 'Intro Image';?>"></a>
	            	</div>
	        	<?php endif; ?>
	            <div class="item_content">
	                <h3><a href="<?= JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid)); ?>"><?= $item->title;?></a></h3>
	              
	            </div>
	        </article>
	    	<?php endif ?>
		<?php endforeach; ?>
	    </div>
	</div>
  </div>
