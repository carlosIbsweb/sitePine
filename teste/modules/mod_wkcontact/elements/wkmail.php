<?php

/**
 * @subpackage  mod_wkcontact
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
		$path = 'modules/mod_wkcontact/elements/assets/';
		JHtml::_('jquery.framework');
		JHtml::_('jquery.ui', array('core', 'sortable'));
		JHtml::_('behavior.framework', $type);
		JHTML::_('stylesheet', $path . 'css/style.css');
		JHTML::_('stylesheet', $path . 'css/demo.css');
		JHTML::_('stylesheet', $path . 'css/wkcontact.min.css');
		JHTML::_('stylesheet', $path . 'css/wkcontact-render.min.css');

		$html = [];
		$html[] = '<div class="content wkcontact">';
    	$html[] = '<div class="wkcontact-header">WK Contact<div class="wkheader-right"><span class="addcol">Add col</span><div class="wkcol"></div></div></div>';
    	$html[] = '<div class="build-wrap"></div>';
    	$html[] = '<div class="render-wrap" style="display:none"></div>';
    	$html[] = '<button id="edit-form">Edit Form</button>';
  		$html[] = '</div>';
  		$html[] = '<script src="'.JUri::root().$path.'js/wkcontact.min.js"></script>';
  		$html[] = '<script src="'.JUri::root().$path.'js/wkcontact-render.min.js"></script>';
  		$html[] = '<script src="'.JUri::root().$path.'js/demo.js"></script>';
  		$html[] = '<input type="hidden" name="'. $this->name .'" id="'. $this->id .'" value="'. $this->value .'">';
  		$html[] = '<input type="hidden" name="getWkParams" id="getWkParams" data-id="'.$_GET['id'].'">';

		return implode("",$html);
	}
}
