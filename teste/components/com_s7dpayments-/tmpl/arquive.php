<?php
/**
* @version 1.0
* @package S7DPayments
* @copyright (C) 2006-2015 www.site7dias.com.br
* @license S7D, http://www.s7dias.com.br
*/

define( '_JEXEC', 1 );
define( 'DS', '/' );

define( 'JPATH_BASE', $_SERVER['DOCUMENT_ROOT']);
require_once ( JPATH_BASE .DS. 'includes' .DS. 'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once ( JPATH_BASE .DS. 'components' .DS. 'com_s7dpayments'.DS.'controller.php' );
$mainframe = JFactory::getApplication('site');
$mainframe->initialise();
jimport( 'joomla.user.user');
jimport( 'joomla.session.session');
jimport( 'joomla.user.authentication');

//Dados do usuário logado
$user = JFactory::getUser(); 
$jid = $user->id; 
$jname = $user->name; 
$jguest = $user->guest;

$input = explode("/",JUri::root());

$key = array_search('tmpl', $input);
if($key!=false){
    unset($input[$key]);
}

//url raid do meu arquivo
$url = implode("/",$input);

//Instanciando o methodo courses
$courses = s7dPayments::getCourses();

foreach(s7dPayments::getItens($_GET['courseId']) as $items):
	foreach($courses->list as $k=> $courseid):
		if($_GET['courseId'] == $courseid && explode(":",$k)[1] != 'bl'):
			$vdarray = $courseid;
		endif;
	endforeach;
endforeach;

/**********
 *Bloqueio dos cursos Package.
**********/

foreach(s7dPayments::getCoursesPackage() as $item)
{
	foreach($courses->list as $k=> $courseid)
	{
		if($item->id == $courseid && explode(":",$k)[1] != 'bl')
		{
			$cpkArr = explode(",",$item->package);

			foreach($cpkArr as $lbk)
			{
				if($lbk == $_GET['courseId'])
				{
					$vdarray = $lbk;
				}
			}

		}
		
	}
}	

if(!empty($jid) && !empty($vdarray)):
header('Content-type: application/pdf');

//pasta onde ficará os arquivos em pdf protegidos
$image = $url.'assets/files_dpFsdf05b55d133c4162c4953fc97eebb093/course'.$_GET['courseId'].'/'.$_GET['file'].'.pdf';

readfile($image);

else:
	echo '<div class="alert alert-danger">';
  	echo '<strong>Acesso negado!</strong> Você precisa de permissão para visualizar essa página.';
  	echo '</div>';
endif;

?>
<embed></embed>