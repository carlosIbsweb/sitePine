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
 
defined('_JEXEC') or die;
jimport('joomla.form.formfield');
JHtml::_('behavior.framework', true); // für CSS etc (?)

class JFormFieldAbout extends JFormField {

		/////////////////////////////////////////////////////
		protected $type = 'About';
		/////////////////////////////////////////////////////

		protected function getInput() {
		/////////////////////////////////////////////////////
			
			//return '<script src="'.JURI::root().$this->element['path'].'script.js"></script>';
			//JFactory::getDocument()->addStyleSheet(JURI::root().$this->element['path'].'style.css');  
			$csspath = str_replace( JPATH_ROOT.DIRECTORY_SEPARATOR, '', __DIR__ ).'/m/style.css'; 
			$csspath = JURI::root(false).str_replace( DIRECTORY_SEPARATOR, '/', $csspath ); 
			JFactory::getDocument()->addStyleSheet( $csspath );  
			/////////////////////////
 
			$extension = $this->form->getData();  
			$ismod = $extension->get('module'); 
			if( !empty($ismod) ){  // MODULE :
			
						$extension = $extension->get('module');
						$extensionpath = "modules/".$extension ;
				
			}else{ // PLUGIN :
						
						$folder = $extension->get('folder');
						$extension = $extension->get('element');
						$extensionpath = 'plugins/'.$folder.'/'.$extension ;
			}
			// $extensionpath = $this->element['extensionpath'];
			// $extension = $this->element['extension'];
			
			/////////////////////////
			$configfile = JPATH_ROOT.'/'.$extensionpath.'/'.$extension.'.xml';
			$xml = simplexml_load_file($configfile);
			$ver = $xml->version;
			$lastupdated = '';
			// $lastupdated = ($xml->updateDate) ? ' <small>(last updated '.$xml->updateDate.')</small>' : '';
			$id = strtoupper($extension);
			/////////////////////////
			$credits = JText::_($id.'_CREDITS');
			if( $credits == $id.'_CREDITS' ){ 
							$credits = ''; // because obviously doesn't exist.
			}
			/////////////////////////
			$shortname = JText::_($id.'_SHORTNAME');
			if( $shortname == $id.'_SHORTNAME' ){ 
							/* * * * * * * 
							Migrate - if there is no respective CONSTANT ( => Translation ), 
							then GENERATE the name from name
							* * * * * * */ 
							$shortname = JText::_($id);
							$shortname = substr( $shortname, strpos( $shortname, '-' )+1);
							$shortname = trim($shortname);
			}
			/////////////////////////
			$return = "<div class='descarea'><h1>".JText::_($id)."<small> ver. ".$xml->version."</small></h1>".
						// $lastupdated.
						"<p class='projectwebsite'><a href='".$xml->project."' target='_blank'>Learn more on the ".$shortname." project website.</a></p>
						<p class='smaller license'>".$shortname." is released under the <a target='_blank' href='".$xml->licenseUrl."'>".$xml->license.".</a></p>".
						"<div class='descriptionbox'> ";
						/////////////////////////
								if( stripos( __DIR__, '_admin' ) !== false ){
									
										$logourl = '_admin';
								}else{
										$logourl = 'admin';
								}
								$logourl = JUri::root(true).'/'.$extensionpath.'/'.$logourl.'/elements/ext/m/lab5.jpg';
						/////////////////////////
						$return .= 
						//<p>Description :</p>
						JText::_($id.'_DESCRIPTION').'
						</div>'.
						'<img height="30" width="auto" src="'.$logourl.'" border="0" style="clear:both; float:none; margin:20px 0px;">
						' . $credits . '
						</div>'.
						// '<img src="https://piwik.lab5.ch/piwik.php?idsite=11&rec=1&_rcn='.$extension.'&_rck='.$extension.'_jadmin_tab_about&action_name='.$_SERVER['HTTP_HOST'].'" style="border:0"/>'. 
						'';
						
			return $return;
		}
		/////////////////////////////////////////////////////
	//protected function getLabel() { }
}
