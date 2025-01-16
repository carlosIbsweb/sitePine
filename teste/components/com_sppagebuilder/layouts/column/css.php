<?php
/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2016 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('restricted aceess');

$options = $displayData['options'];
$custom_class  = (isset($options->class)) ? ' ' . $options->class : '';
$data_attr = '';
$doc = JFactory::getDocument();

// Style
$style ='';
$style_sm ='';
$style_xs ='';

$column_styles = '';

if(isset($options->padding) && is_object($options->padding)){
	if (isset($options->padding->md) && $options->padding->md) $style .= SppagebuilderHelperSite::getPaddingMargin($options->padding->md, 'padding');
	if (isset($options->padding->sm) && $options->padding->sm) $style_sm .= SppagebuilderHelperSite::getPaddingMargin($options->padding->sm, 'padding');
	if (isset($options->padding->xs) && $options->padding->xs) $style_xs .= SppagebuilderHelperSite::getPaddingMargin($options->padding->xs, 'padding');
} else {
	if (isset($options->padding) && $options->padding) $style .= SppagebuilderHelperSite::getPaddingMargin($options->padding, 'padding');
	if (isset($options->padding_sm) && $options->padding_sm) $style_sm .= SppagebuilderHelperSite::getPaddingMargin($options->padding_sm, 'padding');
	if (isset($options->padding_xs) && $options->padding_xs) $style_xs .= SppagebuilderHelperSite::getPaddingMargin($options->padding_xs, 'padding');
}

if(isset($options->margin) && is_object($options->margin)){
	if (isset($options->margin->md) && $options->margin->md) $style .= SppagebuilderHelperSite::getPaddingMargin($options->margin->md, 'margin');
	if (isset($options->margin->sm) && $options->margin->sm) $style_sm .= SppagebuilderHelperSite::getPaddingMargin($options->margin->sm, 'margin');
	if (isset($options->margin->xs) && $options->margin->xs) $style_xs .= SppagebuilderHelperSite::getPaddingMargin($options->margin->xs, 'margin');
} else {
	if (isset($options->margin) && $options->margin) $style .= SppagebuilderHelperSite::getPaddingMargin($options->margin, 'margin');
	if (isset($options->margin_sm) && $options->margin_sm) $style_sm .= SppagebuilderHelperSite::getPaddingMargin($options->margin_sm, 'margin');
	if (isset($options->margin_xs) && $options->margin_xs) $style_xs .= SppagebuilderHelperSite::getPaddingMargin($options->margin_xs, 'margin');
}

// Border
if(isset($options->use_border) && $options->use_border) {

	if(isset($options->border_width) && is_object($options->border_width)){
		$style .= !empty($options->border_width->md) ? "border-width: " . $options->border_width->md . "px;\n" : "";
		$style_sm .= !empty($options->border_width->sm) ? "border-width: " . $options->border_width->sm . "px;\n" : "";
		$style_xs .= !empty($options->border_width->xs) ? "border-width: " . $options->border_width->xs . "px;\n" : "";
	} else {
		$style .= isset($options->border_width) && $options->border_width ? "border-width: " . $options->border_width . "px;\n" : "";
		$style_sm .= isset($options->border_width_sm) && $options->border_width_sm ? "border-width: " . $options->border_width_sm . "px;\n" : "";
		$style_xs .= isset($options->border_width_xs) && $options->border_width_xs ? "border-width: " . $options->border_width_xs . "px;\n" : "";
	}


    if(isset($options->border_color) && $options->border_color) {
        $style .= "border-color: " . $options->border_color . ";\n";
    }

    if(isset($options->boder_style) && $options->boder_style) {
        $style .= "border-style: " . $options->boder_style . ";\n";
    }
}

if(isset($options->border_radius)){
	if(is_object($options->border_radius)){
		$style .= (isset($options->border_radius->md) && $options->border_radius->md) ? "border-radius: " . $options->border_radius->md . "px;\n" : "";
		$style_sm .= (isset($options->border_radius->sm) && $options->border_radius->sm) ? "border-radius: " . $options->border_radius->sm . "px;\n" : "";
		$style_xs .= (isset($options->border_radius->xs) && $options->border_radius->xs) ? "border-radius: " . $options->border_radius->xs . "px;\n" : "";
	} else {
		$style .= (isset($options->border_radius_md) && $options->border_radius_md) ? "border-radius: " . $options->border_radius_md . "px;\n" : "";
		$style_sm .= (isset($options->border_radius_sm) && $options->border_radius_sm) ? "border-radius: " . $options->border_radius_sm . "px;\n" : "";
		$style_xs .= (isset($options->border_radius_xs) && $options->border_radius_xs) ? "border-radius: " . $options->border_radius_xs . "px;\n" : "";

	}
}

// Box Shadow
if(isset($options->boxshadow) && $options->boxshadow){
    if(is_object($options->boxshadow)){
        $ho = (isset($options->boxshadow->ho) && $options->boxshadow->ho != '') ? $options->boxshadow->ho.'px' : '0px';
        $vo = (isset($options->boxshadow->vo) && $options->boxshadow->vo != '') ? $options->boxshadow->vo.'px' : '0px';
        $blur = (isset($options->boxshadow->blur) && $options->boxshadow->blur != '') ? $options->boxshadow->blur.'px' : '0px';
        $spread = (isset($options->boxshadow->spread) && $options->boxshadow->spread != '') ? $options->boxshadow->spread.'px' : '0px';
        $color = (isset($options->boxshadow->color) && $options->boxshadow->color != '') ? $options->boxshadow->color : '#fff';

        $style .= "box-shadow: ${ho} ${vo} ${blur} ${spread} ${color};";
    } else {
        $style .= "box-shadow: " . $options->boxshadow . ";";
    }
}

if (isset($options->color) && $options->color) $style .= 'color:'.$options->color.';';

if(isset($options->background_type)){
	if (($options->background_type == 'image' || $options->background_type == 'color') && isset($options->background) && $options->background) $style .= 'background-color:'.$options->background.';';

	if ($options->background_type == 'image' && isset($options->background_image) && $options->background_image) {
	
		if(strpos($options->background_image, "http://") !== false || strpos($options->background_image, "https://") !== false){
			$style .= 'background-image:url(' . $options->background_image.');';
		} else {
			$style .= 'background-image:url('. JURI::base(true) . '/' . $options->background_image.');';
		}
	
		if (isset($options->background_repeat) && $options->background_repeat) $style .= 'background-repeat:'.$options->background_repeat.';';
		if (isset($options->background_size) && $options->background_size) $style .= 'background-size:'.$options->background_size.';';
		if (isset($options->background_attachment) && $options->background_attachment) $style .= 'background-attachment:'.$options->background_attachment.';';
		if (isset($options->background_position) && $options->background_position) $style .= 'background-position:'.$options->background_position.';';
	
	}

	if($options->background_type == 'gradient' && isset($options->background_gradient) && is_object($options->background_gradient)) {
		$radialPos = (isset($options->background_gradient->radialPos) && !empty($options->background_gradient->radialPos)) ? $options->background_gradient->radialPos : 'center center';
	
		$gradientColor = (isset($options->background_gradient->color) && !empty($options->background_gradient->color)) ? $options->background_gradient->color : '';
	
		$gradientColor2 = (isset($options->background_gradient->color2) && !empty($options->background_gradient->color2)) ? $options->background_gradient->color2 : '';
	
		$gradientDeg = (isset($options->background_gradient->deg) && !empty($options->background_gradient->deg)) ? $options->background_gradient->deg : '0';
	
		$gradientPos = (isset($options->background_gradient->pos) && !empty($options->background_gradient->pos)) ? $options->background_gradient->pos : '0';
	
		$gradientPos2 = (isset($options->background_gradient->pos2) && !empty($options->background_gradient->pos2)) ? $options->background_gradient->pos2 : '100';
	
		if(isset($options->background_gradient->type) && $options->background_gradient->type == 'radial'){
			$style .= "\tbackground-image: radial-gradient(at " . $radialPos . ", " . $gradientColor . " " . $gradientPos . "%, " . $gradientColor2 . " " . $gradientPos2 . "%);\n";
		} else {
			$style .= "\tbackground-image: linear-gradient(" . $gradientDeg . "deg, " . $gradientColor . " " . $gradientPos . "%, " . $gradientColor2 . " " . $gradientPos2 . "%);\n";
		}
	}
} else {
	if (isset($options->background) && $options->background) $style .= 'background-color:'.$options->background.';';

	if (isset($options->background_image) && $options->background_image) {
	
		if(strpos($options->background_image, "http://") !== false || strpos($options->background_image, "https://") !== false){
			$style .= 'background-image:url(' . $options->background_image.');';
		} else {
			$style .= 'background-image:url('. JURI::base(true) . '/' . $options->background_image.');';
		}
	
		if (isset($options->background_repeat) && $options->background_repeat) $style .= 'background-repeat:'.$options->background_repeat.';';
		if (isset($options->background_size) && $options->background_size) $style .= 'background-size:'.$options->background_size.';';
		if (isset($options->background_attachment) && $options->background_attachment) $style .= 'background-attachment:'.$options->background_attachment.';';
		if (isset($options->background_position) && $options->background_position) $style .= 'background-position:'.$options->background_position.';';
	
	}
}

if($style) {
	$column_styles .= '#column-id-' . $options->dynamicId . '{'. $style .'}';
}
if($style_sm) {
	$column_styles .= '@media (min-width: 768px) and (max-width: 991px) { #column-id-' . $options->dynamicId . '{'. $style_sm .'} }';
}
if($style_xs) {
	$column_styles .= '@media (max-width: 767px) { #column-id-' . $options->dynamicId . '{'. $style_xs .'} }';
}
if(isset($options->background_type)){
	if ($options->background_type == 'image' && isset($options->background_image) && $options->background_image) {
		if (isset($options->overlay) && $options->overlay) {
			$column_styles .= '#column-id-' . $options->dynamicId . ' > .sppb-column-overlay {background-color: '. $options->overlay .'}';
		}
	}
} else {
	if (isset($options->background_image) && $options->background_image) {
		if (isset($options->overlay) && $options->overlay) {
			$column_styles .= '#column-id-' . $options->dynamicId . ' > .sppb-column-overlay {background-color: '. $options->overlay .'}';
		}
	}
}


echo $column_styles;
