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
define( 'JPATH_BASE', implode("",explode("components/com_s7dgallery/views/album/tmpl",dirname(__FILE__))));
   
require_once ( JPATH_BASE .DS. 'includes' .DS. 'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

$mainframe = JFactory::getApplication('site');
$mainframe->initialise();
jimport( 'joomla.user.user');
jimport( 'joomla.session.session');
jimport( 'joomla.user.authentication');
jimport( 'joomla.application.module.helper' );
$base = implode(explode("components/com_s7dgallery/views/album/tmpl",dirname($_SERVER['PHP_SELF'])));

// Load the modal behavior script.
JHtml::_('behavior.framework', true);

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Get the current user object.
$user = JFactory::getUser();
$userId = $user->id;

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
//$productId  = $this->item->id;


// abre o arquivo em modo binário
$imagem = JPATH_BASE.'images/s7dgallery/gal-1/large/pexels-photo.jpg';
$info = getimagesize($imagem); 
header("Content-type: {$info['mime']}"); 
readfile($imagem); 
exit; 

?>



  