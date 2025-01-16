<?php
/**
 * @package     Lab5 - jPowerTools
 * @subpackage  Lab5 - Übercompressor CSS
 *
 * @author	:	Lab5 - Dennis Riegelsberger
 * @authorUrl	:	https://lab5.ch
 * @authorEmail	:	info@lab5.ch
 * @copyright	:	(C) Lab5 - Dennis Riegelsberger. All rights reserved.
 * @copyrightUrl	:	https://lab5.ch
 * @license	:	GNU General Public License version 2 or later;
 * @licenseUrl	:	https://www.gnu.org/licenses/gpl-2.0.html
 * @project	:	https://lab5.ch/blog
 * @file-ver	:	3.6.1
 */
defined('_JEXEC') or die;
////////////////////////////////////////////////////////////////////////////////////////
class PlgSystemLab5_ubercompressor_css extends JPlugin {
////////////////////////////////////////////////////////////////////////////////////////
					
					static $this;
					static $param;
					static $extensionname = 'jPowerTools';
					static $current_script_path;
					static $css_arr = array();
					
					
					////////////////////////////////////////////////////////////////////////////
					public function __construct(&$subject, $config = array()) {
					////////////////////////////////////////////////////////////////////////////
									
									self::$param = new StdClass();
									parent::__construct($subject, $config); 
								
					} //////////////////////////////////////////////////////////////////////////
					function onBeforeCompileHead(){ // buffer already processed
					////////////////////////////////////////////////////////////////////////////
													
													if ( !$this->_selfcheck($this)) return  ; // stop processing
													////
													if ( self::$this->params->get('compress_css', false )) {
																self::compressScripts( 'css' );
													}
													if ( self::$this->params->get('compress_js', false )) {
																self::compressScripts( 'js' );
													}
												
									return; 
						
					} //////////////////////////////////////////////////////////////////////////
					protected static function tag( $subfunctionname ) 	{
					////////////////////////////////////////////////////////////////////////////
													
										$tag = 
										'/*|.|.|.|.|.|.|.|.|.o|l|.|.|.|.|.|.|.|.|.ooo|l|l _           _     _____ |l| |         | |   |  ___||l| |     __ _| |__ |___ \ |l| |    / _` | \'_ \    \ \|l| |___| (_| | |_) /\__/ /|l\_____/\__,_|_.__/\____/ |l|l|l|t|t|t|t'.self::$extensionname.' :: '.$subfunctionname.' - was developed by :|l|t|t|t|t|l|t|t|t|t01001100 01100001 01100010 00110101|l|t|t|l|t|t|t|tLab5 - Professional Web Development|l|t|t|t|tSwitzerland|l|t|t|t|thttps://lab5.ch|t|l|l|.|.|.|.|.|.|.|.|.ooo|l|.|.|.|.|.|.|.|.|.ooo|l*/'; // SPARES SOME SPACE 
										//$tag = str_replace( "\n", '|l', $tag ) ; $tag = str_replace( "\t", '|t', $tag ) ; $tag = str_replace( "ooooooooo", '|.', $tag ) ; // FORTH
										$tag = str_replace( '|.', "ooooooooo", $tag ) ; $tag = str_replace( '|t', "\t", $tag ) ; $tag = str_replace( '|l', "\n", $tag ) ;  // BACK
										
								return $tag;
					} //////////////////////////////////////////////////////////////////////////
					static function addStyleDeclaration( $css ) { 
					////////////////////////////////////////////////////////////////////////////
														
												self::$css_arr[] = array( 'content' => $css );
												
					} //////////////////////////////////////////////////////////////////////////
					static function addStyleSheet ( $url, $mime = 'text/css', $media = '', $attribs = array() ){ 
					////////////////////////////////////////////////////////////////////////////
												
												self::$css_arr[] = array(
																'url' => $url , 
																'params' => array( 
																				'mime' => $mime, 
																				'media' => $media, 
																				'attribs' => $attribs 
																));
																
					} //////////////////////////////////////////////////////////////////////////
					static function printScripts ($letype, $ie = '' ){ 
					////////////////////////////////////////////////////////////////////////////

									foreach( self::${$letype.'_arr'} as  $obj ) { 
											
												if($letype == 'css'){
													
															$ie_attr = (!empty($ie)) ? 'ie="'.$ie.'"' : '' ;
															if( isset($obj['content']) ){
																echo '<style>'.$obj['content'].'</style>';
															}elseif( isset($obj['url']) ){
																echo '<link rel="stylesheet" href="'.$obj['url'].'" type="'.$obj['params']['mime'].'" '.$ie_attr.' />';
															}
												}
												if($letype == 'js'){
													
															if( isset($obj['content']) ){
																echo '<script type="text/javascript">'.$obj['content'].'</script>';
															}elseif( isset($obj['url']) ){
																echo '<script src="'.$obj['url'].'" type="'.$obj['params']['mime'].'" /></script>';
															}
												}
									}
									self::${$letype.'_arr'} = array();
									
					} //////////////////////////////////////////////////////////////////////////
					static function compressJS(){ self::compressScripts( 'js' ); } // OLD! MIGRATE!
					static function compressCSS ( $ie = '' ){  self::compressScripts( 'css', $ie ); } // OLD! MIGRATE!
					static function compressScripts ( $type, $ie = '' ){ 
					////////////////////////////////////////////////////////////////////////////
											
											if ( self::$this->params->get('compress_'.$type, false )) {
														require (__DIR__.'/ubercompressor_'.$type.'/Ubercompressor.php');
											}else{
														self::printScripts($type, $ie);
											}
											
					} //////////////////////////////////////////////////////////////////////////
					protected function _selfcheck( $thisthis = false ) {
					////////////////////////////////////////////////////////////////////////////
													
													/* * * * * * *
													Simple performance checks to determine whether should process further
													* * * * * * */
													if($this) self::$this = $thisthis;
													if (JFactory::getApplication()->isAdmin()) return false; 
													if (JFactory::getApplication()->getName() != 'site')  return false; 
													if (!self::$this->params->get('enabled', 1)) return false; 
													$urlOption  = JRequest::getVar('option','none');
													$urlTask    = JRequest::getVar('task','none');
													if(($urlOption == 'com_content') and ($urlTask == 'edit')) return false; 
												return true; 
												
					} //////////////////////////////////////////////////////////////////////////
					function permastore_param( $name, $value ){
					////////////////////////////////////////////////////////////////////////////
													
													/* * * * * * *
													Funktion zum Korrigieren / Migrieren von alten Parametern.
													* * * * * * */
													$table = new JTableExtension(JFactory::getDbo());
													$table->load(array('element' => $this->_name));
													$this->params->set($name, $value); 
													$table->set('params', $this->params->toString());
													$table->store(); // Save the change into DB
													
					} //////////////////////////////////////////////////////////////////////////
					
} //////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////