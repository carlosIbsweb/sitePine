<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Picnic
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2019 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Picnic', JPATH_COMPONENT);
JLoader::register('PicnicController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = JControllerLegacy::getInstance('Picnic');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
