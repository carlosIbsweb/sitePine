<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Visitacao_grupo
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2020 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Controller\BaseController;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Visitacao_grupo', JPATH_COMPONENT);
JLoader::register('Visitacao_grupoController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = BaseController::getInstance('Visitacao_grupo');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
