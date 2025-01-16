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

$_SESSION['sreturn'] = $url.'/?courses';

if(empty($user->id)):
//sessão de retorno
header('location: '.$url.'/?user=login');
endif;

?>
<div id="dPagCourses">

	<?php foreach (s7dPayments::getItens() as $key => $value): ?>
		
		<?php foreach($courses->list as $k=> $courseid): ?>
			<?php if($value->id == $courseid && explode(":",$k)[1] != 'bl'): ?>
				<div class="dPagItems">
					<span class="dPagCat"><?= s7dPayments::getCategyTitle($value->catid);?></span>
					<a href="<?= $url.'?cat='.$value->catid.'&courseId='. $value->id; ?>"><h3><?= $value->title; ?></h3></a>
					<p><?= strip_tags($value->description);?></p>
				</div>
			<?php endif; ?>
		<?php endforeach ?>
		
	<?php endforeach ?>
</div>