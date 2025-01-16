<?php
/**  
 * @package INSTANTPAYPAL::plugins::system
 * @subpackage libraries
 * @subpackage fields
 * @author Joomla! Extensions Store
 * @copyright (C) 2014 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Form Field for css purpouse
 * 
 * @package INSTANTPAYPAL::plugins::system
 * @subpackage libraries
 * @subpackage fields
 * @since 2.0
 */
class JFormFieldCssField extends JFormField {
	/**
	 * Method to get the field label markup.
	 *
	 * @return string The field label markup.
	 *        
	 * @since 11.1
	 */
	protected function getLabel() {
		return null;
	}
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return string The field input markup.
	 *        
	 * @since 11.1
	 */
	protected function getInput() {
		// Add the css file for plugin settings styling
		$doc = JFactory::getDocument ();
		
		// Add custom JS to rework bootstrap popovers for the label description
		$script = <<<EOL
		document.addEventListener('DOMContentLoaded', function() {
			var showHideControl = function(selectValue) {
				var targetCtrls = document.querySelectorAll('*.smartcheckout');
				[].forEach.call(targetCtrls, (control) => {
					var parentControlContainer = control.closest('div.control-group,li');
					if(selectValue != 'smartcheckout') {
						parentControlContainer.style.display = 'none';
					} else {
						parentControlContainer.style.display = 'block';
					}
				});
			}
				
			var selectControlOptions = document.querySelectorAll('#jform_params_button_type option');
			var selectControl = document.querySelector('#jform_params_button_type');
			selectControl.addEventListener('change', (e) => {
				showHideControl(e.target.value);
			});
				
			var selectControlUl = document.querySelector('#jform_params_button_type_chzn');
			if(selectControlUl) {
				selectControlUl.addEventListener('click', () => {
					setTimeout(function(){
						var selectControlLi = document.querySelectorAll('ul.chzn-results li');
						[].forEach.call(selectControlLi, (controlLi) => {
							controlLi.addEventListener('click', () => {
								var selectedOptionIndex = controlLi.dataset.optionArrayIndex;
								var selectedOptionValue = selectControlOptions[selectedOptionIndex].value;
								showHideControl(selectedOptionValue);
							});
						});
					}, 0);
				});
			}
				
			var selectControlSelectedOption = document.querySelector('#jform_params_button_type option:checked');
			showHideControl(selectControlSelectedOption.value);
		});
EOL;
		$doc->addScriptDeclaration($script);
		
		return null;
	}
}