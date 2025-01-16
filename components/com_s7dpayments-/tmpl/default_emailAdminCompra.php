<?php
/**
 * @version     1.0.0
 * @package     com_s7dpayments
 * @copyright   Copyright (C) 2016. Todos os direitos reservados.
 * @license     GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

?>
<div id="pineMail">
<h2>Nova Compra com os seguintes dados:</h2>
<table id="tablePine">
	<tr><td><strong>Nome: </strong> </td> <td> {nome}</td></tr>
	<tr><td><strong>Usuário: </strong> </td> <td> {username}</td></tr>
	<tr><td><strong>Telefone: </strong> </td> <td> {telefone}</td></tr>
	<tr><td><strong>Endereço: </strong> </td> <td> {endereco}</td></tr>
	<tr><td><strong>Data da compra: </strong> </td> <td> {date}</td></tr>
	<tr><td><strong>Forma de Pagamento: </strong> </td> <td> {forma}</td></tr>
</table>
{produtos}
</div>