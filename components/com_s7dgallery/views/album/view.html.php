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
 * View to edit
 *
 * @since  1.6
 */
class S7dgalleryViewAlbum extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;

	protected $params;

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
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		$this->state  = $this->get('State');
		$this->item   = $this->get('Item');
		$this->params = $app->getParams('com_s7dgallery');

		if (!empty($this->item))
		{
			
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		

		if ($this->_layout == 'edit')
		{
			$authorised = $user->authorise('core.create', 'com_s7dgallery');

			if ($authorised !== true)
			{
				throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function _prepareDocument()
	{
		$app   = JFactory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// We need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_S7DGALLERY_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}

	/***********
	 *Chamar modulo
	************/

	public static function getModule($posi){
		$document = JFactory::getDocument();
    	$renderer = $document->loadRenderer('modules');
    	$position = 'sg-position-'.$posi;
    	$options  = array('style' => 'raw');
    	echo $renderer->render($position, $options, null); 
	}
	public static function diffDates($date1)
	{
		$datatime1 = new DateTime($date1);
		$datatime2 = new DateTime(date('Y/m/d H:i:s'));
		
		$data1  = $datatime1->format('Y-m-d H:i:s');
		$data2  = $datatime2->format('Y-m-d H:i:s');

		//Formato da data original.
		$data = $datatime1->format('d/m/Y');

		/*$diff = $datatime1->diff($datatime2);
		$horas = $diff->h + ($diff->days * 24);

		$minutos = $diff->format('%i') >= 1 ? $diff->format('%i') : null;
		
		$tdias = $horas >= 48 ? ' dias' : ' dia';
		$thoras = $diff->format('%h') >= 2 ? ' horas' : ' hora';
		$tminutos = $diff->format('%i') >= 2 ? ' minutos' : ($diff->format('%i') >= 1 ? ' minuto' : ' alguns segundos');
		
		$hormin = $diff->format('%h') == 0 ? $minutos . $tminutos : $diff->format('%h') . $thoras;
		$diahor = $horas >= 24 ? (int)($horas /24) . $tdias :  $hormin;

		$anomes = $horas >= 168 ?  $data : 'Há ' .$diahor;*/

		return $data;
	}    

	public function acessos($id){
    	// Initialiase variables.
    	$db    = JFactory::getDbo();
    	$query = $db->getQuery(true);
    	$acessos = self::selectA($id) + 1;
    	// Create the base update statement.
    	$query->update($db->quoteName('#__s7dgallery_albums'))
    		->set($db->quoteName('access') . ' = ' . $db->quote($acessos))
    		->where($db->quoteName('id') . ' = ' . $db->quote($id));
    	
    	// Set the query and execute the update.
    	$db->setQuery($query);
    	
    	try
    	{
    		$db->execute();
    	}
    	catch (RuntimeException $e)
    	{
    		JError::raiseWarning(500, $e->getMessage());
    	}
    }

    public function selectA($id) {
    	// Build the query for the table list.
    	$db = JFactory::getDbo();
    	$db->setQuery(
    		'SELECT access'
    		. ' FROM #__s7dgallery_albums'
    		. ' WHERE id = ' . (int) $id
    	);
    	
    	$result = $db->loadResult();
    	return $result;
    }
}
