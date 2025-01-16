<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Picnic_vespertino
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2019 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Picnicsvespertino controller class.
 *
 * @since  1.6
 */
class Picnic_vespertinoControllerPicnicsvespertino extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'picnicvespertino';
		parent::__construct();
	}
}
