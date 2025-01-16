<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Aniversarios_e_eventos
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2020 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Controller\BaseController;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Aniversarios_e_eventos', JPATH_COMPONENT);
JLoader::register('Aniversarios_e_eventosController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = BaseController::getInstance('Aniversarios_e_eventos');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
