<?php

/**
 * @subpackage	mod_wkpopup
 * @copyright	Copyright (C) 2017 - Web Keys.
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;


class modWkpopupHelper
{
	static function getItems(&$params)
	{
		$img 	= $params->get('image');
		$vid 	= $params->get('video');
		$cus 	= $params->get('custom');
		$link 	= $params->get('link');
		$target = $params->get('target');

		// get image external
		$imgExternal = preg_match("/http:\/\//",$img) || preg_match("/https:\/\//",$img) ? $img : '/'.$img;

		// Capture image height
		$imgFull = '<img class="wkpImgHeight" src="'.$img.'" style="display:none" />';

		$wkpEmpty = 'Error';

		// Vídeo auto player
		$vPlayer = $params->get('vplayer') == 1 ? '?autoplay=1' : '';

		// Get vídeo
		if(preg_match("/youtube/",$vid)):
			$video = "https://www.youtube.com/embed/".end(explode("watch?v=",$vid)).$vPlayer;
		elseif(preg_match("/vimeo/",$vid)):
			$video = "https://player.vimeo.com/video/".end(explode("vimeo.com/",$vid)).$vPlayer;
		else:
			$video = $vid;
		endif;

		// Creating session for popup
		if(time() - $_SESSION['pSession'] > $params->get('pSession') || $params->get('pSession') == ""):
			unset($_SESSION['pSession']);
		endif;
		
		if($params->get('pSession') != ''){
			if(!isset($_SESSION['pSession'])):
				$_SESSION['pSession'] = time();
				$pSession = "true";
			endif;
		}else{
			$pSession = "false";
		}

		// Style background modal
		$sModal = $params->get('bgmodal');
		$styleBg = $sModal == 'white' ? 'rgba(255, 255, 255, 0.68)' : 
			($sModal == 'black' ? 'rgba(0, 0, 0, 0.68)' : 
			($sModal == 'amethyst' ? 'rgba(155, 89, 182, 0.68)' : 
			($sModal == 'emerald' ? 'rgba(46, 204, 113, 0.68)' : 
			($sModal == 'sunflower' ? 'rgba(241, 196, 15, 0.68)' : 
			($sModal == 'wetasphalt' ? 'rgba(52, 73, 94, 0.68)' : 
			($sModal == 'carrot' ? 'rgba(230, 126, 34, 0.68)' : 
			($sModal == 'turquoise' ? 'rgba(26, 188, 156, 0.68)' : 
			($sModal == 'peterriver' ? 'rgba(52, 152, 219, 0.68)' : 
			($sModal == 'alizarin' ? 'rgba(231, 76, 60, 0.68)' : 'rgba(0, 0, 0, 0.68)'
		)))))))));

		//Style white bg
		$swbgc = $sModal == 'white' ? true : false;

		//get the variables
		$item = new StdClass();
		$item->url  = !empty($img) ? $imgExternal : (empty($img) && empty($vid) && empty($cus) ? $wkpEmpty : (!empty($video) ? $video : $cus));
		$item->popup = !empty($img) ? 'image' : (empty($img) && empty($vid) && empty($cus) ? 'image' : (!empty($video) ? 'video' : 'custom'));
		$item->width = $params->get('width');
		$item->animateOut = $params->get('animateOut');
		$item->animateIn = $params->get('animateIn');
		$item->durationIn = $params->get('durationIn');
		$item->durationOut = $params->get('durationOut');
		$item->imgFull = $imgFull;
		$item->imgName = explode(".",basename($img))[0];
		$item->pSession = $pSession == "true" ? "" : ($pSession == "false" ? "" : true);
		$item->shadow = $params->get("shadow");
		$item->link = $link;
		$item->target = $target;
		$item->stylebg = $styleBg;
		$item->swbgc = $swbgc;

		return $item;
	}

}