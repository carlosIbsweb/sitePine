<?php
/*------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/

defined('JPATH_BASE') or die();

class JFormFieldVersionhistory extends JFormField {

	public function getInput()	{
		
		JHTML::_('behavior.modal');
		
		$versioncat  = $this->value;

		$url = JRoute::_('http://www.daycounts.com/component/versions/?tmpl=component&catid='.$this->versioncat);

		$html = '<div style="display:inline-block; margin-top:5px;">';
		$html .= '<a class="modal btn btn-mini" rel="{handler: \'iframe\', size: {x: 875, y: 550}, onClose: function() {}}" href="'.$url.'" >'.JText::_('PLG_DAYCOUNTS_VERSION_HISTORY').'</a>';
		$html .= '</div>';
			
		return $html;

	}	
	
}

