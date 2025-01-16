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

use \Joomla\CMS\Factory;

/**
 * Class Inscricao_pre_matriculaController
 *
 * @since  1.6
 */
class Inscricao_pre_matriculaController extends \Joomla\CMS\MVC\Controller\BaseController
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   mixed    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return   JController This object to support chaining.
	 *
	 * @since    1.5
     * @throws Exception
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$view = Factory::getApplication()->input->getCmd('view', 'inscricoes');
		Factory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
}
