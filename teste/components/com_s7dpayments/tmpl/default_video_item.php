<?php
/**
 * @version     1.0.0
 * @package     com_s7dpayments
 * @copyright   Copyright (C) 2016. Todos os direitos reservados.
 * @license     GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 * @author      Carlos <carlosnaluta@gmail.com> - http://site7dias.com.br
 */

// No direct access
defined('_JEXEC') or die;

?>

<div id="dVideos" class="col-md-12">
	<div class="dVheader col-md-12">
		<?php foreach(json_decode(s7dPayments::getItens()[0]->videos) as $items): ?>
			<?php foreach($items as $item): ?>
				<?php if($item->link == $_GET['video'] && $item->title != ''):?>
					<?php $vlink = $item->link; ?>
					<h2><?= $item->title;?></h2>
					<p><?= $item->description;?></p>
				<?php endif;?>
			<?php endforeach;?>
		<?php endforeach;?>
	</div>
	
	<div class="dluz col-md-6 col-sm-6">
		<iframe src="https://player.vimeo.com/video/<?= $vlink;?>?color=ffffff&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
	</div>
	
	<div class="mVideos col-md-6 col-sm-6">
		<?php foreach(json_decode(s7dPayments::getItens()[0]->videos) as $items): ?>
			<?php foreach($items as $item):?>
				<?php if($item->title != ''): ?>
					<?php $dActive = $item->link == $_GET['video'] ? 'dactive' : null;?>
					<a href="<?= $url;?>?cat=<?= $_GET['cat'];?>&courseId=<?=$_GET['courseId'];?>&video=<?= $item->link;?>" class="<?= $dActive;?>"><?= $item->title;?></a>
				<?php endif; ?>
			<?php endforeach;?>
		<?php endforeach; ?>
	</div>
</div>