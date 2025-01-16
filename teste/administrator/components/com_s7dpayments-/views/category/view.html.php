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
 * View to edit
 *
 * @since  1.6
 */
class S7dpaymentsViewCategory extends JViewLegacy
{
    protected $state;

    protected $item;

    protected $form;

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
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     *
     * @throws Exception
     */
    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $user  = JFactory::getUser();
        $isNew = ($this->item->id == 0);

        if (isset($this->item->checked_out))
        {
            $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        }
        else
        {
            $checkedOut = false;
        }

        $canDo = S7dpaymentsHelper::getActions();

        JToolBarHelper::title(JText::_('COM_S7DPAYMENTS_PAGE_'.($checkedOut ? 'VIEW_CATEGORY' : ($isNew ? 'ADD_CATEGORY' : 'EDIT_CATEGORY'))), 'article-add.png');

        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
        {
            JToolBarHelper::apply('category.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('category.save', 'JTOOLBAR_SAVE');
        }

        if (!$checkedOut && ($canDo->get('core.create')))
        {
            JToolBarHelper::custom('category.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        }

        // If an existing item, can save to a copy.
        if (!$isNew && $canDo->get('core.create'))
        {
            JToolBarHelper::custom('category.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        }

        if (empty($this->item->id))
        {
            JToolBarHelper::cancel('category.cancel', 'JTOOLBAR_CANCEL');
        }
        else
        {
            JToolBarHelper::cancel('category.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
