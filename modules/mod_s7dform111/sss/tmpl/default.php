<?php

/**
 * @subpackage  mod_wkcontact
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */


// Acesso ao Joomla
defined('_JEXEC') or die;

$moduleId = 'awForm-'.$module->id;

/*
*Gerando o PDF.
*/

if(isset($_GET['planilha'])){

	if($_POST['pass'] != 'senhaAdeb'){

		echo '
			<form action="" method="post" class="adepolCsv">
				<input type="text" autocomplete="off" name="pass" placeholder="Digite sua Senha...">
				<div class="form-group">
				<input type="submit" value="Baixar Arquivo" class="btn btn-success"/>
				</div>
			</form>
		';
		echo isset($_POST['pass']) ? '<div class="alert alert-danger">Senha inválida!</div>' : '';
		return;
	}

	$list = array_map(function($elements){
		if($elements->Data){
			$d = $elements->Data === '0000-00-00 00:00:00' ? ' - ' : date('d/m/Y H:i:s',strtotime($elements->Data));
			//$elements->Data =  ? ' - ' : date('d/m/Y',strtotime($elements->Data));
			$elements->Data = $d;
		}
		return (array) $elements;
	},modS7dformHelper::getListBeach($_GET['form'],$params));


	$headers = array_keys($list[0]);

	$dados = $list;

$arquivo = fopen($_GET['form'].'.csv', 'w');

fputcsv($arquivo , array_map('utf8_decode', $headers),';');

foreach ($dados as $chave => $valor) {
    $produto[$chave]  = $valor['produto'];
    $preco[$chave] = $valor['preco'];
}
/*// SORT_ASC para ordem crescente
array_multisort($preco, SORT_ASC, $dados);*/

foreach ($dados as $linha) {
    fputcsv($arquivo, array_map('utf8_decode', $linha),';');
}
fclose($arquivo);

//-----------------------------------------------------
$url = $_GET['form'].'.csv';

// Use basename() function to return
// the base name of file
$file_name = basename($url); 
  
$info = pathinfo($file_name);
  
// Checking if the file is a
// CSV file or not
if ($info["extension"] == "csv") {
  
    /* Informing the browser that
    the file type of the concerned
    file is a MIME type (Multipurpose
    Internet Mail Extension type).
    Hence, no need to play the file
    but to directly download it on
    the client's machine. */
    header("Content-Description: File Transfer"); 
    header("Content-Type: application/octet-stream"); 
    header("content-type:application/csv;charset=iso-88959-15");
    header(
    "Content-Disposition: attachment; filename=\""
    . $file_name . "\""); 
    readfile ($url);
} 
  
else echo "Sorry, that's not a CSV file";
   
exit(); 
return;
}

if(isset($_GET['confirmarEmail']))
{
	include('_confirm.php');
	return;
}

if(isset($_GET['pdf']))
{
	awPdf::gPdf($params);
}

if(isset($_GET['awEdit']))
{
	include('_edit.php');
}else
{
	include('_form.php');
}

if(isset($_GET['download'])){
	if($_GET['redirect'] == $_SESSION['redirectDownload']){
		unset($_SESSION['redirectDownload']);
		awDownload::awFile($params);
	}
}

