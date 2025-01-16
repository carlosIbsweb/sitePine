<?php

/**
 * @subpackage  mod_wkcontact
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */


class JFormFieldS7dform extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var     string
	 * @since   
	 */
	protected $type = 'S7dform';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   
	 */
	protected function getInput()
	{

		
		// Get the document object.
        $doc = JFactory::getDocument();
        $path = JUri::root(true).'/modules/mod_s7dform/elements/assets/';
        //JHtml::_('jquery.framework');
        //JHtml::_('jquery.ui', array('core', 'sortable'));
        //JHtml::_('behavior.framework', $type);
        $doc->addStyleSheet($path.'/css/demo.css?v=1');
        $doc->addStyleSheet($path.'/css/style.css?v=1');
        $doc->addScript($path.'/js/formeo.min.js?v=2');
        $doc->addScript($path.'/js/demo.js?v=1');

    $value = $this->value;

	$html[] = '<div id="nada"></div>';
 	$html[] = '<div id="s7dForm"></div>';
  	$html[] = '<div class="render-form" id="render"></div>';
  	$html[] = '<textarea  name="'.$this->name.'" id="awFormJsonData">'.$value.'</textarea>';

	echo implode('',$html);
	}
}



