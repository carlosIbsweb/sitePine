<<?php
        
/**
 * @version     1.0.0
 * @package     com_s7dpayments
 * @copyright   Copyright (C) 2016. Todos os direitos reservados.
 * @license     GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 * @author      Carlos <carlosnaluta@gmail.com> - http://site7dias.com.br
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class S7dpaymentsControllerPayments extends JControllerLegacy {

    /**
     * Method to display a view.
     *
     * @param   boolean         $cachable  If true, the view output will be cached
     * @param   array           $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  JController     This object to support chaining.
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = false) {
        require_once JPATH_COMPONENT . '/helpers/s7dpayments.php';

        $view = JFactory::getApplication()->input->getCmd('view', 'payments');
        JFactory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }

    public static function pakage()
	{
		echo 'moço';
	}

}

