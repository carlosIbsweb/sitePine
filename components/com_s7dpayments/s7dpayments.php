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

require(JPATH_SITE.'/components/com_s7dpayments/controller.php');
//Criando url
$app  = JFactory::getApplication();

//Configs------------------------
$divD = 0; //Divisão da diária

$mAlias = $app->getMenu()->getActive()->alias;
$mTitle = $app->getMenu()->getActive()->title;
$mLink = $app->getMenu()->getActive()->link;

/********************/
if(isset($_REQUEST['catid'])):
$parent = s7dPayments::getCategory($_REQUEST['catid'],'parent_id');
$caId  = s7dPayments::getCategory($_REQUEST['catid'],'id');

$menuTitle = s7dPayments::getCategory($parent,'title');
$menuAlias = s7dPayments::getCategory($parent,'alias');
$catTitle = s7dPayments::getCategory($caId,'title');
$catAlias = s7dPayments::getCategory($caId,'alias');
$menuLink = $app->getMenu()->getActive()->route;

if(isset($_GET['courseId'])):
$itemTitle = s7dPayments::getItem($_GET['courseId'],'title');
endif;

/*********************/

$pUrl = explode($mAlias,JUri::current());
endif;
$baseUrl = str_replace("/","",$pUrl[1]);


$url = JRoute::_('index.php?option=com_s7dpayments');

spl_autoload_register(function ($newclass) {
    @include(JPATH_SITE."/components/com_s7dpayments/payments/controllers/".$newclass.".php");
});

//Instanciando o methodo courses
$courses = s7dPayments::getCourses();


$idCode = uniqid();
$_SESSION['idVideo'] = $_GET['idVideo'];

if(isset($_SESSION['idVideo']) and $_SESSION['idVideo'] == $_GET['idCode']):
	$urlVideo = 'aula_01_exemplo.mp4';
endif;

$user = JFactory::getUser();
$userid = $user->id;

$_SESSION['user'] = $user->id;

//Executando carrinho
paymentsCart::executCart($userid);

//Sessão do carrinho
$cartid = $_SESSION['cartid'];


// Get the document object.
$doc = JFactory::getDocument();
$idParent =	JFactory::getApplication()->getMenu()->getActive()->parent_id;
$title = JFactory::getApplication()->getMenu()->getItem($idParent)->title;

$content ="
jQuery(function($){
	$( document).on(\"change\",\".lVideos input[type=radio]\",function(){
		$('.lPriceV').html($(this).data('price'));
	});	
});

function goBack() {
  window.history.back();
}

jQuery(function($){
	$( document ).ready(function(){
		$('#sp-main-body').before('<div class=\"dheader\"><div class=\"container\"><h3>".$title."</h3></div></div>');
	});
	
	//Validando as categorias dos cursos completos.
	var inPackage = $('#dpFormStore input[name=\"ipackage\"]');

	$( document ).on('submit','#dpFormStore',function(){
		if(inPackage.length > 0)
		{
			var dpCheck = $('#dpFormStore input[name=\"ipackage\"]:checked').length;
			
			if(dpCheck == 1)
			{
				return true;
			}else{
				alert('Você precisa selecionar uma categoria para continuar');
				return false;
			}
		}
	});

	//end
	
});

";

$doc->addScriptDeclaration($content);

require(JPATH_SITE.'/components/com_s7dpayments/tmpl/default.php');

?>
