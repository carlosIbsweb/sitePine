<?php 
/**
* @version 1.0
* @package GovRef
* @copyright (C) 2006-2015 www.site7dias.com.br
* @license S7D, http://www.s7dias.com.br
*/

// No direct access
defined('_JEXEC') or die;

$_SESSION['sreturn'] = 'index.php?option=com_s7dpayments&file='.$_GET['file'];

if(empty($user->id)):
//sessão de retorno
header('location: index.php?option=com_s7dpayments&user=login');
endif;

//Instanciando o methodo courses
$courses = s7dPayments::getCourses();

foreach(s7dPayments::getItens($_GET['courseId']) as $items):
	foreach($courses->list as $k=> $courseid):
		if($_GET['courseId'] == $courseid && explode(":",$k)[1] != 'bl'):
			$vdarray = $courseid;
		endif;
	endforeach;
endforeach;
?>

<?php if(!empty($vdarray)): ?>
	<div class="darquivo">
		<?php foreach(s7dPayments::getItens() as $items): ?>
		<?php if (is_array(json_decode($items->arquivos,true))): ?>
			<?php foreach(json_decode($items->arquivos) as $k=> $it): ?>
				<?php if($it->linkpdf == $_GET['file']): ?>
					<h3><?= $it->title;?></h3>
					<p><?= $it->description;?></p>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif ?>
	<?php endforeach; ?>

		<object id="resourceobject" data="<?= JUri::root();?>components/com_s7dpayments/tmpl/arquive.php?courseId=<?=$_GET['courseId'];?>&file=<?= $_GET['file'];?>" Disposition="sdfasdfsadf" type="application/pdf" width="800" height="600" internalinstanceid="526" style="width: 1214px; height: 915px;" title=""></object>
	</div>
<?php else: ?>
	Você não tem permissão para visualizar esse arquivo.
<?php endif; ?>