<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_rdestaques
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$l = array_chunk($list, 2);
$count = count($l);

?>

<!-- Article grid view -->
<div class="row">
<?php for($i = 0; $i < $count; $i++){ ?>
    <div class="col col_6_of_12">
        <div class="article_standard_view">
        
        <?php foreach($l[$i] as $k=> $item): ?>
			<?php
				$image = ModRdestaquesHelper::tImage(json_decode($item->images)->image_intro,$folder,$item->id.$module->id,720,550);
			?>
            <article class="item">
            	<?php if(!empty($image)): ?>
                	<div class="item_header">
                    	<a href="<?= JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid)); ?>"><img src="<?= $image;?>" alt="<?= !empty(json_decode($item->images)->image_intro_alt) ? json_decode($item->images)->image_intro_alt : 'Intro Image';?>"></a>
               		</div>
               	<?php endif; ?>
                <div class="item_content">
                    <h3><a href="post_standard.html"><?= $item->title;?></a></h3>
                    <?php if($pDate == 1): ?>
                        <div class="entry_meta">
                            <span><i class="fa fa-clock-o"></i> <?= ModRdestaquesHelper::diffDates($item->publish_up);?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
        </div> 
    </div>
<?php } ?>
</div>