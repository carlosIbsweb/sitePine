<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Aniversarios_e_eventos
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2020 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Cadastro controller class.
 *
 * @since  1.6
 */
class Aniversarios_e_eventosControllerCadastro extends \Joomla\CMS\MVC\Controller\FormController
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'cadastros';
		parent::__construct();
	}
}
