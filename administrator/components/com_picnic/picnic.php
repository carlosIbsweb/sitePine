<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Picnic
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2019 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_picnic'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Picnic', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('PicnicHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'picnic.php');

$controller = JControllerLegacy::getInstance('Picnic');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
