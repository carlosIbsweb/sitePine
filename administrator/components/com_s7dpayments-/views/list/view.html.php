<?php
/**
 * @version     1.0.0
 * @package     com_s7dpayments
 * @copyright   Copyright (C) 2016. Todos os direitos reservados.
 * @license     GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 * @author      Carlos <carlosnaluta@gmail.com> - http://site7dias.com.br
 */

// No direct access
defined('_JEXEC') or die;


jimport('joomla.application.component.view');

/**
 * View class for a list of S7dpayments.
 *
 * @since  1.6
 */
class S7dpaymentsViewList extends JViewLegacy
{
    protected $items;

    protected $pagination;

    protected $state;

    /**
     * Display the view
     *
     * @param   string  $tpl  Template name
     *
     * @return void
     *
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new Exception(implode("\n", $errors));
        }

        S7dpaymentsHelper::addSubmenu('payments');

        $this->addToolbar();

        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     *
     * @since    1.6
     */
    protected function addToolbar()
    {
        require_once JPATH_COMPONENT . '/helpers/s7dpayments.php';

        $state = $this->get('State');
        $canDo = S7dpaymentsHelper::getActions($state->get('filter.category_id'));

        JToolBarHelper::title(JText::_('COM_S7DPAYMENTS_TITLE_PAYMENTS'), 'payments.png');

        // Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/payment';

        if (file_exists($formPath))
        {
            if ($canDo->get('core.create'))
            {
                JToolBarHelper::addNew('payment.add', 'JTOOLBAR_NEW');
                JToolbarHelper::custom('payments.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
            }

            if ($canDo->get('core.edit') && isset($this->items[0]))
            {
                JToolBarHelper::editList('payment.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state'))
        {
            if (isset($this->items[0]->state))
            {
                JToolBarHelper::divider();
                JToolBarHelper::custom('payments.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('payments.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            }
            elseif (isset($this->items[0]))
            {
                // If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'payments.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state))
            {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('payments.archive', 'JTOOLBAR_ARCHIVE');
            }

            if (isset($this->items[0]->checked_out))
            {
                JToolBarHelper::custom('payments.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        // Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state))
        {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
            {
                JToolBarHelper::deleteList('', 'payments.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            }
            elseif ($canDo->get('core.edit.state'))
            {
                JToolBarHelper::trash('payments.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::preferences('com_s7dpayments');
        }

        // Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_s7dpayments&view=payments');

        $this->extra_sidebar = '';
        JHtmlSidebar::addFilter(

            JText::_('JOPTION_SELECT_PUBLISHED'),

            'filter_published',

            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

        );
    }

    /**
     * Method to order fields 
     *
     * @return void 
     */
    protected function getSortFields()
    {
        return array(
            'a.`id`' => JText::_('JGRID_HEADING_ID'),
            'a.`name`' => JText::_('COM_S7DPAYMENTS_PAYMENTS_TITLE'),
            'a.`state`' => JText::_('JSTATUS'),
            'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
            'a.`created_by`' => JText::_('COM_S7DPAYMENTS_PAYMENTS_CREATED_BY'),
            'a.`date`' => JText::_('COM_S7DPAYMENTS_PAYMENTS_DATE'),
        );
    }

    //Pegar Status
    public static function getStatus($statusId)
    {
        //Inserindo os dados do usúario;
        $db =& JFactory::getDBO();
        $cleanStatus = $statusId;

        $statusId = !empty($statusId) ? $db->quote($statusId) : false;

        $text = '';
        
        if($statusId)
        {
            //Buscando Dados existentes
            $db->setQuery('SELECT #__s7dpayments_status.status FROM #__s7dpayments_status WHERE statusId = '.$statusId);
            $result = $db->loadResult();

            switch ($cleanStatus) {
                case '1':
                    $text = '<span class="ap-status ap-aguarde">'.$result.'</span>';
                    break;
                case '3':
                    $text = '<span class="ap-status ap-success">'.$result.'</span>';
                    break;
                case '7':
                     $text = '<span class="ap-status ap-error">'.$result.'</span>';
                    break;
                default:
                    $text = $result;
                    break;
            }


        }
        return $text;
        
        
    }
}
