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

<?php 
if(!empty($vdarray)) : 
	paymentsForum::executTopic();
?>

  <h2>Adicionar Tópico</h2>

<div class="dforumadd">
	<form action="" method="post">
	<span>
		<input type="text" name="title" placeholder="Título">
	</span>
	<span>
		<textarea name="description" placeholder="Descrição"></textarea>
	</span>
		
		<input type="submit" value="enviar" name="tenviar">
	</form>
</div>

<?php else: ?>
	<div class="alert alert-danger">
  		<strong>Acesso negado!</strong> Você precisa de permissão para visualizar essa página.
  	</div>
<?php endif; ?>