<?php
/**
 * @version    2.0
 * @package    Com_S7dlv
 * @author     carlos <carlos@ibsweb.com.br>
 * @copyright  2018 carlos
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

define( '_JEXEC', 1 );
define( 'DS', '/' );
define( 'JPATH_BASE', implode("",explode("components/com_s7dgallery/image",dirname(__FILE__))));
   
require_once ( JPATH_BASE .DS. 'includes' .DS. 'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

$mainframe = JFactory::getApplication('site');
$mainframe->initialise();
jimport( 'joomla.user.user');
jimport( 'joomla.session.session');
jimport( 'joomla.user.authentication');
jimport( 'joomla.application.module.helper' );
// import Joomla modelitem library
require_once ( JPATH_BASE .DS. 'components' .DS. 'com_s7dgallery'.DS.'controller.php' );
$base = implode(explode("components/com_s7dgallery/image",dirname($_SERVER['PHP_SELF'])));

// Load the modal behavior script.
JHtml::_('behavior.framework', true);

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Get the current user object.
$user = JFactory::getUser();
$userId = $user->id;


$limit      = 6;
$perPage    = 4;


// Get the document object.
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base(true).'/media/com_s7dgallery/css/s7dcolumns.css');
$doc->addStyleSheet(JUri::base(true).'/media/com_s7dgallery/css/line-awesome.css');
$doc->addStyleSheet(JUri::base(true).'/components/com_s7dgallery/assets/css/style.css');
$doc->addStyleSheet(JUri::base(true).'/components/com_s7dgallery/assets/css/magnific-popup.css');
$doc->addStyleSheet(JUri::base(true).'/components/com_s7dgallery/assets/css/justifiedGallery.css');
$doc->addScript(JUri::base(true).'/components/com_s7dgallery/assets/js/jquery.magnific-popup.js');
$doc->addScript(JUri::base(true).'/components/com_s7dgallery/assets/js/s7dGallery.js');
$doc->addScript(JUri::base(true).'/components/com_s7dgallery/assets/js/justifiedGallery.js');


function getItems($id)
{
	// Initialiase variables.
	$db    = JFactory::getDbo();
	$query = $db->getQuery(true);
	
	// Create the base select statement.
	$query->select('*')
		->from($db->quoteName('#__s7dgallery_albums'))
		->where($db->quoteName('id') . ' = ' . $db->quote($id))
		->order($db->quoteName('ordering') . ' ASC');
	
	// Set the query and load the result.
	$db->setQuery($query);
	
	try
	{
		$result = $db->loadObjectList();
	}
	catch (RuntimeException $e)
	{
		JError::raiseWarning(500, $e->getMessage());
	}

	return $result;
}



$folder     = JPATH_BASE.'/images/s7dgallery/gal-'.$_GET['itemId'].'/';

$error = false;
switch ($_GET['path']) {

	case 'large':
		$path = $folder.'large/';
		break;
	case 'medium':
		$path = $folder.'medium/';
		break;
	case 'small':
		$path = $folder.'small/';
		break;
	case 'thumbs':
		$path = $folder.'thumbs/';
		break;
	
	default:
		$error = true;
		break;
}



if($error != true){
foreach(getItems($_GET['itemId']) as $items)
{
	foreach(json_decode($items->images) as $img)
	{

		if($img->id == $_GET['imgId'])
		{
			$access = $img->access != 1 ? true : (!empty($userId) ? true : false);
			if($access){
				// abre o arquivo em modo binário
			$imagem = $path.$img->image;
			if(file_exists($imagem)){
				$info = getimagesize($imagem);
				//Tempo em cache 3600
				header('Cache-Control: public, must-revalidate, max-age=3600');
				header("Content-type: {$info['mime']}"); 
				readfile($imagem); 
				exit; 
			}
			else{
				echo htmlentities('Imagem não existe');
			}
		}else{
			echo htmlentities('não permitido');
		}
			
			
		}
	}
}
}else{
	echo htmlentities('Pasta não existe');
}

?>



  