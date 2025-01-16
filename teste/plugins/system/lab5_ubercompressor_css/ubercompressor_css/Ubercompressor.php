<?php defined('_JEXEC') or die();
/////////////////////////////////////////////////////////////////////////////////////////////
/** - Übercompressor CSS
 * @package     Lab5 - jPowerTools
 *
 * @author	:	Lab5 - Dennis Riegelsberger
 * @authorUrl	:	https://lab5.ch
 * @authorEmail	:	info@lab5.ch
 * @copyright	:	(C) Lab5 - Dennis Riegelsberger. All rights reserved.
 * @copyrightUrl	:	https://lab5.ch
 * @license	:	GNU General Public License version 2 or later;
 * @licenseUrl	:	https://www.gnu.org/licenses/gpl-2.0.html
 * @project	:	https://lab5.ch/blog
 * @release	:	1.x
 * @file-ver	:	1.1.3
 */
/////////////////////////////////////////////////////////////////////////////////////////////
if(!isset($doc)) $doc = JFactory::getDocument();
/////////////////////////////////////////////////////////////////////////////////////////////
$letype = 'css';
$letype_mime = 'text/css';
$content_type = 'text/css';
$key_files = '_styleSheets';
$key_inline = '_style';
/////////////////////////////////////////////////////////////////////////////////////////////
// le params :
/////////////////////////////////////////////////////////////////////////////////////////////
$ledata = '';		
$baseurl = JUri::root( false );	
$compress_inline = self::$this->params->get('compress_inline_'.$letype, '0' );
// Pseudopgrogressive Cachefile ?
$gzipped_cache = 
self::$this->params->get( 'compress_'.$letype.'_cache_gzipped', false );
/////////////////////////////////////////////////////////////////////////////////////////////
$font_face_code = '';		

	/////////////////////////////////////////////////////////////////////////////////////////////
				
				if(count(self::${$letype.'_arr'})){
					$normalmode = false;
				}else{
					$normalmode = true;
					foreach( $doc->{$key_files} as $file => $params ) { 
								self::${$letype.'_arr'}[] = array(
															// 'typ' => 'file' , 
															'url' => $file , 
															'params' => $params 
																				// [mime] => text/css
																				// [media] => 
																				// [attribs] => Array
															);
					}
					if($compress_inline) { 
							if(isset($doc->{$key_inline}[$letype_mime])){
								self::${$letype.'_arr'}[] = array(
															// 'typ' => 'inline' , 
															'content' => $doc->{$key_inline}[$letype_mime]);
							}
					}
				}
				//////
				// print_r( $doc->{$key_inline} ); echo"\n<br>\n<br>\n<br>"; print_r( $doc->{$key_files} ); exit;	

	/////////////////////////////////////////////////////////////////////////////////////////////
	/////// CACHE - Settings
	/////////////////////////////////////////////////////////////////////////////////////////////
			// CHECKSUM - create checksum for cachefile 
			$md5sum = ''; // create checksum for cachefile  : 
			foreach( self::${$letype.'_arr'} as  $obj ) { 
						
						if( isset($obj['content']) ){
								$md5sum .= md5($obj['content']);
						}elseif( isset($obj['url']) ){
								$md5sum .= $obj['url'];
						}
			}
			$md5sum = md5($md5sum) ; 
			///////////////////////////////////////////////////////////////
			// Setting up the file name and path to the file
			$path = str_replace( JPATH_ROOT . DIRECTORY_SEPARATOR, '', JPATH_CACHE) . "/lab5-jpowertools-ubercompressor-". $letype;
			if (!JFolder::exists($path)) {  mkdir($path);  } 
			$cache_fullpath = $path. '/' . $letype ."-". $md5sum; 
			$cache_fullpath .= ( $gzipped_cache ) ? '.php' : '.'.$letype ; 
			$cache_time = self::$this->params->get('compress_'.$letype.'_cache_time', '60000' ); // Grab the cache time from parameters		
			/////////////////////////////////////////////////////////////////////////////////////////////
			if (JFile::exists($cache_fullpath)) { 
				// echo 'exists '."\n<br>"; 
				// echo 'exists '.$cache_fullpath ."\n<br>"; 
				$diff = (time()-filectime($cache_fullpath));
			} else {
				// echo 'NOT exists '.$cache_fullpath ."\n<br>"; 
				// echo 'NOT exists ' ."\n<br>"; 
				$diff = $cache_time+1;
			} 
			// echo 'FILEAGE: ' .$diff.' > '.$cache_time."\n<br>"; 
	/////////////////////////////////////////////////////////////////////////////////////////////
	if ( $diff > $cache_time )	{
	/////////////////////////////////////////////////////////////////////////////////////////////
					
					////////////////////////////////////////////////////////////////////////////////////////////////
					////////////////////////////////////////////////////////////////////////////////////////////////
					foreach( self::${$letype.'_arr'} as $obj ) :
					////////////////////////////////////////////////////////////////////////////////////////////////
					////////////////////////////////////////////////////////////////////////////////////////////////

								///////////////////////////////////////////////////////////////
								// is this a file or inline-css ?
								///////////////////////////////////////////////////////////////
								if( isset($obj['content']) ): // inline 
								///////////////////////////////////////////////////////////////
								
											$ledata .= $obj['content'];  
													
								else: // typ == file (url) 
								///////////////////////////////////////////////////////////////
													
											$file = $obj['url'];
											$params = $obj['params'];
									
											// fonts :
											///////////////////////////////////////////////////////////////
														/********
														Lake linked fonts and turn them into @imports :
														****/
														/*
														<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700italic' rel='stylesheet' type='text/css'>
														@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700italic);
														*/
														$needle = 'fonts.googleapis.com';
														if ( stripos( $file, $needle ) ){
																
																$file = substr( $file, stripos( $file, $needle) );
																$file = "\n" . '@import url("//'.$file.'");' . "\n";	
																$ledata .= $file ;	
																$file =  '' ;	
																continue;
														}


											///////////////////////////////////////////////////////////////
											// prepare the file - string :
											///////////////////////////////////////////////////////////////
														$file = str_replace( $baseurl, '/', $file);
														$file = ( substr( $file, 0, 1 ) == '/' ) ? substr( $file, 1 ) : $file;
														
														// if vars are attached, treat as remote
														if( stripos( $file, '?' ) !== false ){
																	
																	if( stripos( $file, 'http' ) === false ) {
																				
																				
																				if(class_exists( 'JUri' )){
																					
																						$file = JUri::root( false ) . $file;
																				}else{
																						$file = '/' . $file;
																				}
																	}	
														}		
														// if( strpos($file, 'http://' ) === false &&
															// strpos($file, 'https://' ) === false ){

																	// if( substr( $file, 0, 1 ) !== '/' ){

																			// $file = '/'.$file;
																	// }
																	// $file =
																	// 'http://'.$_SERVER['HTTP_HOST'].$file;
														// }
														// $content = $TOOLS->grabPageCURL($file);
														//$islocal = ( stripos( $file, 'http' ) === false ) ? true : false ;	
														//echo $file ." \n";
											///////////////////////////////////////////////////////////////
											// prepare the new internal path for the cache css file
											///////////////////////////////////////////////////////////////
														// urls müssen ( durch absolute ) ersetzt werden :
														self::$current_script_path = str_replace( basename($file), '', $file );
														
											///////////////////////////////////////////////////////////////
											// read the files 
											///////////////////////////////////////////////////////////////
														$data = @file_get_contents($file); 
											///////////////////////////////////////////////////////////////
											// prepare its content - rewite the paths
											///////////////////////////////////////////////////////////////
																
														// require_once(__DIR__.'/ubercompressor/zenurirewriter.php');
														// $data = ZenUriRewriter::rewrite( $data , $path );
														
														$data =	preg_replace_callback('/url\(["\']*([^\'"))]*)["\']*\)/',
																		///////////////////////////////////////////////////////////////	  
																		function ($treffer) {
																		///////////////////////////////////////////////////////////////	  
																						//return self::CSSurlReplacer($treffer);
																						///////////////////////////////////////////////////////////////	  
																						$finalurl = $treffer[1];
																						//echo  'url("'.$finalurl.'")'; 
																						/////////////////
																						if( strpos($finalurl, 'http://' ) !== false OR strpos($finalurl, 'https://' ) !== false ){
																										// nichts tun.
																						}elseif( preg_match( '/([a-z])/i', substr( $finalurl, 0, 1 ))){ // falls !relative url von aktu.verzeichnis aus

																										$finalurl = self::$current_script_path.$finalurl;
																						/////////////////
																						}else{ // relativer pfad mit ../
																						/////////////////
																										$c = substr_count( $treffer[1], '../' );
																										/////////////////
																										if($c > 0 ){ // wichtig, sonst werden absolute URLs auch bearbeitet
																										/////////////////
																												$treffer[1] = str_replace( '../', '', $treffer[1] );

																												$p = explode( '/', self::$current_script_path );
																												for( $i=0; $i<=$c; $i++ ){

																																array_pop($p); //[count($p)-1-$i-1]
																												}
																												$absoluteurl = implode( '/', $p );
																												$finalurl = $absoluteurl.'/'.$treffer[1];
																										}////////////////
																						}////////////////
																		//echo  'url("'.$finalurl.'")'; // exit;
																		$finalurl = ( substr( $finalurl, 0, 1 ) == '/' ) ? substr( $finalurl, 1 ) : $finalurl;
																		$finalurl = ( substr( $finalurl, 0, 4 ) == 'http' ) ? $finalurl : '/'.$finalurl;
																		return 'url("'.$finalurl.'")';		
																		///////////////////////////////////////////////////////////////			
																		}, 
														$data );
														//////////////////////////////////////
														$data .= "\n#endofpage505endofpage{border:0}";
											///////////////////////////////////////////////////////////////
											$ledata .= $data;			
								///////////////////////////////////////////////////////////////
								endif; // typ == file (url)
					endforeach ;
					////////////////////////////////////////////////////////////////////////////////////////////////
					// Compress inline ( header only )
					////////////////////////////////////////////////////////////////////////////////////////////////
					// if( $compress_inline) :
					
											// //foreach( $doc->{$key_inline} as $file => $params ) :
											// ////////////////////////////////////////////////////////////////////////////////////////////////
											// $data = $doc->{$key_inline}[$letype_mime];
														
											// ///////////////////////////////////////////////////////////////
											// // minify
											// ///////////////////////////////////////////////////////////////
														// //$data = \JShrink\JShrink_Minifier::minify($data);
											
														// $ledata .= $data;					
											// //endforeach;
					// endif;
					////////////////////////////////////////////////////////////////////////////////////////////////
					// extract and put afront all font-face codes
					////////////////////////////////////////////////////////////////////////////////////////////////
					
														$needle = '(@font-face([\t\s]*){.*?})';
														
														// Find and collect all font-face codes :
														preg_match_all( '/'.$needle.'/ismu', $ledata, $fontmatches);
														// print_r( $fontmatches ); exit;
														foreach( $fontmatches[0] as $fontstyle ){
																		
																	// cut them out of the original code :
																	$ledata = str_replace( $fontstyle, '', $ledata ); 
																	// collect the font-fac codes :
																	$font_face_code .= $fontstyle;
																	
														}
														// 
														// Re-add the font-face codes, but in front of all other code :
														$ledata = $font_face_code . $ledata;

										// print_r($ledata); exit;
								
					////////////////////////////////////////////////////////////////////////////////////////////////
					// extract and put afront all font - import commands
					////////////////////////////////////////////////////////////////////////////////////////////////
						
								// @import MÜSSEN am Anfang einer ( oder sogar aller?) CSS stehen !
								$fontimports = array();
								preg_match_all('/\@import[\s]* url\(.*\);/', $ledata, $fontimports);
								$ledata = str_replace( $fontimports[0], '', $ledata);
								$fontimports = $fontimports[0];
								// @import am Anfang hinzufügen :
								$ledata = implode( "\n", $fontimports)."\n".$ledata;
								
					
					////////////////////////////////////////////////////////////////////////////////////////////////
					// Compress it 
					////////////////////////////////////////////////////////////////////////////////////////////////
						
											/* remove comments */
													$ledata = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $ledata);
											/* remove tabs, spaces, new lines, etc. */
													$ledata = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), ' ', $ledata);
											/* remove unnecessary spaces */
													$ledata = preg_replace('/[ ]+([{};,:])/', '\1', $ledata);
													$ledata = preg_replace('/([{};,:])[ ]+/', '\1', $ledata);
											/* remove empty class */
													//$ledata = preg_replace('/(\}([^\}]*\{\})+)/', '}', $ledata);
													// Fehlerhaft ! Entfernt auch manche Media-Queries !
													// Problem-Beispiel: "}@media (min-width:123px){ #example{}"
													$muster_nomedia = '/(\}(?!@media)([^\}]*\{\})+)/'; // if auf class folgt.
													//preg_match_all($muster_nomedia, $ledata, $test); echo var_dump($test); exit;
													$ledata = preg_replace($muster_nomedia, '}', $ledata);
													$muster_1stinmedia = '/(\{[\s]*([^\}]*\{\})+)/'; // if 1st after media open
													//preg_match_all($muster_1stinmedia, $ledata, $test); echo var_dump($test); exit;
													$ledata = preg_replace($muster_1stinmedia, '{', $ledata);
											/* remove PHP code */
													$ledata = preg_replace('/<\?(.*?)\?>/mix', '', $ledata);
											/* replace url*/
													//$ledata = preg_replace_callback('/url\(([^\)]*)\)/', array('GKTemplate', 'replaceurl'), $ledata);
											////////////////////////
											$ledata = str_replace('#endofpage505endofpage{border:0}', "\n\n\n", $ledata);
										
					////////////////////////////////////////////////////////////////////////////////////////////////
					// save it 
					////////////////////////////////////////////////////////////////////////////////////////////////
					
						$outfile = $ledata;
						if( $gzipped_cache ) : 
										// overwrite this with :
										$outfile='<?php 
										ob_start ("ob_gzhandler");
										ob_start("compress");
										header("Content-type: '.$letype_mime.'; charset: UTF-8");
										header("Cache-Control: must-revalidate");
										$offset = '.$cache_time.' ; 
										$ExpStr = "Expires: " . gmdate("D, d M Y H:i:s",time() + $offset) . " GMT";
										header($ExpStr);
										
										function compress($buffer) {
											$buffer = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $buffer);
											$buffer = str_replace(array("\r\n", "\r", "\n", "\t", "  ", "    ", "    "), "", $buffer);
											$buffer = str_replace("{ ", "{", $buffer);
											$buffer = str_replace(" }", "}", $buffer);
											$buffer = str_replace("; ", ";", $buffer);
											$buffer = str_replace(", ", ",", $buffer);
											$buffer = str_replace(" {", "{", $buffer);
											$buffer = str_replace("} ", "}", $buffer);
											$buffer = str_replace(": ", ":", $buffer);
											$buffer = str_replace(" ,", ",", $buffer);
											$buffer = str_replace(" ;", ";", $buffer);
										return $buffer;
										}
										?>';
										$outfile .= $ledata;	
						endif;			
						
						$outfile = self::tag('Ubercompressor '.strtoupper($letype)) . $outfile ;	
						JFile::delete($cache_fullpath);	
						if (JFile::exists($cache_fullpath)) { 
								@unlink($cache_fullpath);	
						} 
						JFile::write($cache_fullpath,$outfile);	
						// file_put_contents( $cache_fullpath, $outfile );
	} 
	////////////////////////////////////////////////////////////////////////////////////////////////
	// add it to the document, remove old first. finished. 
	////////////////////////////////////////////////////////////////////////////////////////////////
	self::${$letype.'_arr'} = array();
	if( !$normalmode ){
			// self::addStyleSheet($cache_fullpath,$letype_mime, 'all' );
			if( substr($cache_fullpath, 0, 1) != '/'){
				$cache_fullpath = '/'.$cache_fullpath;
			}
			// self::addStyleSheet($cache_fullpath,$letype_mime, 'screen' );
			self::addStyleSheet($cache_fullpath,$letype_mime, 'all' );
			self::printScripts($letype, $ie );
	}else{
			$doc->{$key_files} = array(); // remove all that was
			if($compress_inline) { $doc->{$key_inline}[$letype_mime] = ''; }
			// to recreate it in one - one, to bind them all ... :P
			// $doc->addStyleSheet($cache_fullpath,$letype_mime, 'screen'); 
			$doc->addStyleSheet($cache_fullpath,$letype_mime, 'all'); 
	}
	
	
	
	//////////////////////////////
	//print_r( $doc->{$key_inline} ); echo"\n<br>\n<br>\n<br>"; print_r( $doc->{$key_files} ); exit;
	//////////////////////////////

	
