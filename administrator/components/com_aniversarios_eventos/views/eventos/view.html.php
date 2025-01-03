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

jimport('joomla.application.component.view');

/**
 * View class for a list of Aniversarios_eventos.
 *
 * @since  1.6
 */
class Aniversarios_eventosViewEventos extends JViewLegacy
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
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		Aniversarios_eventosHelper::addSubmenu('eventos');

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
		$state = $this->get('State');
		$canDo = Aniversarios_eventosHelper::getActions();

		JToolBarHelper::title(JText::_('COM_ANIVERSARIOS_EVENTOS_TITLE_EVENTOS'), 'eventos.png');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/evento';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::addNew('evento.add', 'JTOOLBAR_NEW');

				if (isset($this->items[0]))
				{
					JToolbarHelper::custom('eventos.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
				}
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				JToolBarHelper::editList('evento.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('eventos.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('eventos.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'eventos.delete', 'JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('eventos.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('eventos.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'eventos.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('eventos.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_aniversarios_eventos');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_aniversarios_eventos&view=eventos');
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
			'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
			'a.`state`' => JText::_('JSTATUS'),
			'a.`nomeresp`' => JText::_('COM_ANIVERSARIOS_EVENTOS_EVENTOS_NOMERESP'),
			'a.`telefone`' => JText::_('COM_ANIVERSARIOS_EVENTOS_EVENTOS_TELEFONE'),
			'a.`email`' => JText::_('COM_ANIVERSARIOS_EVENTOS_EVENTOS_EMAIL'),
			'a.`nomecria`' => JText::_('COM_ANIVERSARIOS_EVENTOS_EVENTOS_NOMECRIA'),
			'a.`idade`' => JText::_('COM_ANIVERSARIOS_EVENTOS_EVENTOS_IDADE'),
			'a.`escola`' => JText::_('COM_ANIVERSARIOS_EVENTOS_EVENTOS_ESCOLA'),
			'a.`diafesta`' => JText::_('COM_ANIVERSARIOS_EVENTOS_EVENTOS_DIAFESTA'),
			'a.`numerocria`' => JText::_('COM_ANIVERSARIOS_EVENTOS_EVENTOS_NUMEROCRIA'),
			'a.`numeroadult`' => JText::_('COM_ANIVERSARIOS_EVENTOS_EVENTOS_NUMEROADULT'),
			'a.`opcoesfestas`' => JText::_('Opções de Festas'),
			'a.`tema`' => JText::_('COM_ANIVERSARIOS_EVENTOS_EVENTOS_TEMA'),
			'a.`checkbox`' => JText::_('COM_ANIVERSARIOS_EVENTOS_EVENTOS_CHECKBOX'),
		);
	}

    /**
     * Check if state is set
     *
     * @param   mixed  $state  State
     *
     * @return bool
     */
    public function getState($state)
    {
        return isset($this->state->{$state}) ? $this->state->{$state} : false;
    }
}
