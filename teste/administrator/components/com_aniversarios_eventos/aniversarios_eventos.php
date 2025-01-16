<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Aniversarios_eventos
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2018 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_aniversarios_eventos'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Aniversarios_eventos', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('Aniversarios_eventosHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'aniversarios_eventos.php');

$controller = JControllerLegacy::getInstance('Aniversarios_eventos');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
