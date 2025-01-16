<?php

/**
 * @version    CVS: 2.0.0
 * @package    Com_S7dgallery
 * @author     carlos <carlosnaluta@gmail.com>
 * @copyright  2018 carlos
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of S7dgallery.
 *
 * @since  1.6
 */
class S7dgalleryViewAlbums extends JViewLegacy
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

		S7dgalleryHelper::addSubmenu('albums');

		// Only set the toolbar if not modal
	  if ($this->getLayout() !== 'modal') {
		  $this->addToolBar();
	  }

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
		$canDo = S7dgalleryHelper::getActions();

		JToolBarHelper::title(JText::_('COM_S7DGALLERY_TITLE_ALBUMS'), 'albums.png');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/album';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::addNew('album.add', 'JTOOLBAR_NEW');

				if (isset($this->items[0]))
				{
					JToolbarHelper::custom('albums.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
				}
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				JToolBarHelper::editList('album.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('albums.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('albums.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'albums.delete', 'JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('albums.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('albums.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'albums.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
				echo 'eba tudo bem';
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('albums.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_s7dgallery');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_s7dgallery&view=albums');
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
			'a.`title`' => JText::_('COM_S7DGALLERY_ALBUMS_TITLE'),
			'a.`catid`' => JText::_('COM_S7DGALLERY_ALBUMS_CATID'),
			'a.`state`' => JText::_('JSTATUS'),
			'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
			'a.`data`' => JText::_('COM_S7DGALLERY_ALBUMS_DATA'),
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

    public function getCategoryFilter($selectId = null){
    	// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('id', 'title')));
		$query->from($db->quoteName('#__s7dgallery_categories'));
		$query->where($db->quoteName('state') . ' = '. $db->quote(1));
		$query->order('ordering ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		$options = [];
		foreach($results as $items){
			$selected = $selectId == $items->id  ? 'selected' : null;
			$options[] = '<option '.$selected. ' value="'.$items->id.'">'.$items->title.'</option>';
		}

		return implode('',$options);
    }

    public function getCategoryTitle($catId){
    	// Initialiase variables.
    	$db    = JFactory::getDbo();
    	$query = $db->getQuery(true);
    	
    	// Create the base select statement.
    	$query->select('*')
    		->from($db->quoteName('#__categories'))
    		->where($db->quoteName('id') . ' = ' . $db->quote($catId));
    	
    	// Set the query and load the result.
    	$db->setQuery($query);
    	
    	try
    	{
    		$result = $db->loadObjectList();
    	}
    	catch (RuntimeException $e)
    	{
    		JError::raiseWarning(500, $e->getMessage());
    	}

    	foreach($result as $item)
    	{
    		echo $item->title;
    	}
    }
}
