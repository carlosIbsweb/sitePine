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

if(!empty($vdarray)):
	require('default_video_item.php');
else:
	echo '<div class="alert alert-danger">';
  	echo '<strong>Acesso negado!</strong> Você precisa de permissão para visualizar essa página.';
  	echo '</div>';
endif;
?>