<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_S7dpayments
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2021 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * School controller class.
 *
 * @since  1.6
 */
class S7dpaymentsControllerSchool extends \Joomla\CMS\MVC\Controller\FormController
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'schools';
		parent::__construct();
	}
}
