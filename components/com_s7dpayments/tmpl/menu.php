<?php
/**
 * @package     
 * @subpackage  com_s7dpayments
 **/

// No direct access.
defined('_JEXEC') or die;

$subitem = 
	isset($_GET['user']) && $_GET['user'] == 'register' ? 'Cadastrar ' : (
		isset($_GET['user']) && $_GET['user'] == 'login' ? 'Entrar ou Registrar' : $catTitle
	);
?>

  <nav class="navbar navbar-expand-lg navbar-light bg-light menu-colonia container">
    <a class="navbar-brand dmstore" href="<?= $menuAlias;?>"><?= $menuTitle ?? $subitem;?><br><span class="navbar-subtitle small"><?= $menuTitle ? $subitem : '';?></span></a>

    <?php if($user->name): ?>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"><i class="material-icons">menu</i></span>
    </button>

    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        
        	<li class="nav-item">
          		<a class="nav-link" href="#">Olá, <?= explode(" ",$user->name)[0]; ?></a>
        	</li>
    	
        <li class="nav-item dropdown"><?php include('cart.php');?></li>
      </ul>
    </div>
<?php endif;?>
  </nav>


<div class="dmenu">
	<?php if(!isset($_GET['user'])): ?>
		<a href="<?= $menuAlias;?>" class="dmstore"><span class="hidden-xs"><?= $menuTitle;?></span></a>
		<div class="dmmais col-md-5">
		<a <?= isset($_GET['courseId']) ? 'href="'.$menuLink.'"' : null;?>><?= $catTitle;?></a>
		</div>
		<?php if(isset($_GET['courseId'])): ?>
			<a href="#" onclick="goBack()" class="hidden-md hidden-lg breadCrumbBack">Voltar</a>
		<?php endif;?>
		<div class="col-md-5" style="float:right"><?php require_once('cart.php'); ?></div>
		
	<?php elseif(isset($_GET['user']) && $_GET['user'] == 'register'): ?>
		<a href="<?= $menuAlias;?>" class="dmstore"><span class="hidden-xs"><?= $menuTitle;?></span></a>
		<div class="dmmais">
			<a>Cadastrar</a>
		</div>
	<?php elseif(isset($_GET['user']) && $_GET['user'] == 'login'): ?>
		<a href="<?= $menuAlias;?>" class="dmstore"><span><?= $menuTitle;?></span></a>
		<div class="dmmais">
		<a> Entrar ou Registrar</a>
		</div>
	<?php endif ?>


	<?php /*elseif(isset($_GET['courses']) || (isset($_GET['courseId']) and !isset($_GET['store'])) || isset($_GET['file'])): ?>
		<a href="<?= $url;?>?courses" class="dmcourses"><span class="hidden-xs"><?= $mTitle;?></span></a>
		<div class="dmmais hidden-xs">
			<?php foreach(s7dPayments::getItens($_GET['courseId']) as $item): ?>
				<a href="<?= $url.'?courseId='.$_GET['courseId'];?>"><?= isset($_GET['courseId']) ? $item->title: null; ?></a>
				<?php if(isset($_GET['file'])): ?>
					<?php foreach(json_decode($item->arquivos) as $k=> $aqvs): ?>
						<a><?= $aqvs->linkpdf == $_GET['file'] ? $aqvs->title : null; ?><?php echo isset($_GET['video']) ? $_SESSION['videoItemTitle'] : null; ?></a>
					<?php endforeach; ?>
				<?php endif; ?>
				
				<?php if(isset($_GET['topic'])): ?>
					<a href="<?= $url;?>?courseId=<?= $_GET['courseId'];?>&topic">Fórum</a>
					<?php if(isset($_GET['itemId'])): ?>
						<a><?= s7dPayments::cutString(paymentsForum::getTopicName('s7dpayments_forum','title',$_GET['itemId']),60); ?></a>
					<?php endif; ?>
				<?php endif ?>

				<?php if(isset($_GET['addtopic'])): ?>
					<a href="<?= $url;?>?courseId=<?= $_GET['courseId'];?>&topic">Fórum</a>
					<a>Adicionar Tópico</a>
				<?php endif ?>
				<?php if(isset($_GET['video'])): ?>
					<?php 
					foreach(json_decode($item->videos) as $ivideo)
					{
						foreach($ivideo as $itemsv)
						{
							if($itemsv->link == $_GET['video'])
							{
								echo '<a>'.$itemsv->title.'</a>';
							}
						}
					}
					?>	
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<?php //mobile Back;?>
		<a href="#" onclick="goBack()" class="hidden-md hidden-lg breadCrumbBack">Voltar</a>

		<?php require_once('cart.php'); ?>

	<?php elseif(isset($_GET['user'])): ?>
		<a href="<?= $url;?>?user=login" class="dmlogin"><span class="hidden-xs">Entrar ou criar conta</span></a>
		<?php //mobile Back;?>
		<a href="#" onclick="goBack()" class="hidden-md hidden-lg breadCrumbBack">Voltar</a>
	
	<?php elseif(isset($_GET['cart'])): ?>
		<h4>Meu carrinho</h4>
		<?php //mobile Back;?>
		<a href="#" onclick="goBack()" class="hidden-md hidden-lg breadCrumbBack">Voltar</a>
	
	<?php elseif(isset($_GET['courseId'])): ?>
		<?php foreach(s7dPayments::getItens($_GET['courseId']) as $item): ?>
			<?= isset($_GET['video']) ? '<a href="'.$url.'?cat='.$_GET['cat'].'&courseId='.$_GET['courseId'].'" class="dmvideos">'.$item-> title.'</a><span class="svin">Vídeos</span>' : '<a class="dmvideos">'.s7dPayments::getCategyTitle($_GET['cat']) .'</a>'; ?>
		<?php endforeach ?>
		<?php require_once('cart.php'); ?>
	<?php else: ?>
		<a href="<?= $url;?>" class="dcourses"><?= $mTitle;?></a>
	<?php endif*/; ?>
</div>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
