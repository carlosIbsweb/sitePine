<?php

/**
 * @subpackage	mod_wknewss7d
 * @copyright	Copyright (C) 2017 - Web Keys.
 * @license		GNU/GPL
 */

// Acesso ao Joomla
defined('_JEXEC') or die;

$row 		= ceil(count($items) / $params->get('columns'));
$rows 		= $params->get('columns') == 1 ? 1 : $row;
$itemsRow 	= array_filter(array_chunk($items, $params->get('columns')));
$rowWidth 	= $rows > 1 ? 'wk-col-md-6  wk-col-lg-12' : ($params->get('format') != 'slider' ? 'wk-row' : null);
$pStyle 	= $params->get('format') != 'slider' ? null : 'padding:0';

$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
JLoader::register('S7dgalleryHelperRoute', JPATH_SITE . '/components/com_s7dgallery/helpers/route.php');

$exLink = $params->get('exLink') != 0 ? true : false;

?>

<?php for($i = 0; $i < $rows; $i++) { 
	$nItems = $params->get('columns') == 1 ? $items : $itemsRow[$i];
?>
<div class="wk-news-normal">
	<div class="wk-news-inner <?= $rowWidth.$wkNewsFormat;?>">
	<?php foreach($nItems as $item): ?>
		<?php
		foreach(json_decode($item->images) as $imgs):
			if($imgs->cover == 1):
				$imageIntro = JUri::root().'components/com_s7dgallery/image/image.php?itemId='.$item->id.'&imgId='.$imgs->id.'&path=thumbs';
			endif;
		endforeach;
		//echo basename($imageIntro);
		//$imageIntro = json_decode($item->images)->image_intro;
		$image 		= $params->get('format') != 'links' ? (!empty($imageIntro) ? $imageIntro : 'modules/mod_wknewss7d/assets/images/wk-news-no-image.jpg') : null; 
		//$images = modWknewss7dHelper::getVideo($item->introtext)->img != false ? modWknewss7dHelper::getVideo($item->introtext) : $imageIntro;

		//Direct
		//$direct = basename($image) == 'wk-news-no-image.jpg' ? 'center,center' : null;

		$link = 'index.php/informacoes-pine/blog-e-galeria/'.$item->alias;
		
		$menus = $app->getMenu();
	
		//modWknewss7dHelper::sImage($folderPath,$image,$folderPath.$module->id.'-'.implode("-",explode(" ",$item->modified)).'-'.basename($image),$imgWidth.'x'.$imgHeight,'crop',$direct);

		//$imageUrl = $folder.$module->id.'-'.implode("-",explode(" ",$item->modified)).'-'.basename($image);
		$video =  empty(modWknewss7dHelper::getVideo($item->introtext)->img);
		//icon vídeo
		$urlVideo = $item->videoUrl;
		$ivideo = $video ? null : '<span class="wk-icon-video"></span>';

		//Hat Slider
		$hatS =  !empty(modWknewss7dHelper::getTag($item->id)) && $params->get('format') == 'slider'  && $params->get('exhat') == 1 ? '<anside class="wk-news-hat hatSuspended">' .modWknewss7dHelper::getTag($item->id). '</anside>' : '';
	?>

	<div id="wk-news-item-<?= $item->id;?>-<?= $module->id;?>" class="wk-news-item <?= $col;?>" style="<?= $pStyle;?>">
		<?php if($style == 'bottom' && $exContent == 1) {echo modWknewss7dHelper::newsFormat($params,$item->title,$item->introtext,$item->id,$item->data,$item->alias);};?>
		<?php if($exImagem == 1 && $params->get('format') != 'links'): ?>
			<div class="wk-news-img">
				<?php if($exLink == true) : ?><a href="<?= $link; ?>"><?php endif; ?>
					<?= $hatS;?>
					<?= $ivideo.$contentOverlay; ?>
					<img src="<?= $image; ?>" alt="Imagem Intro">
				<?php if($exLink == true) : ?></a><?php endif; ?>
			</div>
		<?php else: ?>
		<?php 
			$styleImgBox = '#wk-news-item-'.$item->id.'-'.$module->id.' .wk-news-content { margin-top: 0px}';
			$doc->addStyleDeclaration($styleImgBox);
		?>
		<?php endif ?>
		<?php if($style != 'bottom' && $exContent == 1) {echo modWknewss7dHelper::newsFormat($params,$item->title,$item->introtext,$item->id,$item->data,$link);};?>
	</div>
<?php endforeach; ?>
</div>
</div>
<?php } ?>