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

use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Categories\Categories;

/**
 * Class Pine_vacation_funRouter
 *
 */
class Pine_vacation_funRouter extends RouterView
{
	private $noIDs;
	public function __construct($app = null, $menu = null)
	{
		$params = Factory::getApplication()->getParams('com_pine_vacation_fun');
		$this->noIDs = (bool) $params->get('sef_ids');
		
		

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));

		if ($params->get('sef_advanced', 0))
		{
			$this->attachRule(new StandardRules($this));
			$this->attachRule(new NomenuRules($this));
		}
		else
		{
			JLoader::register('Pine_vacation_funRulesLegacy', __DIR__ . '/helpers/legacyrouter.php');
			JLoader::register('Pine_vacation_funHelpersPine_vacation_fun', __DIR__ . '/helpers/pine_vacation_fun.php');
			$this->attachRule(new Pine_vacation_funRulesLegacy($this));
		}
	}


	

	
}
