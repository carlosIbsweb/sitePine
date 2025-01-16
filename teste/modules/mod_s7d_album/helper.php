<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_feed
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_feed
 *
 * @since  1.5
 */
class ModSanisliderHelper
{
	public static function getImages(&$params) {

		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('id','images')));
		$query->from($db->quoteName('#__s7dgallery_albums'));
		$query->where($db->quoteName('id') . ' = '. $db->quote($params->get('said')));
		$query->order('ordering ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}
}
