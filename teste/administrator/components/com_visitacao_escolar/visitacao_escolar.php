<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Visitacao_escolar
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2018 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_visitacao_escolar'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Visitacao_escolar', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('Visitacao_escolarHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'visitacao_escolar.php');

$controller = JControllerLegacy::getInstance('Visitacao_escolar');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
