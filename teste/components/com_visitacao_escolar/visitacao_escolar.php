<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Visitacao_escolar
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2018 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Visitacao_escolar', JPATH_COMPONENT);
JLoader::register('Visitacao_escolarController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = JControllerLegacy::getInstance('Visitacao_escolar');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
