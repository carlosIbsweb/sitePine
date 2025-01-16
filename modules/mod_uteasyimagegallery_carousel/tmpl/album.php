<?php
/**
* @package com_speasyimagegallery
* @subpackage mod_uteasyimagegallery_carousel
* @author Unitemplates https://www.unitemplates.com
* @copyright Copyright (c) 2020 - 2023 Unitemplates
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
* @filesource mod_speasyimagegallery of Joomshaper
*/

// No direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

$doc = Factory::getDocument();
$doc->addStylesheet( Uri::base(true) . '/modules/mod_uteasyimagegallery_carousel/assets/css/swiper-bundle.min.css' );
$doc->addStylesheet( Uri::base(true) . '/modules/mod_uteasyimagegallery_carousel/assets/css/uteig_carousel.css' );
$doc->addScript( Uri::base(true) . '/modules/mod_uteasyimagegallery_carousel/assets/js/swiper-bundle.min.js' );

$fullscreen = ($params->get('fullscreen')) ? ' fullscreen':'';
$image_as = $params->get('image_as');

$nav_text = $params->get('nav_text');
$nav_text_prev = ($nav_text !== 'text') ? '<i class="fas fa-'.$nav_text.'-left"></i>' : Text::_('JPREV');
$nav_text_next = ($nav_text !== 'text') ? '<i class="fas fa-'.$nav_text.'-right"></i>' : Text::_('JNEXT');
?>

<div id="mod-uteig-carousel-album-<?php echo $module->id; ?>" class="mod-uteig-carousel-album<?php echo ' ' . $params->get('moduleclass_sfx');?>">
  <?php
  if(count($images)) {
    ?>
    <div class="mod-uteig-carousel-<?php echo $module->id;?> swiper">
      <div class="swiper-wrapper">
      <?php foreach ($images as $key => $image) {
        $source = json_decode($image->images);
        switch ($params->get('image_size')) {
          case 'original': $cover = $source->original; break;
          case 'mini': $cover = $source->mini; break;
          case 'x_thumb': $cover = $source->x_thumb; break;
          case 'y_thumb': $cover = $source->y_thumb; break;          
          default: $cover = $source->thumb; break;
        }?>

        <div class="uteig-carousel-item swiper-slide">
          <div class="uteig-carousel-img<?php echo $fullscreen;;?>"<?php if($image_as == 'background'){echo ' style="background-image:url('.$cover.');"';}?> data-offset="<?php echo $params->get('fullscreen_offset');?>">
            <?php if ($image_as == 'image') :?>
              <img class="uteig-carousel-normal-img" src="<?php echo $cover;?>" alt="<?php echo $image->alt;?>">
            <?php endif;?>
            <div class="uteig-carousel-item-info">
              <div class="uteig-carousel-item-meta">
                <?php if($params->get('show_image_title')) :?>
                  <h3 class="uteig-carousel-item-title"><?php echo $image->title; ?></h3>
                <?php endif;?>
                <?php if(!empty($image->description) && $params->get('show_image_desc')): ;?>
                  <div class="uteig-carousel-item-desc"><?php echo strip_tags($image->description) ;?></div>
                <?php endif; ?>
                <?php if($params->get('show_image_count')) :?>
                  <div class="uteig-carousel-item-count">
                    <strong><?php echo ($key + 1) .' '. Text::_('MOD_UTEIG_CAROUSEL_OF') .' '. count($images);?></strong>
                  </div>
                <?php endif;?>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
      </div>
      <?php if ($params->get('nav') === 'true'){
				echo '<div id="uteig-navigation-'.$module->id.'" class="uteig-navigation swiper-navigation">';
				echo '<div id="uteig-nav-prev-'.$module->id.'" class="uteig-nav-prev">'.$nav_text_prev.'</div>';
				echo '<div id="uteig-nav-next-'.$module->id.'" class="uteig-nav-next">'.$nav_text_next.'</div>';
				echo '</div>';
			}
			echo ($params->get('dots') === 'true' ) ? '<div id="uteig-dots-'.$module->id.'" class="swiper-pagination"></div>' : '';
			?>
    </div>
    <?php
  } else {
    echo '<div class="alert">' . Text::_('MOD_UTEIG_NO_IMAGES') . '</div>';
  }
  ?>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function(){
		fullScreenImages();

		const uteigCarousel_<?php echo $module->id;?> = new Swiper('.mod-uteig-carousel-<?php echo $module->id;?>', {
			loop: <?php echo ($params->get('loop')) ? $params->get('loop') : 'false';?>,
			speed: <?php echo ($params->get('smartspeed')) ? $params->get('smartspeed') : 300;?>,
			slidesPerView: 1,
			centeredSlides: <?php echo ($params->get('center')) ? $params->get('center') : 'false';?>,
			spaceBetween: <?php echo ($params->get('margin')) ? $params->get('margin') : 0;?>,
			<?php if ($params->get('autoplay') === 'true') { echo "
			autoplay: {
				delay: 5000,
			},";} ?>
			<?php if($params->get('nav') === 'true') { echo "
			navigation: {
				nextEl: '#uteig-nav-next-".$module->id."',
				prevEl: '#uteig-nav-prev-".$module->id."',
			},";}?>
			<?php if ($params->get('dots') === 'true') { echo "
			pagination: {
				el: '#uteig-dots-".$module->id."',
				clickable: true,
			},";}?>
			breakpoints: {
				0: {
					slidesPerView: <?php echo ($params->get('items_sm')) ? $params->get('items_sm') : 1;?>,
				},
				768: {
					slidesPerView: <?php echo ($params->get('items_md')) ? $params->get('items_md') : 2;?>,
				},
				992: {
					slidesPerView: <?php echo ($params->get('items')) ? $params->get('items') : 3;?>,
				},
			},
		});
	})
	window.addEventListener('resize', function(){
		fullScreenImages();
	})
	function fullScreenImages() {
		const fullscreenImgs = document.querySelectorAll('.uteig-carousel-img.fullscreen');
		if (fullscreenImgs.length > 0) {
			const offsets = document.querySelector('.uteig-carousel-img').getAttribute('data-offset');
			const offsetsArr = offsets.split(',');
			let offsetHeight = 0;		
			offsetsArr.forEach(function (value) {
			  offsetHeight += (value.trim()) ? document.querySelector(value.trim()).offsetHeight : 0;
			});
			fullscreenImgs.forEach(function (fullscreenImg) {
			  fullscreenImg.style.height = (window.innerHeight - offsetHeight) + 'px';
			});
		}		
	}
</script>
