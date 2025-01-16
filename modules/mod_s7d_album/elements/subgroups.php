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
class JFormFieldSubgroups extends \Joomla\CMS\Form\FormField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'subgroups';

	//Tabela dos items
	protected $table_items;

	//Tabela do grupo ou categorias.
	protected $table_group;

	//Campo para exibição dos items
	protected $key_field;

	//Campo para o value dos items
	protected $value_field;

	//Ordenação dos items
	protected $order_items;

	//Ordenação dos grupos ou categorias.
	protected $order_group;

	//Campo dos items que tem como parent o id da tabela dos grupos ou categoria.
	protected $parent_id;

	//Campo para exibição dos grupos ou categoria.
	protected $group_field;

	//Condição do grupo ou categoria.
	protected $where_group;

	//Condição dos items.
	protected $where_items;

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 *
	 * @since    1.6
	 */

	protected function setDados()
	{
		$this->table_items 	= $this->element['table_items'];
		$this->table_group 	= $this->element['table_group'];
		$this->key_field 	= $this->element['key_field'];
		$this->value_field 	= $this->element['value_field'];
		$this->order_group 	= $this->element['order_group'];
		$this->order_items 	= $this->element['order_items'];
		$this->parent_id 	= $this->element['parent_id'];
		$this->group_field 	= $this->element['group_field'];
		$this->where_items 	= $this->element['where_items'];
		$this->where_group 	= $this->element['where_group'];

		return true;
	}
	protected function getInput()
	{
		$this->setDados();
		return self::mostra();
	}

	protected function _Items()
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
		    ->from($db->quoteName((string) $this->element['table_items']));
		    if($this->order_items)
		    {
		    	$query->order($this->order_items ? $db->quoteName('id'). 'ASC' : $this->order_items);
		    }

		    if($this->where_items)
		    {
		    	$query->where($this->where_items);
		    }
		    

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}



	protected function _ItemsGroup()
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
		    ->from($db->quoteName((string) $this->table_group));

		    if($this->order_group){
		    	$query->order($this->order_group ? $db->quoteName('id'). 'ASC' : $this->order_group);
		    }

		    if($this->where_group)
		    {
		    	$query->where($this->where_group);
		    }

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();


		return $results;
	}


	protected function mostra()
	{
		$html = array();
		$html[] = '<select name="'.$this->name.'">';

		$html[] = '<option value="">Selecione</option>';
		
		foreach(self::_ItemsGroup() as $g)
		{
			$html[] = '<optgroup label="'.$g->{$this->group_field}.'">';
			foreach(self::_Items() as $sup)
			{
				if($g->id == $sup->{$this->parent_id})
				{
					$selected = $this->value == $sup->{$this->value_field} ? ' selected' : null;
					$html[] = '<option value="'.$sup->{$this->value_field}.'" '.$selected.'>'.$sup->{$this->key_field}.'</option>'."\n";
				}
				
			}
			  $html[] = '</optgroup>'; 
		}
		$html[] = '</select>';

		return implode('',$html);
	}
}
