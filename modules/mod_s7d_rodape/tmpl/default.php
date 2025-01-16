<?php
/**
 * @version		1.1
 * @package		Site 7 Dias
 * @subpackage	mod_s7d_rodape
 * @copyright	Copyright (C) 2015 - 2015 Site 7 Dias - Todos os direitos reservados.
 */
 
// Acesso
defined('_JEXEC') or die;

// Carrega a imagem
$img = $params->get('styleis') == 'isb' ? 'isblank.png' : 'isdefault.png';

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::BASE().'modules/mod_s7d_rodape/css/is7d.css');
$document->addStyleSheet(JURI::BASE().'modules/mod_s7d_rodape/css/fixed.css');

?>

<div id="cS7D">
	<a href="http://site7dias.com.br" target="_blank" title="Site 7 Dias">
		<span><img src="<?php JURI::BASE(); ?>modules/mod_s7d_rodape/img/<?= $img; ?>" alt="Site 7 Dias" /></span>
	</a>
</div>