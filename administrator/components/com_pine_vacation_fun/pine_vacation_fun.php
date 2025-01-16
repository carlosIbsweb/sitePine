<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Pine_vacation_fun
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
if (!Factory::getUser()->authorise('core.manage', 'com_pine_vacation_fun'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Pine_vacation_fun', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('Pine_vacation_funHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'pine_vacation_fun.php');

$controller = BaseController::getInstance('Pine_vacation_fun');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
