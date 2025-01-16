<?php
/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2016 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('restricted aceess');

class SppagebuilderAddonGallery extends SppagebuilderAddons{

	public function render() {

		$class = (isset($this->addon->settings->class) && $this->addon->settings->class) ? $this->addon->settings->class : '';
		$title = (isset($this->addon->settings->title) && $this->addon->settings->title) ? $this->addon->settings->title : '';
		$heading_selector = (isset($this->addon->settings->heading_selector) && $this->addon->settings->heading_selector) ? $this->addon->settings->heading_selector : 'h3';

		//Options
		$width = (isset($this->addon->settings->width) && $this->addon->settings->width) ? $this->addon->settings->width : 200;
		$height = (isset($this->addon->settings->height) && $this->addon->settings->height) ? $this->addon->settings->height : 200;

		$output  = '<div class="sppb-addon sppb-addon-gallery ' . $class . '">';
		$output .= ($title) ? '<'.$heading_selector.' class="sppb-addon-title">' . $title . '</'.$heading_selector.'>' : '';
		$output .= '<div class="sppb-addon-content">';
		$output .= '<ul class="sppb-gallery clearfix">';

		if(isset($this->addon->settings->sp_gallery_item) && count((array) $this->addon->settings->sp_gallery_item)){
			foreach ($this->addon->settings->sp_gallery_item as $key => $value) {
				if($value->thumb) {
					$output .= '<li>';
					$output .= ($value->full) ? '<a href="' . $value->full . '" class="sppb-gallery-btn">' : '';
					$output .= '<img class="sppb-img-responsive" src="' . $value->thumb . '" width="' . $width . '" height="' . $height . '" alt="' . $value->title . '" style="width:'.$width.'px;">';
					$output .= ($value->full) ? '</a>' : '';
					$output .= '</li>';
				}
			}
		}

		$output .= '</ul>';
		$output	.= '</div>';
		$output .= '</div>';

		return $output;
	}

	public function stylesheets() {
		return array(JURI::base(true) . '/components/com_sppagebuilder/assets/css/magnific-popup.css');
	}

	public function scripts() {
		return array(JURI::base(true) . '/components/com_sppagebuilder/assets/js/jquery.magnific-popup.min.js');
	}

	public function js() {
		$addon_id = '#sppb-addon-' . $this->addon->id;
		$js ='jQuery(function($){
			$("'.$addon_id.' ul li").magnificPopup({
				delegate: "a",
				type: "image",
				mainClass: "mfp-no-margins mfp-with-zoom",
				gallery:{
					enabled:true
				},
				image: {
					verticalFit: true
				},
				zoom: {
					enabled: true,
					duration: 300
				}
			});
		})';

		return $js;
	}

	public static function getTemplate() {
		$output = '
		<div class="sppb-addon sppb-addon-gallery {{ data.class }}">
			<# if( !_.isEmpty( data.title ) ){ #><{{ data.heading_selector }} class="sppb-addon-title">{{ data.title }}</{{ data.heading_selector }}><# } #>
			<div class="sppb-addon-content">
				<ul class="sppb-gallery clearfix">
				<# _.each(data.sp_gallery_item, function (value, key) { #>
					<# if(value.thumb) { #>
						<li>
						<# if(value.full && value.full.indexOf("http://") == -1 && value.full.indexOf("https://") == -1){ #>
							<a href=\'{{ pagebuilder_base + value.full }}\' class="sppb-gallery-btn">
						<# } else if(value.full){ #>
							<a href=\'{{ value.full }}\' class="sppb-gallery-btn">
						<# } #>
							<# if(value.thumb && value.thumb.indexOf("http://") == -1 && value.thumb.indexOf("https://") == -1){ #>
								<img class="sppb-img-responsive" src=\'{{ pagebuilder_base + value.thumb }}\' width="{{ data.width }}" height="{{ data.height }}" alt="{{ value.title }}" style="width:{{ data.width }}px;">
							<# } else if(value.thumb){ #>
								<img class="sppb-img-responsive" src=\'{{ value.thumb }}\' width="{{ data.width }}" height="{{ data.height }}" alt="{{ value.title }}" style="width:{{ data.width }}px;">
							<# } #>
							
						<# if(value.full){ #>
							</a>
						<# } #>
						</li>
					<# } #>
				<# }); #>
				</ul>
			</div>
		</div>
		';

		return $output;
	}

}
