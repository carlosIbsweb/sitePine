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
class S7dpaymentsViewCourse extends JViewLegacy
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

        JToolBarHelper::title('S7D Payments: '.JText::_('COM_S7DPAYMENTS_PAGE_'.($checkedOut ? 'VIEW_COURSE' : ($isNew ? 'ADD_COURSE' : 'EDIT_COURSE'))), 'article-add.png');

        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
        {
            JToolBarHelper::apply('course.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('course.save', 'JTOOLBAR_SAVE');
        }

        if (!$checkedOut && ($canDo->get('core.create')))
        {
            JToolBarHelper::custom('course.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        }

        // If an existing item, can save to a copy.
        if (!$isNew && $canDo->get('core.create'))
        {
            JToolBarHelper::custom('course.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        }

        if (empty($this->item->id))
        {
            JToolBarHelper::cancel('course.cancel', 'JTOOLBAR_CANCEL');
        }
        else
        {
            JToolBarHelper::cancel('course.cancel', 'JTOOLBAR_CLOSE');
        }
    }

   public function archives()
    {
        jimport( 'joomla.filesystem.folder' );
        jimport('joomla.filesystem.file');
        $input = JFactory::getApplication()->input;
        $files = $input->files->get('file');
        
        foreach($files as  $file):
            $filename = JFile::makeSafe($file['name']);
            $extensao = strrchr($filename, '.');
            // Converte a extensao para mimusculo
            $extensao = strtolower($extensao); 
            if(strstr('.jpg;.jpeg;.gif;.png', $extensao)):
                $src = $file['tmp_name'];
                $dest = JPATH_SITE . "/components/com_s7dpayments/assets/files_dpFsdf05b55d133c4162c4953fc97eebb093/". $filename;
            endif;
            
            if(!empty($input->get('file'))):
                JFile::upload($src, $dest);
            endif;

        endforeach;
    }
}
