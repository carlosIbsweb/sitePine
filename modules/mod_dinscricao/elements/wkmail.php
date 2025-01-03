<?php /**
 * Wkmail Field class for the .
 *
 * @subpackage  mod_dinscricao
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */

class JFormFieldWkmail extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var     string
	 * @since   
	 */
	protected $type = 'Wkmail';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   
	 */
	protected function getInput()
	{
		$document = JFactory::getDocument();
		$document->addScriptDeclaration("JURI='" . JURI::root() . "';");
		$path = 'modules/mod_dinscricao/elements/css/';
		JHtml::_('jquery.framework');
		JHtml::_('jquery.ui', array('core', 'sortable'));
		JHtml::_('behavior.framework', $type);
		JHTML::_('stylesheet', $path . 'style.css');

		return '';
	}
}
