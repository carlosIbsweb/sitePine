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

$doc->addScript(JUri::base(true).'/components/com_s7dpayments/assets/js/formValid/dist/jquery.validate.js');
$doc->addScript(JUri::base(true).'/components/com_s7dpayments/assets/js/s7dpaymentsUser.js');

//session_unset();

if(empty($user->id)):
//sessão de retorno
header('location: '.$url.'/?user=login');
//retorno
$_SESSION['sreturn'] = base64_encode(JUri::getInstance()->toString());
endif;

$js = '
	jQuery(function($){
		$( document ).ready(function(){
			$( document ).S7dPayments();
		})
	});

';

$doc->addScriptDeclaration($js);

?>

<?php 

$valor = 0;
if(is_array(json_decode(paymentsCart::getCart($cartid,'cartid','products'),true))){
	foreach(json_decode(paymentsCart::getCart($cartid,'cartid','products')) as $items){
		if($items->id === $_GET['courseId']){
			foreach($items->criancas as $qntCri){$valor = $valor + count($qntCri);}
		}
	}
}
?>
<div id="dStoreItem">
	<div class="dStoreItem">
		<?php foreach(s7dPayments::getCat($_GET['cat']) as $cat): ?>
			<?php $catTitle = $cat->title; ?>
			<?php foreach(s7dPayments::getItens($_GET['courseId']) as $items): ?>
				<?php if($cat->id == $items->catid): ?>
					<?php
						$listVideos = $items-> videos;
						$price = $items->price;
						$_SESSION['sPTitle'] = $items-> title;
						$_SESSION['sPDescription'] = strip_tags($items-> description);
						$_SESSION['sPImage'] = strip_tags($items-> image);
					 ?>
					<h2><?= $items-> title; ?></h2>
					<div class="dStoreItemImg">
						<img src="<?= $items-> image; ?>" alt="Imagem Course" />
					</div>
					<div class="dStoreItemDescription">
						<span class="lTitleDesc">Roteiro</span>
						<?= $items-> description; ?>
					</div>
				<?php endif; ?>
			<?php endforeach ?>
		<?php endforeach ?>
	</div>

	<div class="dStoreDetails">
		<ul>
			<li class="lCat"><span>Categoria</span><?= $catTitle; ?></li>

			<form action="" method="post" id="dpFormStore">
			<li class="criQnt">
				<span>Crianças</span> - <span class="cQnt"><?= $valor; ?></span>
			</li>
			<li class="criList">
				<div class="criancasMais">
					<div class="cCriHidden" style="display:none">
						<?php foreach(json_decode(paymentsCart::getCart($cartid,'cartid','products')) as $item): ?>
							<?php if($item->id === $_GET['courseId']) : ?>
								<?php foreach($item->criancas as $kid => $icri): ?>	
									<div class="formCri" id="<?= $kid;?>">
										
										<?php foreach($icri as $name => $val): ?>
												<?php $label = explode('-',$name)[1];?>
												<div class="form-group">
												<?php if($label == 'label'): ?>
													<label for="<?= $name;?>-crianca" class="col-form-"><?= $val;?></label>
													<input type="hidden" class="form-control" name="criancas[<?= $kid;?>][<?= $name;?>]" id="<?= $name;?>" value="<?= $val;?>">
												<?php endif ?>
												<?php if($name == 'escola'): ?>
													<?php $sEscola = $val;?>
													<select name="criancas[<?= $kid;?>][<?= $name;?>]" class="form-control" id="escola-crianca">
														<option value="411 Norte">411 Norte</option>
														<option value="Affinity Arts">Affinity Arts</option>
														<option value="Alvacir Vite Rossi">Alvacir Vite Rossi</option>
														<option value="Anjo da Guarda">Anjo da Guarda</option>
														<option value="Arvense">Arvence</option>
														<option value="Arvense">Avidus</option>
														<option value="Benjamin Franklin Int’L School">Benjamin Franklin Int’L School</option>
														<option value="Britishi School">Britishi School</option>
														<option value="Caic Unesco">Caic Unesco</option>
														<option value="Canarinho">Canarinho</option>
														<option value="Cantinho Mágico">Cantinho Mágico</option>
														<option value="Cei Assefe">Cei Assefe</option>
														<option value="Centro Educacional Parque Encantado">Centro Educacional Parque Encantado</option>
														<option value="Cieic">Cieic</option>
														<option value="Ciman">Ciman</option>
														<option value="Claretiano">Claretiano</option>
														<option value="Cnec">Cnec</option>
														<option value="Coc">Coc</option>
														<option value="Colégio Adventista">Colégio Adventista</option>
														<option value="Construção do Saber">Construção do Saber</option>								
														<option value="Colégio Mauricio Salles de Mello">Colégio Mauricio Salles de Mello</option>
														<option value="Colégio Moraes Rego">Colégio Moraes Rego</option>
														<option value="Dom Bosco">Dom Bosco</option>
														<option value="Dom Pedro II">Dom Pedro II</option>
														<option value="Ec 209 Sul">Ec 209 Sul</option>
														<option value="Escola Americana">Escola Americana</option>
														<option value="Escola Arara Azul">Escola Arara Azul</option>
														<option value="Escola Batista Asa Sul">Escola Batista Asa Sul</option>
														<option value="Escola Classe 111 Sul">Escola Classe 111 Sul</option>
														<option value="Escola DNA">Escola DNA</option>
														<option value="Escola das Nacoes">Escola das Nacoes</option>
														<option value="Escola Internacional de Genebra">Escola Internacional de Genebra</option>
														<option value="Everest">Everest</option>
														<option value="Fundação Cabo Frio">Fundação Cabo Frio</option>
														<option value="INDI">INDI</option>
														<option value="Kingdom Kids">Kingdom Kids</option>
														<option value="Le Petit Galois - Asa Sul">Le Petit Galois - Asa Sul</option>
														<option value="Leonardo da Vinci - Asa Norte">Leonardo da Vinci - Asa Norte</option>
														<option value="Lffm">Lffm</option>
														<option value="Lycée Français François Mitterrand">Lycée Français François Mitterrand</option>
														<option value="Mackenzie">Mackenzie</option>
														<option value="Mapple Bear">Mapple Bear</option>
														<option value="Maria Imaculada">Maria Imaculada</option>
														<option value="Maria Montessori">Maria Montessori</option>
														<option value="Maristinha Pio Xii">Maristinha Pio Xii</option>
														<option value="Master">Master</option>
														<option value="Miri Piri">Miri Piri</option>
														<option value="Montreal">Montreal</option>
														<option value="Oasis Creche Bem Me Quer">Oasis Creche Bem Me Quer</option>
														<option value="Pedacinho do Céu">Pedacinho do Céu</option>
														<option value="Pia Mater">Pia Mater</option>
														<option value="Santa Rosa">Santa Rosa</option>
														<option value="Santo Andre">Santo Andre</option>
														<option value="Serios">Serios</option>
														<option value="Sibipiruna">Sibipiruna</option>
														<option value="Sigma">Sigma</option>
														<option value="Viraventos">Viraventos</option>
														<option value="Vivendo E Aprendendo">Vivendo E Aprendendo</option>
														<option value="CECAN - Candanguinho">CECAN - Candanguinho</option>
														<option value="CEAV Jr">CEAV Jr</option>
														<option value="La Salle">La Salle</option>
														<option value="Marista João Paulo II">Marista João Paulo II</option>
														<option value="Waldorf Moara">Waldorf Moara</option>
														<option value="Centro Educacional Maria Auxiliadora">Centro Educacional Maria Auxiliadora</option>
														<option value="Colégio Batista">Colégio Batista</option>
														<option value="Colégio Corjesu">Colégio Corjesu</option>
														<option value="Colégio Dromos">Colégio Dromos</option>
														<option value="Colégio Notre Dame">Colégio Notre Dame</option>
														<option value="Colégio Perpétuo Socorro">Colégio Perpétuo Socorro</option>
														<option value="COC Lago Norte">COC Lago Norte</option>
														<option value="COC Jardim Botânico">COC Jardim Botânico</option>
														<option value="Colégio Sagrada Família">Colégio Sagrada Família</option>
														<option value="Madre Carmen Salles">Madre Carmen Salles</option>
														<option value="Montessoriana Educação Infantil">Montessoriana Educação Infantil</option>
														<option value="Objetivo">Objetivo</option>
														<option value="Parque Encantado">Parque Encantado</option>
														<option value="Santa Rosa">Santa Rosa</option>
														<option value="SIS Swiss International School">SIS Swiss International School</option>
														<option value="Le Petit Galois - Águas Claras">Le Petit Galois - Águas Claras</option>
													</select>
												<?php endif ?>
												<?php if($label != 'label' && $name != 'autorizada' && $name != 'escola'): ?>
												<input type="text" class="inputPaym form-control <?= $name == 'nome' ? 'pPrima' : null;?>" name="criancas[<?= $kid;?>][<?= $name;?>]" id="<?= $name;?>-crianca" value="<?= $val;?>">
				 								<?php endif ?>
				 								<?php if($name == 'autorizada'): ?>
				 									<label class="radio-inline"><input class="inputPaym" type="radio" <?= $val == 'Sim' ? 'checked' : null;?> name="criancas[<?= $kid;?>][<?= $name;?>]" value="Sim" id="<?= $name;?>-sim">Sim</label>
													<label class="radio-inline"><input class="inputPaym" type="radio" <?= $val == 'Não' ? 'checked' : null;?> name="criancas[<?= $kid;?>][<?= $name;?>]" value="Não" id="<?= $name;?>-nao">Não</label>
				 								<?php endif ?>
				 								</div>
				 						<?php endforeach ?>
				 						<div class="modal-footer">
											<input type="hidden" class="piPayOk" data-dismiss="modal">
							        		<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
							        		<input type="submit" class="btn btn-primary pineSaveCri" value="Adicionar">
      									</div>
									</div>
								<?php endforeach;?>
							<?php endif;?>
						<?php endforeach;?>
					</div>

					<ul class="criLinks">
					<?php if(is_array(json_decode(paymentsCart::getCart($cartid,'cartid','products'),true))): ?>
					<?php foreach(json_decode(paymentsCart::getCart($cartid,'cartid','products')) as $item): ?>
							<?php if($item->id === $_GET['courseId']) : ?>

								<?php foreach($item->criancas as $kid => $icri): ?>	

										<li id="citem<?= $kid;?>">
										<?php foreach($icri as $name => $val): ?>
											
											<?php if($name == 'nome'): ?>
												
													<span class="pRemov">x</span>
													<a href="#" class="cCriEdit" id="cCriEdit-<?= $kid;?>" data-id="<?= $kid;?>" data-toggle="modal" data-target="#pineAddCri"><?= $val;?></a>
				 								
				 							<?php endif;?>
				 						<?php endforeach ?>
				 						</li>
								<?php endforeach;?>
								
							<?php endif;?>
						<?php endforeach;?>
				<?php endif ?>
				</ul>
				<div class="bTCmais" data-toggle="modal" data-target="#pineAddCri">Adicionar Criança</div>
				</div>
			</li>
			<li class="lPrice">
				<?php if($items->discount){
					//Preço com desconto
					$originalPrice 	= $items-> price;
					$finalPrice 	= $items->price - ($items->price * ($items-> discount/100));
				?>

					<div class="PriceDe">
						<span class="lPriceIn">De:</span> <span class="lPriceInDe">R$ <span class="lPriceV"><?= number_format( $originalPrice , 2, ',', '.'); ?></span></span>
					</div>

				<?php }else{
					$finalPrice = $items-> price;
				} 
				?>
				<span class="lPriceIn">Por: </span><span class="dSpriceM">R$ </span><span class="lPriceV"><?= number_format( $finalPrice , 2, ',', '.'); ?></span>
				<input type="hidden" name="course[title]" value="1">
				<input type="hidden" class="lPriceVHidden" value="<?=  $finalPrice;?>">
				<input type="submit" class="lBtnComprar" value="Comprar" name="enviar" />
			</li>
			</form>
		</ul>
	</div>
</div>


<div class="modal fade pineModal" id="pineAddCri" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog pineAddCri" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Dados da Criança</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"><form id="formCri"></form></div>
    </div>
  </div>
</div>

<script>
	jQuery(function($){
		$( window ).load(function(){
			$('#escola-crianca option[value="<?= $sEscola;?>"]').attr("selected","selected");
		})
	})
</script>