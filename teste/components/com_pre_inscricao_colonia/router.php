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

use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Categories\Categories;

/**
 * Class Pre_inscricao_coloniaRouter
 *
 */
class Pre_inscricao_coloniaRouter extends RouterView
{
	private $noIDs;
	public function __construct($app = null, $menu = null)
	{
		$params = JComponentHelper::getComponent('com_pre_inscricao_colonia')->params;
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
			JLoader::register('Pre_inscricao_coloniaRulesLegacy', __DIR__ . '/helpers/legacyrouter.php');
			JLoader::register('Pre_inscricao_coloniaHelpersPre_inscricao_colonia', __DIR__ . '/helpers/pre_inscricao_colonia.php');
			$this->attachRule(new Pre_inscricao_coloniaRulesLegacy($this));
		}
	}


	

	
}
