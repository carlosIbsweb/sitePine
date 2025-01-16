<?php

/**
 * @package     S7D LV - Site 7 Dias Loja Virtual
 * @copyright   Copyright (C) 2006 - 2018 Site 7 Dias
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 * @author      Carlos(Site 7 Dias) - http://site7dias.com.br
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
// Load route helper.
jimport('joomla.form.helper');
/**
 * Methods supporting a list of S7dlv records.
 *
 * @since  1.6
 */
class S7dgalleryModelS7dimages extends JModelList
{
	public function getImages($id,$data)
	{
		// Build the query for the table list.
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT '.$data
			. ' FROM #__s7dgallery_albums'
			. ' WHERE id = ' . (int) $id
		);
		
		$result = $db->loadResult();

		return $result;
	}

	public function delete_images($id,$images)
	{
		// Initialiase variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Create the base update statement.
		$query->update($db->quoteName('#__s7dgallery_albums'))
			->set($db->quoteName('images') . ' = ' . $db->quote($images))
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

	public function upImage($id,$json = array(),$data)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		// Fields to update.
		$fields = array(
		    $db->quoteName($data) . ' = ' . $db->quote($json)
		);

		// Conditions for which records should be updated.
		$conditions = array(
		    $db->quoteName('id') . ' = '.$id
		);

		$query->update($db->quoteName('#__s7dgallery_albums'))->set($fields)->where($conditions);

		$db->setQuery($query);

		$result = $db->execute();

		return $result;

	}
}

