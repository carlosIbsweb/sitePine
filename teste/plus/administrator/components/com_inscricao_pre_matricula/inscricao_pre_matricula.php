<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Inscricao_pre_matricula
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2020 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\Controller\BaseController;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_inscricao_pre_matricula'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Inscricao_pre_matricula', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('Inscricao_pre_matriculaHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'inscricao_pre_matricula.php');

$controller = BaseController::getInstance('Inscricao_pre_matricula');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
