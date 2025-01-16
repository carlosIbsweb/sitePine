<?php
/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2016 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

//no direct accees
defined ('_JEXEC') or die ('restricted access');

class SppagebuilderAddonCarouselpro extends SppagebuilderAddons {

	public function render() {

		$class = (isset($this->addon->settings->class) && $this->addon->settings->class) ? ' ' . $this->addon->settings->class : '';

		//Addons option
		$autoplay = (isset($this->addon->settings->autoplay) && $this->addon->settings->autoplay) ? ' data-sppb-ride="sppb-carousel"' : 0;
		$controllers = (isset($this->addon->settings->controllers) && $this->addon->settings->controllers) ? $this->addon->settings->controllers : 0;
		$arrows = (isset($this->addon->settings->arrows) && $this->addon->settings->arrows) ? $this->addon->settings->arrows : 0;
		$alignment = (isset($this->addon->settings->alignment) && $this->addon->settings->alignment) ? $this->addon->settings->alignment : '';
		$carousel_autoplay = ($autoplay) ? ' data-sppb-ride="sppb-carousel"':'';
		$interval = (isset($this->addon->settings->interval) && $this->addon->settings->interval) ? ((int) $this->addon->settings->interval * 1000) : 5000;

		$output  = '<div id="sppb-carousel-'. $this->addon->id .'" data-interval="'.$interval.'" class="sppb-carousel sppb-carousel-pro sppb-slide' . $class . '"'. $carousel_autoplay .'>';

		if($controllers) {
			$output .= '<ol class="sppb-carousel-indicators">';
				foreach ($this->addon->settings->sp_carouselpro_item as $key1 => $value) {
					$output .= '<li data-sppb-target="#sppb-carousel-'. $this->addon->id .'" '. (($key1 == 0) ? ' class="active"': '' ) .'  data-sppb-slide-to="'. $key1 .'"></li>' . "\n";
				}
			$output .= '</ol>';
		}

		$output .= '<div class="sppb-carousel-inner ' . $alignment . '">';

		if(isset($this->addon->settings->sp_carouselpro_item) && count((array) $this->addon->settings->sp_carouselpro_item)){
			foreach ($this->addon->settings->sp_carouselpro_item as $key => $value) {
				$bg = (isset($value->bg) && $value->bg) ? $value->bg : '';
				$bg_class = (isset($value->bg) && $value->bg) ? ' sppb-item-has-bg' : '';
				$video = (isset($value->video) && $value->video) ? $value->video : '';
				$image = (isset($value->image) && $value->image) ? $value->image : '';
	
				$output  .= '<div id="sppb-item-' . $this->addon->id . $key . '" class="sppb-item'. $bg_class . (($key == 0) ? ' active' : '') .'">';
				$output  .= ($bg) ? '<img src="' . $bg . '" alt="' . $value->title . '">' : '';
	
				$output  .= '<div class="sppb-carousel-item-inner">';
				$output  .= '<div>';
				$output  .= '<div>';
	
				$output  .= '<div class="sppb-row">';
	
				$output  .= '<div class="sppb-col-sm-6">';
				$output  .= '<div class="sppb-carousel-pro-text">';
	
				if((isset($value->title) && $value->title) || (isset($value->content) && $value->content) ) {
					$output  .= (isset($value->title) && $value->title) ? '<h2>' . $value->title . '</h2>' : '';
					$output  .= (isset($value->content) && $value->content) ? '<div class="sppb-carousel-pro-content">' . $value->content . '</div>' : '';
					if(isset($value->button_text) && $value->button_text) {
						$button_class = (isset($value->button_type) && $value->button_type) ? ' sppb-btn-' . $value->button_type : ' sppb-btn-default';
						$button_class .= (isset($value->button_size) && $value->button_size) ? ' sppb-btn-' . $value->button_size : '';
						$button_class .= (isset($value->button_shape) && $value->button_shape) ? ' sppb-btn-' . $value->button_shape: ' sppb-btn-rounded';
						$button_class .= (isset($value->button_appearance) && $value->button_appearance) ? ' sppb-btn-' . $value->button_appearance : '';
						$button_class .= (isset($value->button_block) && $value->button_block) ? ' ' . $value->button_block : '';
						$button_icon = (isset($value->button_icon) && $value->button_icon) ? $value->button_icon : '';
						$button_icon_position = (isset($value->button_icon_position) && $value->button_icon_position) ? $value->button_icon_position: 'left';
						$button_target = (isset($value->button_target) && $value->button_target) ? $value->button_target : '_self';
						$button_url = (isset($value->button_url) && $value->button_url) ? $value->button_url : '';
	
						if($button_icon_position == 'left') {
							$value->button_text = ($button_icon) ? '<i class="fa ' . $button_icon . '"></i> ' . $value->button_text : $value->button_text;
						} else {
							$value->button_text = ($button_icon) ? $value->button_text . ' <i class="fa ' . $button_icon . '"></i>' : $value->button_text;
						}
	
						$output  .= '<a href="' . $button_url . '"  target="' . $button_target . '" id="btn-'. ($this->addon->id + $key) .'" class="sppb-btn'. $button_class .'">' . $value->button_text . '</a>';
					}
				}
	
				$output  .= '</div>';
				$output  .= '</div>';
	
				$output  .= '<div class="sppb-col-sm-6">';
				$output  .= '<div class="sppb-text-right">';
	
				if($video) {
	
					$video = parse_url($video);
	
					switch($video['host']) {
						case 'youtu.be':
						$id = trim($video['path'],'/');
						$src = '//www.youtube.com/embed/' . $id;
						break;
	
						case 'www.youtube.com':
						case 'youtube.com':
						parse_str($video['query'], $query);
						$id = $query['v'];
						$src = '//www.youtube.com/embed/' . $id;
						break;
	
						case 'vimeo.com':
						case 'www.vimeo.com':
						$id = trim($video['path'],'/');
						$src = "//player.vimeo.com/video/{$id}";
					}
	
					$output .= '<div class="sppb-embed-responsive sppb-embed-responsive-16by9">';
					$output .= '<iframe class="sppb-embed-responsive-item" src="' . $src . '" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
					$output .= '</div>';
	
				} else {
					$output  .= ($image) ? '<img class="sppb-img-reponsive" src="' . $image . '" alt="'. $value->title .'">' : '';
				}
	
	
				$output  .= '</div>';
				$output  .= '</div>';
	
				$output  .= '</div>';
	
				$output  .= '</div>';
				$output  .= '</div>';
	
				$output  .= '</div>';
				$output  .= '</div>';
			}
		}

		$output	.= '</div>';

		if($arrows) {
			$output	.= '<a href="#sppb-carousel-'. $this->addon->id .'" class="sppb-carousel-arrow left sppb-carousel-control" data-slide="prev"><i class="fa fa-chevron-left"></i></a>';
			$output	.= '<a href="#sppb-carousel-'. $this->addon->id .'" class="sppb-carousel-arrow right sppb-carousel-control" data-slide="next"><i class="fa fa-chevron-right"></i></a>';
		}

		$output .= '</div>';

		return $output;
	}

	public function css() {
		$addon_id = '#sppb-addon-' . $this->addon->id;
		$layout_path = JPATH_ROOT . '/components/com_sppagebuilder/layouts';
		$css = '';
		$css_sm = '';
		$css_xs = '';

		// Buttons style
		foreach ($this->addon->settings->sp_carouselpro_item as $key => $value) {

			$uniqid = '#sppb-item-' . $this->addon->id . $key . ' ';

			if($value->button_text) {
				$css_path = new JLayoutFile('addon.css.button', $layout_path);
				$css .= $css_path->render(array('addon_id' => $addon_id, 'options' => $value, 'id' => 'btn-' . ($this->addon->id + $key)));
			}

			// Title
			$title_css = (isset($value->title_fontsize) && $value->title_fontsize) ? 'font-size: ' . $value->title_fontsize . 'px;' : '';
			$title_css .= (isset($value->title_lineheight) && $value->title_lineheight) ? 'line-height: ' . $value->title_lineheight . 'px;' : '';
			$title_css .= (isset($value->title_margin) && $value->title_margin) ? 'margin: ' . $value->title_margin . ';' : '';
			$title_css .= (isset($value->title_color) && $value->title_color) ? 'color: ' . $value->title_color . ';' : '';

			if($title_css) {
				$css .= $uniqid . '.sppb-carousel-pro-text h2 {' . $title_css . '}';
			}

			$title_css_sm = (isset($value->title_fontsize_sm) && $value->title_fontsize_sm) ? 'font-size: ' . $value->title_fontsize_sm . 'px;' : '';
			$title_css_sm .= (isset($value->title_lineheight_sm) && $value->title_lineheight_sm) ? 'line-height: ' . $value->title_lineheight_sm . 'px;' : '';
			$title_css_sm .= (isset($value->title_margin_sm) && $value->title_margin_sm) ? 'margin: ' . $value->title_margin_sm . ';' : '';

			if($title_css_sm) {
				$css .= '@media (min-width: 768px) and (max-width: 991px) {';
					$css .= $uniqid . '.sppb-carousel-pro-text h2 {' . $title_css_sm . '}';
				$css .= '}';
			}

			$title_css_xs = (isset($value->title_fontsize_xs) && $value->title_fontsize_xs) ? 'font-size: ' . $value->title_fontsize_xs . 'px;' : '';
			$title_css_xs .= (isset($value->title_lineheight_xs) && $value->title_lineheight_xs) ? 'line-height: ' . $value->title_lineheight_xs . 'px;' : '';
			$title_css_xs .= (isset($value->title_margin_xs) && $value->title_margin_xs) ? 'margin: ' . $value->title_margin_xs . ';' : '';

			if($title_css_xs) {
				$css .= '@media (max-width: 767px) {';
					$css .= $uniqid . '.sppb-carousel-pro-text h2 {' . $title_css_xs . '}';
				$css .= '}';
			}

			// Content
			$content_css = (isset($value->content_fontsize) && $value->content_fontsize) ? 'font-size: ' . $value->content_fontsize . 'px;' : '';
			$content_css .= (isset($value->content_lineheight) && $value->content_lineheight) ? 'line-height: ' . $value->content_lineheight . 'px;' : '';
			$content_css .= (isset($value->content_margin) && $value->content_margin) ? 'margin: ' . $value->content_margin . ';' : '';

			if($content_css) {
				$css .= $uniqid . '.sppb-carousel-pro-text .sppb-carousel-pro-content {' . $content_css . '}';
			}

			$content_css_sm = (isset($value->content_fontsize_sm) && $value->content_fontsize_sm) ? 'font-size: ' . $value->content_fontsize_sm . 'px;' : '';
			$content_css_sm .= (isset($value->content_lineheight_sm) && $value->content_lineheight_sm) ? 'line-height: ' . $value->content_lineheight_sm . 'px;' : '';
			$content_css_sm .= (isset($value->content_margin_sm) && $value->content_margin_sm) ? 'margin: ' . $value->content_margin_sm . ';' : '';

			if($content_css_sm) {
				$css .= '@media (min-width: 768px) and (max-width: 991px) {';
					$css .= $uniqid . '.sppb-carousel-pro-text .sppb-carousel-pro-content {' . $content_css_sm . '}';
				$css .= '}';
			}

			$content_css_xs = (isset($value->content_fontsize_xs) && $value->content_fontsize_xs) ? 'font-size: ' . $value->content_fontsize_xs . 'px;' : '';
			$content_css_xs .= (isset($value->content_lineheight_xs) && $value->content_lineheight_xs) ? 'line-height: ' . $value->content_lineheight_xs . 'px;' : '';
			$content_css_xs .= (isset($value->content_margin_xs) && $value->content_margin_xs) ? 'margin: ' . $value->content_margin_xs . ';' : '';

			if($content_css_xs) {
				$css .= '@media (max-width: 767px) {';
					$css .= $uniqid . '.sppb-carousel-pro-text .sppb-carousel-pro-content {' . $content_css_xs . '}';
				$css .= '}';
			}

		}

		$speed = (isset($this->addon->settings->speed) && $this->addon->settings->speed) ? $this->addon->settings->speed : 600;

		$css .= $addon_id.' .sppb-carousel-inner > .sppb-item{-webkit-transition-duration: '.$speed.'ms; transition-duration: '.$speed.'ms;}';

		return $css;
	}

	public static function getTemplate(){
		$output = '
		<#
		var interval = data.interval ? parseInt(data.interval) * 1000 : 5000;
		var autoplay = data.autoplay ? \'data-sppb-ride="sppb-carousel"\' : "";
		#>
		<style type="text/css">
			#sppb-addon-{{ data.id }} .sppb-carousel-inner > .sppb-item{
				-webkit-transition-duration: {{ data.speed }}ms;
				transition-duration: {{ data.speed }}ms;
			}
			<# _.each(data.sp_carouselpro_item, function (carousel_item, key){ #>
				<#
					var button_fontstyle = carousel_item.button_fontstyle || "";

					var modern_font_style = false;

					var margin = window.getMarginPadding(carousel_item.title_margin, "margin");
					var content_margin = window.getMarginPadding(carousel_item.content_margin, "margin");
				#>
				#sppb-addon-{{ data.id }} #btn-{{ data.id + "" + key }}.sppb-btn-{{ carousel_item.button_type }}{
					letter-spacing: {{ carousel_item.button_letterspace }};

					<# if(_.isObject(carousel_item.button_font_style) && carousel_item.button_font_style.underline) { #>
						text-decoration: underline;
						<# modern_font_style = true #>
					<# } #>

					<# if(_.isObject(carousel_item.button_font_style) && carousel_item.button_font_style.italic) { #>
						font-style: italic;
						<# modern_font_style = true #>
					<# } #>

					<# if(_.isObject(carousel_item.button_font_style) && carousel_item.button_font_style.uppercase) { #>
						text-transform: uppercase;
						<# modern_font_style = true #>
					<# } #>

					<# if(_.isObject(carousel_item.button_font_style) && carousel_item.button_font_style.weight) { #>
						font-weight: {{ carousel_item.button_font_style.weight }};
						<# modern_font_style = true #>
					<# } #>

					<# if(modern_font_style && _.isArray(button_fontstyle)) { #>
						<# if(button_fontstyle.indexOf("underline") !== -1){ #>
							text-decoration: underline;
						<# } #>
						<# if(button_fontstyle.indexOf("uppercase") !== -1){ #>
							text-transform: uppercase;
						<# } #>
						<# if(button_fontstyle.indexOf("italic") !== -1){ #>
							font-style: italic;
						<# } #>
						<# if(button_fontstyle.indexOf("lighter") !== -1){ #>
							font-weight: lighter;
						<# } else if(button_fontstyle.indexOf("normal") !== -1){#>
							font-weight: normal;
						<# } else if(button_fontstyle.indexOf("bold") !== -1){#>
							font-weight: bold;
						<# } else if(button_fontstyle.indexOf("bolder") !== -1){#>
							font-weight: bolder;
						<# } #>
					<# } #>
				}

				#sppb-item-{{  data.id  }}{{ key }} h2{
					<# if(_.isObject(carousel_item.title_fontsize)){ #>
						font-size: {{ carousel_item.title_fontsize.md }}px;
					<# } else { #>
						font-size: {{ carousel_item.title_fontsize }}px;
					<# } #>

					<# if(_.isObject(carousel_item.title_lineheight)){ #>
						line-height: {{ carousel_item.title_lineheight.md }}px;
					<# } else { #>
						line-height: {{ carousel_item.title_lineheight }}px;
					<# } #>

					<# if(_.isObject(margin)){ #>
						{{ margin.md }}
					<# } else { #>
						{{ margin }}
					<# } #>

					color: {{ carousel_item.title_color }};
				}

				#sppb-item-{{  data.id  }}{{ key }} .sppb-carousel-pro-text .sppb-carousel-pro-content{
					<# if(_.isObject(carousel_item.content_fontsize)){ #>
						font-size: {{ carousel_item.content_fontsize.md }}px;
					<# } else { #>
						font-size: {{ carousel_item.content_fontsize }}px;
					<# } #>

					<# if(_.isObject(carousel_item.content_lineheight)){ #>
						line-height: {{ carousel_item.content_lineheight.md }}px;
					<# } else { #>
						line-height: {{ carousel_item.content_lineheight }}px;
					<# } #>

					<# if(_.isObject(content_margin)){ #>
						{{ content_margin.md }}
					<# } else { #>
						{{ content_margin }}
					<# } #>
				}
	
				<# if(carousel_item.button_type == "custom"){ #>
					#sppb-addon-{{ data.id }} #btn-{{ data.id + "" + key }}.sppb-btn-custom{
						color: {{ carousel_item.button_color }};
						<# if(carousel_item.button_appearance == "outline"){ #>
							border-color: {{ carousel_item.button_background_color }}
						<# } else if(carousel_item.button_appearance == "3d"){ #>
							border-bottom-color: {{ carousel_item.button_background_color_hover }};
							background-color: {{ carousel_item.button_background_color }};
						<# } else if(carousel_item.button_appearance == "gradient"){ #>
							border: none;
							<# if(typeof carousel_item.button_background_gradient.type !== "undefined" && carousel_item.button_background_gradient.type == "radial"){ #>
								background-image: radial-gradient(at {{ carousel_item.button_background_gradient.radialPos || "center center"}}, {{ carousel_item.button_background_gradient.color }} {{ carousel_item.button_background_gradient.pos || 0 }}%, {{ carousel_item.button_background_gradient.color2 }} {{ carousel_item.button_background_gradient.pos2 || 100 }}%);
							<# } else { #>
								background-image: linear-gradient({{ carousel_item.button_background_gradient.deg || 0}}deg, {{ carousel_item.button_background_gradient.color }} {{ carousel_item.button_background_gradient.pos || 0 }}%, {{ carousel_item.button_background_gradient.color2 }} {{ carousel_item.button_background_gradient.pos2 || 100 }}%);
							<# } #>
						<# } else { #>
							background-color: {{ carousel_item.button_background_color }};
						<# } #>
					}
	
					#sppb-addon-{{ data.id }} #btn-{{ data.id + "" + key }}.sppb-btn-custom:hover{
						color: {{ carousel_item.button_color_hover }};
						background-color: {{ carousel_item.button_background_color_hover }};
						<# if(carousel_item.button_appearance == "outline"){ #>
							border-color: {{ carousel_item.button_background_color_hover }};
						<# } else if(carousel_item.button_appearance == "gradient"){ #>
							<# if(typeof carousel_item.button_background_gradient_hover.type !== "undefined" && carousel_item.button_background_gradient_hover.type == "radial"){ #>
								background-image: radial-gradient(at {{ carousel_item.button_background_gradient_hover.radialPos || "center center"}}, {{ carousel_item.button_background_gradient_hover.color }} {{ carousel_item.button_background_gradient_hover.pos || 0 }}%, {{ carousel_item.button_background_gradient_hover.color2 }} {{ carousel_item.button_background_gradient_hover.pos2 || 100 }}%);
							<# } else { #>
								background-image: linear-gradient({{ carousel_item.button_background_gradient_hover.deg || 0}}deg, {{ carousel_item.button_background_gradient_hover.color }} {{ carousel_item.button_background_gradient_hover.pos || 0 }}%, {{ carousel_item.button_background_gradient_hover.color2 }} {{ carousel_item.button_background_gradient_hover.pos2 || 100 }}%);
							<# } #>
						<# } #>
					}
				<# } #>

				@media (min-width: 768px) and (max-width: 991px) {
					#sppb-item-{{  data.id  }}{{ key }} h2{
						<# if(_.isObject(carousel_item.title_fontsize)){ #>
							font-size: {{ carousel_item.title_fontsize.sm }}px;
						<# } #>
						<# if(_.isObject(carousel_item.title_lineheight)){ #>
							line-height: {{ carousel_item.title_lineheight.sm }}px;
						<# } #>
						<# if(_.isObject(margin)){ #>
							{{ margin.sm }}
						<# } #>
					}

					#sppb-item-{{  data.id  }}{{ key }} .sppb-carousel-pro-text .sppb-carousel-pro-content{
						<# if(_.isObject(carousel_item.content_fontsize)){ #>
							font-size: {{ carousel_item.content_fontsize.sm }}px;
						<# } #>
	
						<# if(_.isObject(carousel_item.content_lineheight)){ #>
							line-height: {{ carousel_item.content_lineheight.sm }}px;
						<# } #>
	
						<# if(_.isObject(content_margin)){ #>
							{{ content_margin.sm }}
						<# } #>
					}
				}

				@media (max-width: 767px) {
					#sppb-item-{{  data.id  }}{{ key }} h2{
						<# if(_.isObject(carousel_item.title_fontsize)){ #>
							font-size: {{ carousel_item.title_fontsize.xs }}px;
						<# } #>
						<# if(_.isObject(carousel_item.title_lineheight)){ #>
							line-height: {{ carousel_item.title_lineheight.xs }}px;
						<# } #>
						<# if(_.isObject(margin)){ #>
							{{ margin.xs }}
						<# } #>
					}

					#sppb-item-{{  data.id  }}{{ key }} .sppb-carousel-pro-text .sppb-carousel-pro-content{
						<# if(_.isObject(carousel_item.content_fontsize)){ #>
							font-size: {{ carousel_item.content_fontsize.xs }}px;
						<# } #>
	
						<# if(_.isObject(carousel_item.content_lineheight)){ #>
							line-height: {{ carousel_item.content_lineheight.xs }}px;
						<# } #>
	
						<# if(_.isObject(content_margin)){ #>
							{{ content_margin.xs }}
						<# } #>
					}
				}
			<# }); #>
		</style>
		<div class="sppb-carousel sppb-carousel-pro sppb-slide {{ data.class }}"  data-interval="{{ interval }}" {{{ autoplay }}}>
			<# if(data.controllers){ #>
				<ol class="sppb-carousel-indicators">
				<# _.each(data.sp_carouselpro_item, function (carousel_item, key){ #>
					<# var active = (key == 0) ? "active" : ""; #>
					<li data-sppb-target="#sppb-carousel-{{ data.id }}"  class="{{ active }}"  data-sppb-slide-to="{{ key }}"></li>
				<# }); #>
				</ol>
			<# } #>
			<div class="sppb-carousel-inner {{ data.alignment }}">
				<# _.each(data.sp_carouselpro_item, function (carousel_item, key){ #>
					<#
						var classNames = (key == 0) ? "active" : "";
						classNames += (carousel_item.bg) ? " sppb-item-has-bg" : "";
					#>
					<div class="sppb-item {{ classNames }}" id="sppb-item-{{  data.id  }}{{ key }}">
						<# if(carousel_item.bg && carousel_item.bg.indexOf("http://") == -1 && carousel_item.bg.indexOf("https://") == -1){ #>
							<img src=\'{{ pagebuilder_base + carousel_item.bg }}\' alt="{{ carousel_item.title }}">
						<# } else if(carousel_item.bg){ #>
							<img src=\'{{ carousel_item.bg }}\' alt="{{ carousel_item.title }}">
						<# } #>
						<div class="sppb-carousel-item-inner">
							<div>
								<div>
									<div class="sppb-row">
										<div class="sppb-col-sm-6">
											<div class="sppb-carousel-pro-text">
												<# if(carousel_item.title || carousel_item.content) { #>
													<# if(carousel_item.title) { #>
														<h2>{{ carousel_item.title }}</h2>
													<# } #>
													<# if(carousel_item.content) { #>
														<div class="sppb-carousel-pro-content">{{{ carousel_item.content }}}</div>
													<# } #>
													<# if(carousel_item.button_text) { #>
														<#
															var btnClass = "";
															btnClass += carousel_item.button_type ? " sppb-btn-"+carousel_item.button_type : " sppb-btn-default" ;
															btnClass += carousel_item.button_size ? " sppb-btn-"+carousel_item.button_size : "" ;
															btnClass += carousel_item.button_shape ? " sppb-btn-"+carousel_item.button_shape : " sppb-btn-rounded" ;
															btnClass += carousel_item.button_appearance ? " sppb-btn-"+carousel_item.button_appearance : "" ;
															btnClass += carousel_item.button_block ? " "+carousel_item.button_block : "" ;
															var button_text = carousel_item.button_text;
					
															if(carousel_item.button_icon_position == "left"){
																button_text = (carousel_item.button_icon) ? \'<i class="fa {{ carousel_item.button_icon }}"></i> \'+carousel_item.button_text : carousel_item.button_text ;
															}else{
																button_text = (carousel_item.button_icon) ? carousel_item.button_text+\' <i class="fa {{ carousel_item.button_icon }}"></i>\' : carousel_item.button_text ;
															}
														#>
														<a href=\'{{ carousel_item.button_url }}\' target="{{ carousel_item.button_target }}" id="btn-{{ data.id + "" + key}}" class="sppb-btn{{ btnClass }}">{{ button_text }}</a>
													<# } #>
												<# } #>
											</div>
										</div>
										<div class="sppb-col-sm-6">
											<div class="sppb-text-right">
											<# if(carousel_item.video) { #>
												<#
													var video = parseUrl(carousel_item.video),
														src = "";

													if (video.host == "youtu.be") {
														var id = video["path"].replace("/", "");
														src = "//www.youtube.com/embed/"+id;
													} else if(video.host == "www.youtube.com" || video.host == "youtube.com"){
														var id = video["query"].replace("v=", "");
														src = "//www.youtube.com/embed/"+id;
													} else if (video.host == "vimeo.com" || video.host == "www.vimeo.com") {
														var id = video["path"].replace("/", "");
														src = "//player.vimeo.com/video/"+id;
													}
												#>
												<div class="sppb-embed-responsive sppb-embed-responsive-16by9">
													<iframe class="sppb-embed-responsive-item" src=\'{{ src }}\' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
												</div>
											<# } else { #>
												<# if(carousel_item.image && carousel_item.image.indexOf("https://") == -1 && carousel_item.image.indexOf("http://") == -1){ #>
													<img class="sppb-img-reponsive" src=\'{{ pagebuilder_base + carousel_item.image }}\' alt="{{ carousel_item.title }}">
												<# } else if(carousel_item.image){ #>
													<img class="sppb-img-reponsive" src=\'{{ carousel_item.image }}\' alt="{{ carousel_item.title }}">
												<# } #>
											<# } #>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<# }); #>
			</div>
			<# if(data.arrows) { #>
				<a href="#sppb-carousel-{{ data.id }}" class="sppb-carousel-arrow left sppb-carousel-control" data-slide="prev"><i class="fa fa-chevron-left"></i></a>
				<a href="#sppb-carousel-{{ data.id }}" class="sppb-carousel-arrow right sppb-carousel-control" data-slide="next"><i class="fa fa-chevron-right"></i></a>
			<# } #>
		</div>
		';

		return $output;
	}

}
