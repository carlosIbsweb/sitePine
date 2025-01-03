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

//school: \''.paymentsSchool::getSchool().'\'
$js = '
	jQuery(function($){
		$( document ).ready(function(){
			$( document ).S7dPayments({
				school: $(".traz-escolas").html()
			});

			$( document ).on("change","#nome-escola",function(){

				if($(this).val().toLowerCase() == "naescola"){
					$("#pineAddEsc").modal()
				}
			})
			
		})
	});

';

$doc->addScriptDeclaration($js);

?>

<?php 

$valor = 0;
$dias = [];
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
						//$priceDia = $items->price/$divD;
						$_SESSION['sPTitle'] = $items-> title;
						$_SESSION['sPDescription'] = strip_tags($items-> description);
						$_SESSION['sPImage'] = strip_tags($items-> image);
						//Params Categoria Pai
						$cparam = json_decode(s7dPayments::getCategory(s7dPayments::getCategory($cat->id,'parent_id'),'params'));
						$dias = paymentsCart::eDias($cparam->diarias);

						$textBottom = helpers::existir($cparam->textPayment,'Semana');
						$textoProdutoTitulo = helpers::existir($cparam->textoProdutoTitulo,'Crianças');
						$textoProdutoInscricao = helpers::existir($cparam->textoProdutoInscricao,'Inscrever Crianças');
						$textoFormTitulo = helpers::existir($cparam->textoFormTitulo,'Dados da Criança');
						$textoLabelNome = helpers::existir($cparam->textoLabelNome,'Nome da Criança');
						$exibirDadosCrianca = $cparam->exibirDadosCrianca ?? true;
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
				<span><?= $textoProdutoTitulo;?></span> - <span class="cQnt"><?= $valor; ?></span>
			</li>
			<li class="criList">
				<div style="display: none" class="traz-escolas">
					<?= paymentsSchool::getSchool();?>
				</div>
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
												<?php /*if($name == 'escola'): ?>
													<?php $sEscola = $val;?>
													<select name="criancas[<?= $kid;?>][<?= $name;?>]" class="form-control" id="escola-crianca">
														<option>Selecionar</option>
														<?= paymentsSchool::getSchool();?>
													</select>
												<?php endif */?>
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

								<?php 
									$diarias = $item->diarias;
									$semana = $item->semana;

								?>
								
							<?php endif;?>
						<?php endforeach;?>
				<?php endif ?>
				</ul>

				<?php if($items->discount){
					//Preço com desconto
					$originalPrice 	= $items-> price;
					$finalPrice 	= $items->price - ($items->price * ($items-> discount/100));
					$priceDia = $finalPrice/$divD;
					//Arrendodnado para baixo a diária
					$priceDia = floor($priceDia * 100) / 100;
				?>

				<?php }else{
					$finalPrice = $items-> price;
					$priceDia = $finalPrice/$cparam->divisao;
					//Arrendodnado para baixo a diária
					$priceDia = floor($priceDia * 100) / 100;
				} 

				$textoSemana = $textBottom;
				?>
				<div class="bTCmais" data-texto-label-nome="<?= $textoLabelNome?>" data-exibir-dados-crianca="<?= $exibirDadosCrianca;?>" data-toggle="modal" data-target="#pineAddCri" >
					<?= $textoProdutoInscricao;?></div>
				<?php //Botão toogle diarias;?>
				<div class="btnSem" data-pricesem="<?= number_format( $finalPrice , 2, ',', '.'); ?>" data-pricedia="<?= number_format( $priceDia , 2, ',', '.'); ?> <span>(Diária)</span>">
                

                <?php 
                	/*
					Start Forma de Exibição -------------------------------------------------------
                	Forma de exibição dos botões conforme a seleção do tipo de venda
                		case 1: Semana ou Diárias
                		case 2: Apenas Semana
                		case 3: Apenas Diárias
                	*/
                ?>
                <?php 
                switch($cparam->tipodevenda){
                    case 1:
                ?>
                    <a class="btnSsem <?= $semana != 'diaria' ? 'sact' : '';?>"><?= $textoSemana;?></a>
					<a class="btnSdia <?= $semana == 'diaria' ? 'sact' : '';?>">Diárias</a>
				</div>
				<div style="display:none" class="bTDmais addDiarias" data-toggle="modal" data-target="#pineAddDi"><i class="fa fa-hand-pointer-o" aria-hidden="true"></i> Selecionar o(s) dia(s) (<span data-diaz="0">0</span>)</div>
                <?php
                    	break;
                    case 2:
                ?>
                    <a class="btnSsem btnUniq <?= $semana != 'diaria' ? 'sact' : '';?>"><?= $textoSemana;?></a>
                </div>
                <?php
                   		break;
                    case 3:
                ?>
                    <a class="btnSdia btnUniq sact">Diárias</a>
                </div>
                <div style="display:flex" class="bTDmais addDiarias" data-toggle="modal" data-target="#pineAddDi"><i class="fa fa-hand-pointer-o" aria-hidden="true"></i> Selecionar o(s) dia(s) (<span data-diaz="0">0</span>)</div>
            <?php
                break;
            }
            //#end Forma de exibição--------------------------------------------------------------
            ?>
                
                
                <?php /*if(!empty($dias->days) && !empty($cparam->diarias)):?>
					<a class="btnSsem <?= $semana != 'diaria' ? 'sact' : '';?>"><?= $textoSemana;?></a>
					<a class="btnSdia <?= $semana == 'diaria' ? 'sact' : '';?>">Diárias</a>
					<?php else:?>
					<a class="btnSsem btnUniq <?= $semana != 'diaria' ? 'sact' : '';?>"><?= $textoSemana;?></a>
					<?php endif;?>
				</div>
				<?php if(!empty($cparam->diarias)):?>
				<div class="bTDmais addDiarias" data-toggle="modal" data-target="#pineAddDi"><i class="fa fa-hand-pointer-o" aria-hidden="true"></i> Selecionar o(s) dia(s) (<span data-diaz="0">0</span>)</div>
			    <?php endif;*/?>

				</div>
			</li>
			<li class="lPrice">
				<?php if($items->discount){ ?>
					<div class="PriceDe">
						<span class="lPriceIn">De:</span> <span class="lPriceInDe">R$ <span class="lPriceV"><?= number_format( $originalPrice , 2, ',', '.'); ?></span></span>
					</div>
				<?php } ?>
				<span class="hidden puToog">
					<input type="radio" name="course[semana]" class="csem" value="semana" <?= $semana != 'diaria' ? 'checked="true"' : '';?> >
					<input type="radio" name="course[semana]" class="cdia" value="diaria" <?= $semana == 'diaria' ? 'checked="true"' : '';?>>
				</span>
				<div class="pricePine">
				<span class="lPriceIn">Por: </span><span class="dSpriceM">R$ </span><span class="lPriceV pAll"><?= number_format( $finalPrice , 2, ',', '.'); ?></span>
				</div>
				<input type="hidden" name="course[title]" value="1">
				<input type="hidden" class="lPriceVHidden" value="<?=  $finalPrice;?>">
				<input type="hidden" name="course[diaria]" id="pVdiaria" value="<?= $diarias;?>">
				<input type="submit" class="lBtnComprar" value="Prosseguir" name="enviar" />
			</li>
			</form>
		</ul>
	</div>
</div>


<div class="modal fade pineModal" id="pineAddCri" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog pineAddCri" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?= $textoFormTitulo; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"><form id="formCri"></form></div>
    </div>
  </div>
</div>

<div class="modal fade pineModal" id="pineAddDi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog pineAddDi" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Diárias</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<div class="criDesc">
				<h5>Clique e selecione os dias desejados:</h5>
				<p>Selecione entre 1 a 3 dias.</p>
			</div>
			<div class="pDias">

			</div>
	  </div>
	  <div class="modal-footer">
			<input type="hidden" class="piPayOk" data-dismiss="modal">
			<button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
		</div>
    </div>
  </div>
</div>
<?php /*
<?php //Nova escola;?>
<div class="modal fade pineModal" style="background: rgba(0,0,0,0.6)" id="pineAddEsc" tabindex="999" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #3c6e15">
        <h5 class="modal-title" id="exampleModalLabel">Cadastrar Nova Escola</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      
      <div class="modal-body"><form id="add_n_escola">
      	<input type="text" name="escola" placeholder="Nome da escola">
      	<div style="float: right;
    margin-top: 10;">
      	<input type="submit" name="" class="btn btn-primary" value="Cadastrar">
      </div>
      </form></div>*/ ?>
    </div>
  </div>
</div>
<script>
	jQuery(function($){
		$( window ).load(function(){
			$('#escola-crianca option[value="<?= $sEscola;?>"]').attr("selected","selected");
		})
		
		var ddAct = '<?= $semana;?>';
		var jprice = ddAct == 'diaria' ? $('.btnSem').data('pricedia') : $('.btnSem').data('pricesem');
		var pvd = $('#pVdiaria').val().split(',');
			pvd = pvd.filter(function(el){
      			return el != ''
      		})
		if(ddAct == 'diaria'){
			$('.addDiarias').css({opacity:0,display:'flex'}).animate({
				opacity:1
			},500) ;
		}

		$('.addDiarias').find('span').html(pvd.length)

		$('#dStoreItem .lPriceV.pAll').html(jprice)

		/*Toogle selecione----------------------*/

		$( document ).on('click','.btnSem a:not(.sact)',function(ev){
			ev.preventDefault();
			var jprice = '';
			<?php //Classe do botão;?>
			var jbtns = $(this).parent('.btnSem');

			jbtns.find('.sact').removeClass('sact')
			
			if(!$(this).hasClass('sact')){
				if($(this).hasClass('btnSsem')){
					
					$('.addDiarias').fadeOut('fast',function(){});
					
					jprice = jbtns.data('pricesem');
						$('.puToog').find('.csem').click();
					
				}

				if($(this).hasClass('btnSdia')){
					jprice = jbtns.data('pricedia');
					$('.puToog').find('.cdia').click();

					$('.addDiarias').css({opacity:0,display:'flex'}).animate({
						opacity:1
					},500) ;
				}

				$('#dStoreItem .lPriceV.pAll').html(jprice)

				$(this).addClass('sact')
			}
		})
		/*----------------------------------------------------------------- */


		/*Abrindo os dias*/
		$( document ).on('click','.addDiarias',function(){
			var temp = []
			/*Trazendo os dias en intervalos*/
			var jjj = <?= json_encode($dias->days);?>;
			var sems = <?= json_encode($dias->sem);?>;
			var pvd = $('#pVdiaria').val().split(',');

			//Convertendo para values inteiros no array
			pvd = pvd.map(function(x){
				return parseInt(x)
			})

			//Convertendo para values inteiros no array
			jjj = jjj.map(function(x){
				return parseInt(x)
			})

			//Diarias selecionadas 

			$.each(jjj,function(rin,v)
			{
				var act = jQuery.inArray(v,pvd) != -1 ? 'diActive' : '';
				var tp = `<li class="`+act+`" data-id="`+v+`"><div class="dsContent"><span class="dsem">`+sems[rin]+`</span><span class="ddia">`+v+`</span></div></li>`;
				temp.push(tp)
			})

			$('#pineAddDi').find('.modal-body').find('.pDias').html('<ul>'+temp.join('')+'</ul>')
		})

		/*Interação dos dias*/
		$( document ).on('click','#pineAddDi .modal-body ul li',function(){

			var pvd = $('#pVdiaria').val().split(',');
			var pvdadd = pvd.filter(function(el){
      			return el != ''
      		})
			
			//Convertendo para values inteiros no array
			pvd = pvd.map(function(x){
				return parseInt(x)
			})
			var vind = parseInt($(this).data('id'));

			pvdadd.push(vind)

			if($(this).hasClass('diActive'))
			{
				$(this).removeClass('diActive')
				$('#pVdiaria').val(jdia(vind,pvd))

				$('.addDiarias').find('span').html(pvd.length)
			}else{

				if(pvd.length < 3)
				{
					$(this).addClass('diActive')
					$('#pVdiaria').val(nOrd(pvdadd))

					$('.addDiarias').find('span').html(pvdadd.length)
				}else{
					alert('Atenção! \rVocê já selecionou o máximo de diárias permitido para esse ingresso.')
				}	
			}
		})

		/*Jogando os dias no input*/
		let jdia = function(vin,arr){
			var ain = jQuery.inArray(vin,arr);
				 arr.splice(ain,1)

				 return nOrd(arr)
		}

		let nOrd = function(v){
			v.sort(function(a,b){
				return a - b
        	})
			return v
		}
		
	})
</script>

<?php 

paymentsCart::getGrupo();
