<?php
/**
 * @version    CVS: 2.0.0
 * @package    Com_S7dgallery
 * @author     carlos <carlosnaluta@gmail.com>
 * @copyright  2018 carlos
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

defined('_JEXEC') or die;
// Include dependancies
jimport('joomla.application.component.controller');

//JLoader::registerPrefix('S7dgallery', JPATH_COMPONENT);


JLoader::register('S7dgalleryController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = JControllerLegacy::getInstance('S7dgallery');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
