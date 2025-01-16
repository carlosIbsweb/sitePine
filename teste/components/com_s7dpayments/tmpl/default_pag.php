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

<div id="dPagCourse" class="col-md-12">
<?php  if(!empty($vdarray)): ?>
	<?php foreach(s7dPayments::getItens() as $items): ?>
				<div class="dPagItem">
					<h3><?= $items->title; ?></h3>
					<p><?= $items->description;?></p>
				</div>
	<?php endforeach;?>

	
	<div class="dPagCourseleft col-sm-6">
		<div class="dVideosList dcinner">
			<span>Vídeos</span>
			<?php require_once 'default_videos_list.php'; ?>
		</div>
		<div class="col-md-12 row">
			<?php //arquivos pdf;?>
			<?php require_once 'file_list.php'; ?>
			<div class="dtopiclink">
				<a href="<?= $url;?>?courseId=<?= $_GET['courseId']?>&topic">Fórum</a>
			</div>

		</div>	
		<div class="dquiz row">
			<?php foreach(paymentsQuiz::getQuizList($_GET['courseId']) as $iQuiz):?>
				<?php if(paymentsQuiz::getQuizes($iQuiz->QuizId)):?>
					<a href="<?= JRoute::_('index.php?option=com_ariquizlite&view=quiz&quizId='.$iQuiz->QuizId.'&task=quiz').'&cat='.$_GET['cat'].'&courseId='.$_GET['courseId'];?>">
						<?= paymentsQuiz::getQuizes($iQuiz->QuizId); ?>
					</a>
				<?php endif;?>
			<?php endforeach ?>
		</div>
	</div>
	
	<div class="dPagImg col-sm-6 hidden-xs">
		<img src="<?= $items->image;?>" alt="Introdução do Curso" />
	</div>
		
	<?php else: ?>
	<div class="alert alert-danger">
  		<strong>Acesso negado!</strong> Você precisa de permissão para visualizar essa página.
  	</div>
	<?php endif; ?>
</div>

<?php
	
$mcp = array(); 
foreach(s7dPayments::getCoursesPackage() as $item)
{
	if($item->id == $_GET['courseId'])
	{
		foreach($courses->list as $k=> $courseid)
		{
			if($item->id == $courseid && explode(":",$k)[1] != 'bl')
			{
				$cpkArr = explode(",",$item->package);

				foreach($cpkArr as $cid)
				{
					array_push($mcp,$cid);
				}
			}
		}
	}
} 

?>

<div id="dPagCourses">

	<?php foreach (s7dPayments::getCoursesPackage() as $key => $value): ?>
		<?php if(in_array($value->id,$mcp)): ?>
			<div class="dPagItems">
				<span class="dPagCat"><?= s7dPayments::getCategyTitle($value->catid);?></span>
				<a href="<?= $url.'?cat='.$value->catid.'&courseId='. $value->id; ?>"><h3><?= $value->title; ?></h3></a>
				<p><?= strip_tags($value->description);?></p>
			</div>
		<?php endif; ?>
	<?php endforeach ?>

</div>

 