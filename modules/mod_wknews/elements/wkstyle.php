<?php

/**
 * @subpackage  mod_wknews
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */


class JFormFieldWkstyle extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var     string
	 * @since   
	 */
	protected $type = 'Wkstyle';

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
		$path = 'modules/mod_wknews/elements/';
		JHtml::_('jquery.framework');
		JHtml::_('jquery.ui', array('core', 'sortable'));
		JHtml::_('behavior.framework', $type);
		JHTML::_('stylesheet', $path . 'css/style.css');
		JHTML::_('script', $path . 'js/scripts.js');

	}
}
