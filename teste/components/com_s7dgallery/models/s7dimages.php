<?php

/**
 * @version    CVS: 2.0.0
 * @package    Com_S7dgallery
 * @author     carlos <carlosnaluta@gmail.com>
 * @copyright  2018 carlos
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */
// No direct access.
defined('_JEXEC') or die;


jimport('joomla.application.component.modellist');
// Load route helper.
jimport('joomla.form.helper');

/**
 * S7dgallery model.
 *
 * @since  1.6
 */
class S7dgalleryModelS7dimages extends JModelList
{
    public function getImages($id)
	{
		// Build the query for the table list.
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT images'
			. ' FROM #__s7dgallery_albums'
			. ' WHERE id = ' . (int) $id
		);
		
		$result = $db->loadResult();

		return $result;
	}
}
