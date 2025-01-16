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
<div class="payForum">
<h2>Fórum</h2>
	<?php if($user->id): ?>
		<a href="<?= $url.'?courseId='.$_GET['courseId'].'&addtopic'; ?>" class="btnforum">Adicionar topico</a>
		<div class="payForumList">
		<?php foreach(paymentsForum::getTopic() as $kt => $items): ?>
			<?php if($items->courseId == $_GET['courseId']): ?>
				<div class="payForumItems">
					<a href="<?= $url;?>?courseId=<?= $_GET['courseId'];?>&topic&itemId=<?= $items->id;?>"><?= $items->title;?></a>
					<p>
						<?= str_replace("\r","<br>",$items->description);?>
					</p>
					<div class="payForumDados">
						<span class="tname"><?= s7dPayments::getUserName('name',$items->userId);?></span> - 
						<span class="tdate"><?= s7dPayments::diffDates($items->date); ?></span>
					</div>
				</div>
				
			<?php endif; ?>
		<?php endforeach; ?>
		</div>
	<?php else: ?>
		<div class="alert alert-danger">
  			<strong>Acesso negado!</strong> Você precisa de permissão para visualizar essa página.
  		</div>
	<?php endif; ?>
</div>