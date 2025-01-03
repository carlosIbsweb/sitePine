<?php
/**
* @title		Joombig image slider flipping rotation
* @website		http://www.joombig.com
* @copyright	Copyright (C) 2013 joombig.com. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<style>
	.jb-container-flipping-rotation {
		width:<?php echo $width;?>px;
		height:<?php echo $height;?>px;
		padding-top:10px;
		padding-bottom:10px;
	}
	ul.heapshot {
		left: <?php echo (($width - $width_image)/2);?>px;
	}
</style>

<script>
	jQuery.noConflict(); 
	var call_autoplay,call_animation_delay,call_rotation;
	call_autoplay = <?php echo $autoplay;?>;
	call_animation_delay = <?php echo $animation_delay;?>;
	call_rotation = <?php echo $rotation;?>;
</script>
<link rel="stylesheet" href="<?php echo $mosConfig_live_site; ?>/modules/mod_joombig_image_slider_flipping_rotation/tmpl/css/style.css" type="text/css" >
<?php
if ($enable_jQuery == 1) {?>
	<script src="<?php echo $mosConfig_live_site; ?>/modules/mod_joombig_image_slider_flipping_rotation/tmpl/js/jquery.js"></script>
<?php }?>
<script src="<?php echo $mosConfig_live_site; ?>/modules/mod_joombig_image_slider_flipping_rotation/tmpl/js/jquery.imagesloaded.min.js"></script>
<script src="<?php echo $mosConfig_live_site; ?>/modules/mod_joombig_image_slider_flipping_rotation/tmpl/js/jQueryRotate.min.js"></script>
<script src="<?php echo $mosConfig_live_site; ?>/modules/mod_joombig_image_slider_flipping_rotation/tmpl/js/jquery.heapshot.js"></script>

<div class="jb-container-flipping-rotation">
			<ul class="heapshot">
			<?php foreach($lists as $item) {?>
				<li><img src="<?php echo $item->image?>" style="width:<?php echo $width_image?>px;height:<?php echo $height_image?>px"/></li>
			<?php } ?>	
			</ul>
</div>

