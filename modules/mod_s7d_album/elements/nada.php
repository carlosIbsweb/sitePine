<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Aprendizes
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2019 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

use \Joomla\CMS\Factory;

/**
 * Supports an HTML select list of categories
 *
 * @since  1.6
 */
class JFormFieldNada extends \Joomla\CMS\Form\FormField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'nada';
	//protected $nada = $this->element->attributes()->name;


	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 *
	 * @since    1.6
	 */


	protected function getInput()
	{
			self::oxa();
	}

	public function oxa()
	{
		echo 'aqui mesmo'.$this->element['nada'];
	}
	public static function getSuperiores()
	{

		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
		$query
		    ->select(array('*'))
		    ->from($db->quoteName('#__superiorcursos'))
		    ->order($db->quoteName('title') . 'ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}



	public static function getSuperioresGroup()
	{

		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
		$query
		    ->select(array('*'))
		    ->from($db->quoteName('#__superiorcursos_groups'))
		    ->order($db->quoteName('id') . 'ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();


		return $results;
	}


	public static function mostra($name,$val)
	{
		$html = array();
		$html[] = '<select name="'.$name.'">';
		$html[] = '<option value="">Selecione</option>';
		$html[] = '<option value="Não possui">Não possui</option>';
		foreach(self::getSuperioresGroup() as $g)
		{
			$html[] = '<optgroup label="'.$g->title.'">';
			foreach(self::getSuperiores() as $sup)
			{
				if($g->id == $sup->grupo)
				{
					$selected = $val == $sup->title ? ' selected' : null;
					$html[] = '<option value="'.$sup->title.'" '.$selected.'>'.$sup->title.'</option>'."\n";
				}
				
			}
			  $html[] = '</optgroup>'; 
		}
		$html[] = '</select>';

		return implode('',$html);
	}
}
