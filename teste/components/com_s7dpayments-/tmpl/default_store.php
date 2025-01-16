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

$base = JUri::base(true);

?>


<link rel="stylesheet" type="text/css" href="<?= $base?>components/com_s7dpayments/assets/css/component.css" />
<script src="<?= $base?>components/com_s7dpayments/assets/js/modernizr.custom.js"></script>

		<!--div class="storeItems">
			inscrições com descontos de 10 a 20% por tempo limitado.
		</div-->	
		<?= $_POST['executcart']; ?>
<div id="tabs" class="tabs">
	<nav>
		<ul>
			<?php foreach(paymentsCart::getCat() as $k=> $items): ?>
				<li><a href="#section-<?= $k; ?>" class="<?= $items-> alias; ?>" title="<?= $items->title;?>"><i class="fa fa-<?= json_decode($items-> params)->icon;?>" aria-hidden="true"></i><span><?= $items->title; ?></span></a></li>
			<?php endforeach ?>
		</ul>
	</nav>
	<div id="dStore" class="content">
		<?php foreach(paymentsCart::getCat() as $k=> $cat): ?>
			<section id="section-<?= $k; ?>">
				<?php foreach(paymentsCart::getItens() as $items): ?>
					<?php if($cat->id == $items->catid): ?>
						<div class="dStoreItems">
							<a href="<?= $url;?>?store=course&cat=<?= $items->catid;?>&courseId=<?= $items->id; ?>">
							<div class="dStoreImg"><img src="<?= $items->image;?>" alt="Course image" /></div>
							<h4><?= $items-> title; ?></h4>
							<?php if($items->discount){
								//Preço com desconto
								$originalPrice 	= $items-> price;
								$finalPrice 	= $items->price - ($items->price * ($items-> discount/100));
							?>
								<div class="PriceDe">
									<span class="lPriceIn">De: </span> <span class="lPriceInDe">R$ <span class="lPriceV"><?= number_format( $originalPrice , 2, ',', '.'); ?></span></span>
								</div>
							<?php }else{
								$finalPrice = $items-> price;
							} ?>
							
							<span class="dSprice"><span class="lPriceIn">Por: </span><span class="dSpriceM"><?php if($finalPrice  != ''): ?>R$ </span><?= number_format( $finalPrice , 2, ',', '.'); ?><?php endif; ?></span>
							<span class="dSbtn">Continuar</span>
							</a>
						</div>
					<?php endif; ?>
				<?php endforeach ?>
			</section>
		<?php endforeach ?>
	</div><!-- /content -->
</div><!-- /tabs -->
<script src="<?= $base?>components/com_s7dpayments/assets/js/cbpFWTabs.js"></script>
<script>
new CBPFWTabs( document.getElementById( 'tabs' ) );
</script>

<?php 
	// Destruir carrinho que foi abandonado após um tempo calculado em minutos pela data.
	foreach(paymentsCart::getCartList() as $k=> $items):
		$tempMinutes = 7200; //tempo em minutos

		$ddate = explode("-",explode(" ",$items->date)[0]);
		$dhour = explode(":",explode(" ",$items->date)[1]);

		$time = time() - mktime($dhour[0],$dhour[1],$dhour[2],$ddate[1],$ddate[2],$ddate[0]);
		$temp = (int) ($time / 60);

		if($temp > $tempMinutes):
			paymentsCart::delCartExpire($items->cartid);
		endif;
	endforeach;


?>