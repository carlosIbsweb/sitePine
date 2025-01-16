<?php
/**
 * @author	:	Lab5 - Dennis Riegelsberger
 * @authorUrl	:	https://lab5.ch
 * @authorEmail	:	info@lab5.ch
 * @copyright	:	(C) Lab5 - Dennis Riegelsberger. All rights reserved.
 * @copyrightUrl	:	https://lab5.ch
 * @license	:	GNU General Public License version 2 or later;
 * @licenseUrl	:	https://www.gnu.org/licenses/gpl-2.0.html
 * @project	:	https://lab5.ch/blog
 */
 
defined('JPATH_BASE') or die;
if(!defined('DS')) define('DS',DIRECTORY_SEPARATOR);

jimport('joomla.form.formfield');
// \libraries\src\Form\FormField.php$
// \layouts\joomla\form\renderfield.php

class JFormFieldIntro extends JFormField {
		
		/////////////////////////////////////////////////////
        protected $type = 'Intro';
		/////////////////////////////////////////////////////
		public function renderField($options = array()) {
		/////////////////////////////////////////////////////
		
					//////////////////////////////
					// LABEL :
					//////////////////////////////
					
								$data = $this->getLayoutData();
								$label = $data['label'];
								// $label = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
								// $label = $this->translateLabel ? \JText::_($label) : $label;
								
					//////////////////////////////
					// DESCRIPTION :
					//////////////////////////////
						
								$desc = $this->getInput();
								$return = 
								'<div class="control-group">'.
											'<hr>'.
											'';
											if(!empty($label)){
											$return .= '<h4>'.$label.'</h4>';
											}
											$return .= 
											'<div>'.$desc.'</div>'.
											'<hr>'.
								'</div>';
							
					//////////////////////////////
					
					return $return;
					
					/* * * * *
						return '
						<div class="control-group">
							<div class="controls">'.$input.'</div>
						</div>';
						
						// return '<h2>'.$this->renderLayout.'</h2>'; 
						$options['hiddenLabel'] = true; 
						$data = array(
									'input'   => $this->getInput(),
									'label'   =>$this->getLabel(),
									'options' => $options,
						); 
						return $this->getRenderer($this->renderLayout)->render($data);
					* * * * */
					
        } ///////////////////////////////////////////////////
        protected function getInput() {
			
					 return  $this->description;
					 
        } ///////////////////////////////////////////////////
		/*
		/////////////////////////////////////////////////////
		protected function getLabel(){
		/////////////////////////////////////////////////////

					return '<h2>TEST</h2>';
				
					$data = $this->getLayoutData();

					// Forcing the Alias field to display the tip below
					$position = $this->element['name'] == 'alias' ? ' data-placement="bottom" ' : '';

					// Here mainly for B/C with old layouts. This can be done in the layouts directly
					$extraData = array(
						'text'        => $data['label'],
						'for'         => $this->id,
						'classes'     => explode(' ', $data['labelclass']),
						'position'    => $position,
					);
					return $this->getRenderer($this->renderLabelLayout)->render(array_merge($data, $extraData));
			
		} ///////////////////////////////////////////////////
		*/
}