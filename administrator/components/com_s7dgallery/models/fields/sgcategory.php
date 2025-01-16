<?php 
/**
 * Sgcategory Field class for the .
 *
 * @package     
 * @subpackage  com_
 * @author       <>
 * @since       
 */
class JFormFieldSgcategory extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var     string
	 * @since   
	 */
	protected $type = 'Sgcategory';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   
	 */
	protected function getInput()
	{
		return self::getItems($_REQUEST['id']);
	}

	protected function getItems($id){
		// Initialiase variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Create the base select statement.
		$query->select('*')
			->from($db->quoteName('#__s7dgallery_categories'))
			->where($db->quoteName('state') . ' = ' . $db->quote('1'))
			->where($db->quoteName('parent_id') . ' = ' . $db->quote(0))
			->where($db->quoteName('id') . ' != ' . $db->quote($id))
			->order($db->quoteName('id') . ' ASC');
		
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

		$html = [];
		$html[] = '<select name="'.$this->name.'">'; 
		$html[] = '<option value="0">-Sem Pai-'.'</option>';
		foreach($result as $k=> $item){

				$html[] = '<option '.$selected.' value="'.$item->id.'">'.$item->title.'</option>';
				$html[] = self::getSubs($item->id,$k,$id,$this->value);
			
		}
		$html[] = '</select>';

		return implode('',$html);
	}


	public static function getSubs($id,$nivel,$itemId,$vId){
		// Initialiase variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Create the base select statement.
		$query->select('*')
			->from($db->quoteName('#__s7dgallery_categories'))
			->where($db->quoteName('state') . ' = ' . $db->quote('1'))
			->where($db->quoteName('parent_id') . ' = ' . $db->quote($id))
			->where($db->quoteName('id') . ' != ' . $db->quote($itemId))
			->order($db->quoteName('id') . ' ASC');
		
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

		$html = [];
		$nivel++;
		foreach($result as $k=> $items)
		{
			$selected = $vId == $items->id ? ' selected ' : null;
			$html[] = '<option '.$selected. 'value="'.$items->id.'">'.str_repeat('- ',$nivel).$items->title.'</option>';
			$html[] = self::getSubs($items->id,$nivel,$itemId,$vId);
		}

		return implode('',$html);

	}
}




