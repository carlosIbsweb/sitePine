<?php
/**
 * @version    CVS: 2.0.0
 * @package    Com_S7dgallery
 * @author     carlos <carlosnaluta@gmail.com>
 * @copyright  2018 carlos
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_s7dgallery'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('S7dgallery', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('S7dgalleryHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 's7dgallery.php');

$controller = JControllerLegacy::getInstance('S7dgallery');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
