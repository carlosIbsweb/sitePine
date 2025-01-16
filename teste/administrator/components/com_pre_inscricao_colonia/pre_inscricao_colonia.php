<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Pre_inscricao_colonia
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
if (!Factory::getUser()->authorise('core.manage', 'com_pre_inscricao_colonia'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Pre_inscricao_colonia', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('Pre_inscricao_coloniaHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'pre_inscricao_colonia.php');

$controller = BaseController::getInstance('Pre_inscricao_colonia');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
