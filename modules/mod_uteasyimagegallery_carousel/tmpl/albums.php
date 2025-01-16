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
use Joomla\CMS\Filesystem\File;

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

<div id="mod-uteig-carousel-albums-<?php echo $module->id; ?>" class="mod-uteig-carousel-albums<?php echo ' ' . $params->get('moduleclass_sfx');?>">
	<?php
	if(count($albums)) { ?>
		<div class="mod-uteig-carousel-<?php echo $module->id;?> swiper">
			<div class="swiper-wrapper">
			<?php foreach ($albums as $key => $album) { ?>
				<?php if ($params->get('album_image_size') == 'thumb'){
					$cover = 'images/speasyimagegallery/albums/'.$album->id.'/thumb.' . File::getExt(basename($album->image));
				} else{
					$cover = $album->image;
				}?>

				<div class="uteig-carousel-item swiper-slide">
					<div class="uteig-carousel-img<?php echo $fullscreen;;?>"<?php if($image_as == 'background'){echo ' style="background-image:url('.$cover.');"';}?> data-offset="<?php echo $params->get('fullscreen_offset');?>">
						<?php if ($image_as == 'image') :?>
							<img class="uteig-carousel-normal-img" src="<?php echo $cover;?>" alt="<?php echo $album->title;?>">
						<?php endif;?>
						<div class="uteig-carousel-item-info">
							<div class="uteig-carousel-item-meta">
								<?php if($params->get('show_album_title')) :?>
									<h3 class="uteig-carousel-item-title">
										<a href="<?php echo $album->url;?>"><?php echo $album->title; ?></a>
									</h3>
								<?php endif;?>
								<?php if($params->get('show_album_count')) :?>
									<div class="uteig-carousel-item-count"><?php echo $album->count; ?> <?php echo ($album->count > 1) ? 'Photos' : 'Photo'; ?></div>
								<?php endif;?>
								<?php if(!empty($album->description) && $params->get('show_album_desc')): ;?>
									<div class="uteig-carousel-item-desc"><?php echo strip_tags($album->description) ;?></div>
								<?php endif; ?>
								<?php if($params->get('show_album_button')) :?>
									<a class="btn btn-primary" href="<?php echo $album->url;?>"><?php echo Text::_('UTEIG_VIEW_ALBUM');?></a>
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
	<?php } else {
		echo '<div class="alert">' . Text::_('MOD_UTEIG_NO_ALBUMS') . '</div>';
	}	?>
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
