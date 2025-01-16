<?php
   /**
    * @package     
    * @subpackage  com_s7dpayments
    **/
   
   // No direct access.
   defined('_JEXEC') or die;
   
   //retorno
   $_SESSION['sreturn'] = base64_encode(JUri::getInstance()->toString());

   //Cupom de Desconto
   $cDiscount = paymentsCart::getCart($cartid,'cartid','discount');
   $cDiscount = $cDiscount != 0 ? $cDiscount : null;

    //destruindo a sessão fcourse.
   if(isset($_SESSION['ftransf'])): 
      unset($_SESSION['ftransf']);
      unset($_SESSION['cartid']);
   endif;

   if(isset($_GET['opa'])):
   paymentsCart::notCli();
endif;
   
   ?>
<form method='post' action=''>
   <div id="dCart">
      <div class="dCartHeader">
         <span class="dCartCourse">Ingresso</span>
         <span class="dCartQnt">Quant.</span>
         <span>Categoria</span>
         <span>Preço</span>
         <span class="dCartDel"></span>
      </div>
      <div class="dCartItems">
         <?php 
            if(is_array(json_decode(paymentsCart::getCart($cartid,'cartid','products'),true))):
               $priceTotal = 0;
               $produtos   = [];
               foreach(json_decode(paymentsCart::getCart($cartid,'cartid','products')) as $k => $items):
                  $valor = 0;
                  $cartItemLink = $menuAlias.'/'.s7dPayments::getCategory(s7dPayments::getCategory($items->catid,'parent_id'),'alias'); 
                  $cartItemTitle = s7dPayments::getCategory(s7dPayments::getCategory($items->catid,'parent_id'),'title');
            
                  if($items->discount){
                     //Preço com desconto
                     $originalPrice    = $items-> price;
                     $finalPrice = $items->price - ($items->price * ($items-> discount/100));
            
                  }else{
                     $finalPrice = $items-> price;
                  }

                  $finalPrice = $cDiscount ? ($finalPrice / (100 - $cDiscount)) * 100 : $finalPrice;
                  
               ?>
         <?php foreach($items->criancas as $qntCri){$valor = $valor + count($qntCri);}?>
         <?php $itemPrice = $finalPrice * $valor;?>
         <div class="dCartItem<?=$_SESSION['ltem'][$items->id] == $items->id ? ' ltem' : null;?>">
            <span class="dCartCourse">
               <a href="<?= $url;?>?store=course&cat=<?= $items->catid;?>&courseId=<?= $items->id; ?>" class="dSbtn">
                  <div class="dCartImg">
                     <img src="<?= $items-> img; ?>" alt="Imagem do Curso" />
                  </div>
               </a>
               <h4>
                  <a href="<?= $menuAlias.'/'.s7dPayments::getCategory(s7dPayments::getCategory($items->catid,'parent_id'),'alias');?>?store=course&cat=<?= $items->catid;?>&courseId=<?= $items->id; ?>" class="dSbtn">
                  <?= '<strong>'.$cartItemTitle.'</strong> '.$items-> course; ?>
                  </a>
               </h4>
               <span class="hidden-lg"></span>
            </span>
            <span class="dCartQnt"><?= $valor;?></span>
            <span><?= $items-> cattitle;?></span>
            <span>R$ <?= number_format( $itemPrice , 2, ',', '.'); ?></span>
            <span class="dCartDel">
            <input type='checkbox' class="delItem" id="delItem<?= $items->id;?>" name='delitem' onChange='submit();' value="<?= $items->id;?>">
            <label class="delItemIcon" for="delItem<?= $items->id;?>" title="Deletar item"></label>
            </span>
         </div>

         <?php 
            $priceTotal = $priceTotal + $itemPrice;
            $priceDiscount = $priceTotal * ($cDiscount/100);
            $priceDiscountTotal = $priceTotal - ($priceTotal * ($cDiscount/100));

            //$priceTotal = $cDiscount ?  $priceTotal - ($priceTotal * ($cDiscount/100)) : $priceTotal; 
            $itemCart = '<td style="padding: 5px 10px;border: 1px solid #ddd;"><strong style="color:#0b7798">'.$cartItemTitle.'</strong> - '.$items->course.'</td>'.'<td style="padding: 5px 10px;border: 1px solid #ddd;">'.$items->cattitle.'</td><td style="padding: 5px 10px;border: 1px solid #ddd; text-align:center">'.$valor.'</td><td style="padding: 5px 10px;border: 1px solid #ddd;"> R$ '.number_format( $itemPrice , 2, ',', '.').'</td>';
            array_push($produtos,$itemCart);

         endforeach; else: $ifCartItem = 'cart'; ?>
         <div class="dCartVazio">Carrinho vazio</div>
         <?php endif; ?>
      </div>
      <input type='checkbox' class="delItem" id="delItemClear" name='delitem' onChange='submit();' value="delAll">
   </div>
</form>



<div class="dCartBottom">
   <a href="<?= $url;?>?store" class="dBtnCont">Efetuar outra inscrição</a>
   <?php if (empty($ifCartItem)): ?>
   <label class="dBtnClear" for="delItemClear" title="Limpar carrinho">Limpar carrinho</label>

   <div class="dEnPag">
   <?php if(!$cDiscount): ?>
   <div class="voucher">
   <form action="" method="post">
   <input type="text" name="pdiscount" placeholder="Insira o voucher aqui" autocomplete="off">
   <input type="submit" name="enviar" value="OK">
</form>
</div>
<?php endif; ?>
   <?Php if($cDiscount): ?>
      <div class="dPrice"><label>Subtotal:</label><span class="denm" >R$</span> <?= number_format( $priceTotal , 2, ',', '.'); ?></div>
      <div class="dPrice dVoucher alert alert-success"><label>Voucher:</label>- <?= number_format( $priceDiscount , 2, ',', '.'); ?></div>
      <div class="dPrice"><label>Total:</label><span class="denm" >R$</span> <?= number_format( $priceDiscountTotal , 2, ',', '.'); ?></div>
   <?php else: ?>
      <div class="dPrice"><label>Total:</label><span class="denm" >R$</span> <?= number_format( $priceTotal , 2, ',', '.'); ?></div>
   <?php endif;?>
      <?php
         if(!empty($userid)):
            require('modPagSeguro.php');
            echo ' <input type="submit" class="dEnC" name="submit" data-toggle="modal" data-target="#pineTermos" alt="Pague com PagSeguro - é rápido, grátis e seguro!" id="eba" value="Concluir compra">';
         else:
            echo '<a class="dEnC" href="'.$url.'?user=login" title="Finalizar compra">Encerrar compra</a>';
         endif; 
         ?>
   </div>
   <?php endif ?>
</div>

<?php 
         if(is_array($produtos)){
            /*Produtos*/
            $pOut[] = '<table class="table table-hover table-dark" style="padding: 20px;border: 1px solid #0898ca;margin-top: 40px;">';
            $pOut[] = '<thead><tr><th>Ingresso</th><th>Categoria</th><th>Quant.</th><th>Preço</th></tr></thead>';
            $pOut[] = implode('<tr>',$produtos);
            if($cDiscount):
               $pOut[] = '<tr scope="row"><td colspan="4" style="padding: 5px 10px;border: 1px solid #ddd;text-align: right;font-size: 15px;color: #127898;"><div><strong style="    color: #000;
    padding: 10px;">Subtotal:</strong><strong>'.number_format( $priceTotal , 2, ',', '.').'</strong></div><span style="background: #aceaae;
    padding: 5px;
    margin: 0 12px 8px 0;
    color: #2e7330;
    font-weight: 600;
    float: left;">Voucher: - '.number_format( $priceDiscount , 2, ',', '.').'</span><strong style="    color: #000;
    padding: 10px;">Total:</strong><strong>R$ '.number_format( $priceDiscountTotal , 2, ',', '.').'</strong></td></tr>';
            else:
               $pOut[] = '<tr scope="row"><th style="text-align:right">Total </th><td colspan="4" style="padding: 5px 10px;border: 1px solid #ddd;text-align: right;font-size: 15px;color: #127898;"><strong>R$ '.number_format( $priceTotal , 2, ',', '.').'</strong></td></tr>';
            endif;
            $pOut[] = '</table>';

            $prodF = implode('',$pOut);

            $_SESSION['prodF'] = $prodF;
         }

 ?>

</div>
<?php
   if(isset($_SESSION['fcourse'])):
      //mensagem de processando se os dados estiverem ok.
      echo '
         <div class="descClose fPagSeguro" id="descClose">
            <span>
               <img src="'.JUri::base(true).'/components/com_s7dpayments/assets/images/spy-loading.gif" />
               <h4>Você será redirecionado para o PagSeguro</h4>
            </span>
         </div>
         <script type="text/javascript">
            var recursiva = function ()
               {
                  document.checkout.submit();                  
               }
               setTimeout(recursiva, 2000);
         </script>
      ';
   endif;

   if(isset($_POST['formPag']) && $_POST['formPag'] == 'ftransf')
   {
    echo '
         <div class="descClose fTransf" id="descClose">
            <span>
            <h5 class="transfHeader">Transfêrencia Bancária em até 4 vezes sem juros</h5>
               <div class="row">
            <div class="col-md-4 info_transfA">
               <strong>Banco do Brasil (001)</strong><br>
               <strong>Agência:</strong> 2887-8<br>
               <strong>Conta:</strong> 27263-9<br>
               <strong>Titular:</strong> Pine Tree Farm Promoção de Eventos EIRELI-ME<br>
               <strong>CNPJ:</strong> 27.504.555/0001-27
            </div>
            <div class="col-md-4 info_transfB" style="margin-bottom:20px">
               <strong>Itaú</strong><br>
               <strong>Agência:</strong> 2709<br>
               <strong>Conta Corrente:</strong> 02161-8<br>
               <strong>Titular:</strong> Grasiela Hauqui Cerutti<br>
               <strong>CPF:</strong> 528.884.440-20
            </div>            
      <div class="row text-center">
         <form action="" method="post">
            <input type="hidden" name="ftransf" />
            <input type="hidden" name="priceTotal" value="'.number_format( $priceTotal , 2, ',', '.').'" />
            <input type="hidden" name="produtos" value="'.$produtos.'" />
            <button class="btn btn-danger pClose">Cancelar</button>
           <input type="submit" class="btn btn-primary" value="Concluir" />
         </form> 
         </div>
            </span>
         </div>
      </div>
   ';
   }

/*'div class="col-md-6 info_transfB" style="margin-bottom:20px">
               <strong>Banco do Brasil</strong><br>
               <strong>Agência:</strong> 4591-8 <br>
               <strong>Conta Corrente:</strong> 108140-3<br>
               <strong>Titular:</strong> Guilherme Crispim Hundley<br>
               <strong>CPF:</strong> 828.250.171.91
            </div>
            <div class="col-md-6" style="color:#ff8e47">
               <strong>Banco Itaú</strong><br>
               <strong>Agência:</strong> 7348 <br>
               <strong>Conta Corrente:</strong> 02043-4<br>
               <strong>Titular:</strong> Alethea Malatesta<br>
               <strong>CPF:</strong> 176.649.848-58
            </div>*/
   
   //destruindo a sessão fcourse.
   if(isset($_SESSION['fcourse'])):
      unset($_SESSION['fcourse']);
      unset($_SESSION['cartid']);
   endif;

   ?>
<div class="modal fade pineModal" id="pineFinaCom" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog pineAddCri" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title text-uppercase" id="exampleModalLabel">Forma de pagamento</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="form-group">
               <form action="" method="post" id="formPag">
    <label for="formPag" class="lFormtransf">Selecione a forma de pagamento</label>
    <select class="form-control" onchange="this.form.submit()" id="formPag" name="formPag">
    <option value="">Selecionar</option>
      <option value="ftransf" data-toggle="modal" data-target="#pineFinaCom">Transferência Bancária (até 4x sem juros)</option>
      <option value="PagSeguro">PagSeguro (à vista ou c/ juros do PagSeguro)</option>
    </select>
    <input type="hidden" name="priceTotal" value="<?= number_format( $priceTotal , 2, ',', '.');?>" />
    </form>
  </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade pineModal" id="pineTermos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog pineAddCri" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title text-uppercase" id="exampleModalLabel">Termo de Adesão</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                 <?php include 'default_termos.php';?> 
               </div>
               <div class="col-md-12">
                  <div class="form-group pinetermos">
                     <input type="checkbox" name="checktermos" data-toggle="modal" data-target="#pineFinaCom" data-dismiss="modal" >
                     <label>Li e aceito os termos</label>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade pineModal" id="pineFinaComTrans" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog pineAddCri" role="document">
   <div class="modal-content">
      <div class="modal-header">
         <h4 class="modal-title text-uppercase" id="exampleModalLabel">Transfêrencia Bancária em até 4 vezes sem juros</h4>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
         </button>
      </div>
      <div class="modal-body">
         <div class="row">
            <div class="col-md-12">
               <p class="text-center"></p>
            </div>
            <div class="col-md-6 info_transfA">
               <strong>Bradesco (237)</strong><br>
               <strong>Agência:</strong> 1421-4<br>
               <strong>Conta:</strong> 2312-4<br>
               <strong>Titular:</strong> Pine Tree Farm Promoção de Eventos EIRELI-ME<br>
               <strong>CNPJ:</strong> 27.504.555/0001-27
            </div>
            <div class="col-md-6 info_transfB">
               <strong>Itaú (341)</strong><br>
               <strong>Agência:</strong> 7348 <br>
               <strong>Conta Corrente:</strong> 02043-4<br>
               <strong>Titular:</strong> Alethea Malatesta<br>
               <strong>CPF:</strong> 176.649.848-58
            </div>
         </div>
         <br>
         <div class="row text-center">
            <button type="button" onclick="location.href = '/termos-de-contratacao';" class="btn btn-primary finComPine" data-dismiss="modal">Concluir</button>
         </div>
      </div>
   </div>
</div>

<script>
   jQuery(function($){
     $( document ).on('click','.pClose',function(){
      $('#descClose').remove();
     })

   })
</script>