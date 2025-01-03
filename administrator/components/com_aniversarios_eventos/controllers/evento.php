<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Aniversarios_eventos
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2018 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Evento controller class.
 *
 * @since  1.6
 */
class Aniversarios_eventosControllerEvento extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'eventos';
		parent::__construct();
	}
}
