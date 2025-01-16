<?php 
/**
* @version 1.0
* @package GovRef
* @copyright (C) 2006-2015 www.site7dias.com.br
* @license S7D, http://www.s7dias.com.br
*/

// No direct access
defined('_JEXEC') or die;

?>
<div class="dfilelist">
	<?php foreach(s7dPayments::getItens() as $items): ?>
		<?php if (is_array(json_decode($items->arquivos,true))): ?>
			<?php foreach(json_decode($items->arquivos) as $k=> $it): ?>
				<a href="<?=$url.'?courseId='.$_GET['courseId'].'&file='.$it-> linkpdf; ?>"><?= $it->title;?></a>
			<?php endforeach; ?>
		<?php endif ?>
	<?php endforeach; ?>
</div>