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
		$app = JFactory::getApplication();

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->params = $app->getParams('com_s7dgallery');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
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
		// we need to get it from the menu item itself
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

	public static function lText($text,$limit) {
		
		$mText = strip_tags($text);
		$count = strlen($mText);
		$TextClean = substr($mText, 0, strrpos(substr($mText, 0, $limit), ' '));
		
		$output = $count > $limit && !empty($limit) ? $TextClean.'...' : $mText;
		
		return $output;
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
}
