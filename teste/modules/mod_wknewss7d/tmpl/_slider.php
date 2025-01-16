<?php

/**
 * @subpackage	mod_wknewss7d
 * @copyright	Copyright (C) 2017 - Web Keys.
 * @license		GNU/GPL
 */

// Acesso ao Joomla
defined('_JEXEC') or die;

$row = ceil(count($items) / $params->get('columns'));
$rows = $params->get('columns') == 1 ? 1 : $row;

$itemsRow = array_filter(array_chunk($items, $params->get('columns')));

$rowWidth = $rows > 1 ? 'wk-col-md-6  wk-col-lg-12' : 'wk-col-md-12';

$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));

?>

<?php for($i = 0; $i < $rows; $i++) { 
	$nItems = $params->get('columns') == 1 ? $items : $itemsRow[$i];
?>
<div class="wk-news-normal wk-row">
	<div class="wk-news-inner <?= $rowWidth.$wkNewsFormat;?>">
	<?php foreach($nItems as $item): ?>
		<?php
		$imageIntro = json_decode($item->images)->image_intro;
		$image 		= $params->get('format') != 'links' ? (!empty($imageIntro) ? $imageIntro : 'modules/mod_wknewss7d/assets/images/wk-news-no-image.jpg') : null; 
		$images = modWknewss7dHelper::getVideo($item->introtext)->img != false ? modWknewss7dHelper::getVideo($item->introtext) : $imageIntro;

		//Direct
		$direct = basename($image) == 'wk-news-no-image.jpg' ? 'center,center' : null;
		
		if (in_array($item->access, $authorised)):
			$link = JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid, $item->language));
		else:
			$link = new JUri(JRoute::_('index.php?option=com_users&view=login', false));
			$link->setVar('return', base64_encode(ContentHelperRoute::getArticleRoute($item->id, $item->catid, $item->language)));
		endif;
	
		modWknewss7dHelper::sImage($folderPath,$image,$folderPath.$module->id.'-'.implode("-",explode(" ",$item->modified)).'-'.basename($image),$imgWidth.'x'.$imgHeight,'crop',$direct);

		$imageUrl = $folder.$module->id.'-'.implode("-",explode(" ",$item->modified)).'-'.basename($image);
		$video =  empty(modWknewss7dHelper::getVideo($item->introtext)->img);
		//icon vídeo
		$urlVideo = $item->videoUrl;
		$ivideo = $video ? null : '<span class="wk-icon-video"></span>';

		//Hat Slider
		$hatS =  !empty(modWknewss7dHelper::getTag($item->id)) && $params->get('format') == 'slider'  && $params->get('exhat') == 1 ? '<anside class="wk-news-hat hatSuspended">' .modWknewss7dHelper::getTag($item->id). '</anside>' : '';
	?>

	<div id="wk-news-item-<?= $item->id;?>-<?= $module->id;?>" class="wk-news-item <?= $col;?>">
		<?php if($style == 'bottom' && $exContent == 1) {echo modWknewss7dHelper::newsFormat($params,$item->title,$item->introtext,$item->id,$item->catid,$item->publish_up,$item->alias);};?>
		<?php if($exImagem == 1 && file_exists($image) && $params->get('format') != 'links'): ?>
			<div class="wk-news-img">
				<a href="<?= $link; ?>">
					<?= $hatS;?>
					<?= $ivideo.$contentOverlay; ?>
					<img src="<?= $imageUrl; ?>" alt="Imagem Intro">
				</a>
			</div>
		<?php else: ?>
		<?php 
			$styleImgBox = '#wk-news-item-'.$item->id.'-'.$module->id.' .wk-news-content { margin-top: 0px}';
			$doc->addStyleDeclaration($styleImgBox);
		?>
		<?php endif ?>
		<?php if($style != 'bottom' && $exContent == 1) {echo modWknewss7dHelper::newsFormat($params,$item->title,$item->introtext,$item->id,$item->publish_up,$link);};?>
	</div>
<?php endforeach; ?>
</div>
</div>
<?php } ?>