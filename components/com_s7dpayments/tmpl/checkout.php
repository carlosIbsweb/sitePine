<?php
/**
 * @package     
 * @subpackage  com_s7dpayments
 **/
//financeiro@depaulanegocios.com.br - email para o pagseguro da Depaula
// No direct access.
defined('_JEXEC') or die;

?>

<form  method="post" action="https://pagseguro.uol.com.br/checkout/checkout.jhtml" name="checkout">
  <input type="hidden" name="email_cobranca" value="contato@pinetreefarm.com.br">
  <input type="hidden" name="tipo" value="CP">
  <input type="hidden" name="moeda" value="BRL">
  <input type="hidden" name="encoding" value="UTF-8">
<?php 
$i = 1;
if(is_array(json_decode(paymentsCart::getCart($cartid,'cartid','products'),true))):
foreach(json_decode(paymentsCart::getCart($cartid,'cartid','products')) as $k => $items): ?>
  <?php $valor = 0; foreach($items->criancas as $qntCri){$valor = $valor + count($qntCri);}?>
  <input type="hidden" name="item_id_<?= $i;?>" value="<?= $items->id; ?>">
  <input type="hidden" name="item_descr_<?= $i;?>" value="<?= '.:: '.$mTitle.' ::. ' .$items->course; ?>">
  <input type="hidden" name="item_quant_<?= $i;?>" value="<?= $valor;?>">
  <input type="hidden" name="item_valor_<?= $i;?>" value="<?= number_format( $items->price , 2, ',', '.'); ?>">
  <input type="hidden" name="item_frete_<?= $i;?>" value="0">
  <input type="hidden" name="item_peso_<?= $i;?>" value="0">
<?php $i++; endforeach ?>

<?php foreach(paymentsCart::getUser($userid) as $dados): ?>
  <?php 
    //prefixo
    $pref     = explode(")",$dados-> telefone);
    $prefixo = str_replace("(","",$pref[0]);

    //Telefone
    $tel = trim($pref[1]);
  ?>
  <input type="hidden" name="cliente_nome" value="<?= $dados-> name; ?>">
  <input type="hidden" name="cliente_cep" value="<?= $dados-> cep; ?>">
  <input type="hidden" name="cliente_end" value="<?= $dados-> endereco; ?>">
  <input type="hidden" name="cliente_num" value="<?= $dados-> id; ?>">
  <input type="hidden" name="cliente_bairro" value="<?= $dados-> bairro; ?>">
  <input type="hidden" name="cliente_cidade" value="<?= $dados-> cidade; ?>">
  <input type="hidden" name="cliente_uf" value="<?= $dados-> estado; ?>">
  <input type="hidden" name="cliente_pais" value="BRA">
  <input type="hidden" name="cliente_ddd" value="<?= $prefixo; ?>">
  <input type="hidden" name="cliente_tel" value="<?= $tel; ?>">
  <input type="hidden" name="cliente_email" value="<?= $dados-> email; ?>">
<?php endforeach; endif; ?>

<input type="image" style="display:none">
</form>

  <input type="submit" class="dEnC" name="submit" data-toggle="modal" data-target="#pineFinaCom" alt="Pague com PagSeguro - é rápido, grátis e seguro!" id="eba" value="Concluir compra">
