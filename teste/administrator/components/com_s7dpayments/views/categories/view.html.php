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
class S7dpaymentsViewCategories extends JViewLegacy
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

        S7dpaymentsHelper::addSubmenu('categories');

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

        JToolBarHelper::title(JText::_('COM_S7DPAYMENTS_TITLE_CATEGORIES'), 'categories.png');

        // Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/category';

        if (file_exists($formPath))
        {
            if ($canDo->get('core.create'))
            {
                JToolBarHelper::addNew('category.add', 'JTOOLBAR_NEW');
                JToolbarHelper::custom('categories.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
            }

            if ($canDo->get('core.edit') && isset($this->items[0]))
            {
                JToolBarHelper::editList('category.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state'))
        {
            if (isset($this->items[0]->state))
            {
                JToolBarHelper::divider();
                JToolBarHelper::custom('categories.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('categories.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            }
            elseif (isset($this->items[0]))
            {
                // If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'categories.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state))
            {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('categories.archive', 'JTOOLBAR_ARCHIVE');
            }

            if (isset($this->items[0]->checked_out))
            {
                JToolBarHelper::custom('categories.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        // Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state))
        {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
            {
                JToolBarHelper::deleteList('', 'categories.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            }
            elseif ($canDo->get('core.edit.state'))
            {
                JToolBarHelper::trash('categories.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::preferences('com_s7dpayments');
        }

        // Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_s7dpayments&view=categories');

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
            'a.`title`' => JText::_('COM_S7DPAYMENTS_CATEGORIES_TITLE'),
            'a.`state`' => JText::_('JSTATUS'),
            'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
            'a.`created_by`' => JText::_('COM_S7DPAYMENTS_CATEGORIES_CREATED_BY'),
            'a.`date`' => JText::_('COM_S7DPAYMENTS_CATEGORIES_DATE'),
        );
    }
}
