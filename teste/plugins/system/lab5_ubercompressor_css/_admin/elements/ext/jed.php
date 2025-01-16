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

class JFormFieldJed extends JFormField {
		
		/////////////////////////////////////////////////////
        protected $type = 'Jed';
		/////////////////////////////////////////////////////
		public function renderField($options = array()) {
		/////////////////////////////////////////////////////
								
								return $this->getInput();  // DESCRIPTION :
								/* * * * *
									$this->element['label']
									$this->translateLabel
									// return '<h2>'.$this->renderLayout.'</h2>'; 
									$options['hiddenLabel'] = true; 
									$data = $this->getLayoutData();
									$label = $data['label'];
									$data = array(
												'input'   => $this->getInput(),
												'label'   =>$this->getLabel(),
												'options' => $options,
									); 
									return $this->getRenderer($this->renderLayout)->render($data);
								* * * * */
					
        } ///////////////////////////////////////////////////
        protected function getInput() {
        /////////////////////////////////////////////////////
				
				if( isset($this->element['url']) ) : // for old setup
					
								$url = $this->element['url'];
								
				else:
				
							$extension = $this->form->getData();
							$extension_mod = $extension->get('module');
							$extension_plg = $extension->get('element');
							if( !empty($extension_mod) ){
									$extension = $extension_mod;
							}else{
									$extension = $extension_plg;
							}
							
							$base= 'https://extensions.joomla.org/extensions/extension/';
							
							/////////////////////////////////////
							// switch ( $this->element['extension'] ){
							switch ( $extension ){
							/////////////////////////////////////
										case "mod_easyflash": 
												$url = 'core-enhancements/flash-management/lab5-easyflash/'; 
												break;
										case "mod_lab5_panorama3d": 
												$url = 'maps-a-weather/maps-a-locations/lab5-panorama-3d/';  
												break;
										case "mod_lab5_mobile_videos": 
												$url = 'multimedia/multimedia-players/lab5-mobile-videos/';  
												break;
										case "lab5_ubercompressor_css": 
												$url = 'core-enhancements/performance/lab5-uebercompressor-css/';  
												break;
										case "lab5_ubercompressor_js": 
												$url = 'core-enhancements/performance/lab5-uebercompressor-js/';  
												break;
										case "lab5_ubercompressor_html": 
												$url = 'core-enhancements/performance/lab5-uebercompressor-html/';  
												break;
										case "lab5_jpowertools": 
										case "lab5_cookie_commander": 
										case "lab5_captcha": 
												$url = 'access-a-security/site-security/lab5-captcha/';  
												break;
										case "lab5_cookie_warnings": 
										case "lab5_jusertube": 
										case "lab5_pagenavigation_with_titles": 
										case "lab5_custom_readmore": 
										case "lab5_slimbox2": 
												$url = 'photos-a-images/lab5-slimbox2/';  
												break;
										default: 
												$base= '';
												$url = 'https://extensions.joomla.org/profile/profile/details/114444/#extensions';
							}
							$url = $base.$url;
							
				endif;
				
				// $LAB5_JEDLINK_TXT = JText::_('LAB5_JEDLINK_TXT');
				$LAB5_JEDLINK_TXT="Find this extension in the JED ( Joomla! Extensions Directory )";
				// $LAB5_JEDLINK_LABEL="Extension in JED:";
				return  '<a href = "'.$url.'" target="_blank">'.$LAB5_JEDLINK_TXT.'</a>	'; 
				 
        } ///////////////////////////////////////////////////
}