<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Visitacao_grupo
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
if (!Factory::getUser()->authorise('core.manage', 'com_visitacao_grupo'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Visitacao_grupo', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('Visitacao_grupoHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'visitacao_grupo.php');

$controller = BaseController::getInstance('Visitacao_grupo');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
