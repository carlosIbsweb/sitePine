<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Inscricao_pre_matricula
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2020 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Controller\BaseController;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Inscricao_pre_matricula', JPATH_COMPONENT);
JLoader::register('Inscricao_pre_matriculaController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = BaseController::getInstance('Inscricao_pre_matricula');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
