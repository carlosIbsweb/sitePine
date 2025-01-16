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

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$base = JUri::base(true);

$site = JPATH_SITE.'/components/com_s7dpayments/tmpl/';

// Get the document object.
$doc = JFactory::getDocument();
JHTML::_("jquery.framework", true);

$doc->addStyleSheet($base.'/components/com_s7dpayments/assets/css/style.css?'.uniqid(), 'text/css');
$doc->addScript($base.'/components/com_s7dpayments/assets/js/jquery.maskedinput.js');
$doc->addScript($base.'/components/com_s7dpayments/assets/js/scripts.js');

$doc->addScript($base.'/components/com_s7dpayments/assets/js/s7dpayments.js?'.uniqid(), 'text/javascript');



?>

<?php
	require_once('menu.php');

/**********
 *Acessar as páginas da aplicação.
**********/
if(isset($_GET['video'])):
	require('default_video.php');
elseif(isset($_GET['courseId']) and !isset($_GET['store']) and !isset($_GET['file']) and !isset($_GET['topic']) and !isset($_GET['addtopic'])):
	require('default_pag.php');
elseif(isset($_GET['store']) and !isset($_GET['cat'])):
	require('default_store.php');
elseif(isset($_GET['store']) && $_GET['store'] == 'course' and isset($_GET['courseId']) and isset($_GET['cat'])):
	require('default_store_item.php');
elseif(isset($_GET['cart'])):
	require('default_cart.php');
elseif(isset($_GET['courses'])):
	require('default_pag_list.php');
elseif(isset($_GET['user']) and $_GET['user'] == 'login'):
	require('default_login.php');
elseif(isset($_GET['user']) and $_GET['user'] == 'register'):
	require('default_register.php');
elseif(isset($_GET['file']) and isset($_GET['courseId'])):
	require_once('file.php');
elseif(isset($_GET['addtopic'])  && isset($_GET['courseId'])):
	require_once('default_add_topic.php');
elseif(isset($_GET['topic']) && !isset($_GET['itemId'])):
	require_once('default_topic_list.php');
elseif(isset($_GET['topic']) && isset($_GET['itemId']) && isset($_GET['courseId'])):
	require_once('default_topic.php');
elseif(isset($_GET['notification'])):
	require_once('notification.php');

else:
	require('default_store.php');
	/**********
	 *Menu principal para selecionar a página de visualizar os cursos ou comprar.
	**********/
	/*
	echo '<div class="pagdefault">';
 	echo '<a href="'.$url.'?courses">Meus Cursos</a>';
 	echo '<a href="'.$url.'?store">Comprar cursos</a>';	
 	echo '</div>';
 	*/
endif;
