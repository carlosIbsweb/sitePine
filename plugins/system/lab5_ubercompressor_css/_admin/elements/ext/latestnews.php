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
 * @file-ver	:	3
 */
 
defined('_JEXEC') or die;

jimport('joomla.form.formfield');

class JFormFieldLatestnews extends JFormField {
	
		/////////////////////////////////////////////////////
        protected $type = 'Latestnews';
        protected $url = 'https://lab5.ch';
		
		/////////////////////////////////////////////////////
		public function renderField($options = array()) {
		/////////////////////////////////////////////////////
						return $this->getInput();
		} ///////////////////////////////////////////////////
        protected function getInput() {
		/////////////////////////////////////////////////////
		
				$debug = false; // true false
				$ELEMENTS = array();
				$news_element = array();
				$urlbase = 'https://lab5.ch/images/extensions';
				/////////////////////////
	 
				$extension = $this->form->getData();  
				$ismod = $extension->get('module'); 
				if( !empty($ismod) ){  // MODULE :
				
							$extension = $extension->get('module');
					
				}else{ // PLUGIN :
							
							$folder = $extension->get('folder');
							$extension = $extension->get('element');
				}
				/////////////////////////
			
				$return =  '<h1>'.JText::_($extension).' recommends :</h1>';
				$return =  '<h1>Lab5 recommends :</h1>';
				$return .=  '<h3>some usefull extensions ...</h3>';
				//////////////////////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////////////////////////////////////////////////////////////////////////////
				
						///////////////////////////////////
						$news_element['title'] = 'Cookie Commander';
						///////////////////////////////////
						
								$news_element['extension'] = 'lab5_cookie_commander';
								$news_element['free'] = 2;
								$news_element['img'] = $urlbase.'/cookie_commander/joomla.extension.cookie_commander.png';
								$news_element['url'] = 'https://lab5.ch/cookie-commander';
								$news_element['desc'] = '<p>DSGVÖ / GDPR Compliance,  give users direct control over cookies and tracking, + cookie warnings infobar, + control interface for your visitors.</p>
								<p>Your visitors get the infamous GDPR compliance notice bar (optional)  AND they get - in detail - controll over tracking and cookies!</p>
								<p>In its basic setup it\'s made to handle Jooma\'s basic cookies, as well as the most common tracking frameworks ( Google Analytics and Piwik/Matomo ). It supports Opt-In as well as opt-Out mode.<br>It supports optional Do-Not-No-Track if you want so. <br>It supports template overrides, too. <br>Easily enhanceable.</p><p>Since every website is individual in its makeup, this extension is specifically made with enhanceability in mind. With just a little coding knowlage, you can expand the range of controll over more cookies and scripts, and even do so on a template per template basis, if you choose.</p>'; 
								$ELEMENTS[] = $news_element; $news_element = array();
								
						///////////////////////////////////
						$news_element['title'] = 'Lab5\' Captcha';
						///////////////////////////////////
								
								$news_element['extension'] = 'lab5_captcha';
								$news_element['free'] = 1;
								$news_element['img'] = $urlbase.'/lab5_captcha/joomla_extension_lab5_captcha.png';
								$news_element['url'] = 'https://lab5.ch/captcha';
								$news_element['desc'] = '<p>Independent stand alone captcha, +100% GDPR compliant.</p><p>The most important advantage of this plugin is, that it\'s complately independent. Free of any form of 3d-party scripts or inclusions of any sorts or anything. No need to include any links, no API keys or anything, nope. Just independence and freedom. It runs completely on itself and your site alone. Sure this also makes it 100% GDPR compliant by the nature of that fact alone.</p>'; 
								$ELEMENTS[] = $news_element; $news_element = array();
								
						///////////////////////////////////
						$news_element['title'] = 'Lab5\' EasyFlash';
						///////////////////////////////////
						
								$news_element['extension'] = 'lab5_easyflash';
								$news_element['free'] = 1;
								$news_element['img'] = $urlbase.'/mod_plg_easyflash/joomla-extension_easy_flash_module_plugin.png';
								$news_element['url'] = 'https://lab5.ch/easyflash';
								$news_element['desc'] = '<p>Easily integrate good-ole Flash apps n movies into your site, which astoundingly still many people do.<br>Module and plugin. </p>'; 
								$ELEMENTS[] = $news_element; $news_element = array();
								
						///////////////////////////////////
						$news_element['title'] = 'jPowerTools';
						///////////////////////////////////
						
								$news_element['extension'] = 'lab5_jpowertools';
								$news_element['free'] = 2;
								$news_element['img'] = $urlbase.'/jpowertools/joomla.extension.lab5.jpowertools.png';
								$news_element['url'] = 'https://lab5.ch/jpowertools';
								$news_element['desc'] = '<p>Possible the most usefull bundle you\'ve ever coma across when it comes to working with Joomla! - The Swiss Army Knife tool for Joomla!. It includes:</p>
								<ul>
								<li>Übercompressor CSS, Übercompressor JS, Übercompressor HTML</li>
								<li>Image Cache, Compress and Resizer</li>
								<li>Cookie Commander</li>
								<li>Analytics ( comfortable inclusion of Google Analytics, Piwik, Matomo - with options )</li>
								<li>Joomla Options ( controlling Mootools, jQuery, Bootstrap )</li>
								<li>Generator Tag Controller</li>
								</ul>
								<p></p>
								'; 
								$ELEMENTS[] = $news_element; $news_element = array();
								
						///////////////////////////////////
						$news_element['title'] = 'Übercompressor CSS';
						///////////////////////////////////
						
								$news_element['extension'] = 'lab5_ubercompressor_css';
								$news_element['free'] = 1;
								$news_element['img'] = $urlbase.'/jpowertools_ubercompressor_css'.'
								/joomla.extension.lab5.jpowertools.ubecompressor.css.png';
								$news_element['url'] = 'https://lab5.ch/ubercompressor-css';
								$news_element['desc'] = '<p>Loading speed and SEO improvement</p><p>This is yet another very (very!) usefull extension, that speeds up your site significantly, boosts the SEO worth of your website in the eyes of sophisiticated search engines, significantly, and just improves yout visitor\'s user experience, and very noticeably so.</p>'; 
								$ELEMENTS[] = $news_element; $news_element = array();
								
						///////////////////////////////////
						$news_element['title'] = 'Übercompressor HTML';
						///////////////////////////////////
						
								$news_element['extension'] = 'lab5_ubercompressor_html';
								$news_element['free'] = 1;
								$news_element['img'] = $urlbase.'/jpowertools_ubercompressor_html'.'
								/joomla.extension.lab5.jpowertools.ubecompressor.html.png';
								$news_element['url'] = 'https://lab5.ch/ubercompressor-html';
								$news_element['desc'] = '<p>Loading speed and SEO improvement</p><p>This extension is yet another highly valued member the jPowertools / Übercompressor product line. Besides making the site a little lighter in terms of bandwith, this plugin primarily speeds up the rendering process of your website. The effect has to do with how (most) browsers build the output - technical nitty gritty which I won\'t get into at this point - but it works, noticeable. Just try and see if you like it - it\'s free after all!</p>'; 
								$ELEMENTS[] = $news_element; $news_element = array();
								
						///////////////////////////////////
						$news_element['title'] = 'Übercompressor JS';
						///////////////////////////////////
						
								$news_element['extension'] = 'lab5_ubercompressor_js';
								$news_element['free'] = 1;
								$news_element['img'] = $urlbase.'/jpowertools_ubercompressor_js'.'
								/joomla.extension.lab5.jpowertools.ubecompressor.js.png';
								$news_element['url'] = 'https://lab5.ch/ubercompressor-js';
								$news_element['desc'] = '<p>Loading speed and SEO improvement</p><p>This is yet another very (very!) usefull extension, that speeds up your site significantly. Remember : Seach engines really like fast loading and thank you by rewarding you with SERP boosts, because you just improved yout visitor\'s user experience, and very noticeably so. This plugin does just that.</p>'; 
								$ELEMENTS[] = $news_element; $news_element = array();
								
						///////////////////////////////////
						$news_element['title'] = 'Mobile Videos Module';
						///////////////////////////////////
						
								$news_element['extension'] = 'mod_lab5_mobile_videos';
								$news_element['free'] = 1;
								$news_element['img'] = $urlbase.'/mod_lab5_mobile_videos/mod_lab5_mobile_videos.intro.jpg';
								$news_element['url'] = 'https://lab5.ch/lab5-mobile-videos';
								$news_element['desc'] = '<p>Module, that lets you add real videos to your site very easily. No 3d Party services neccessary. Just the vid and you\'re ready to go!</p><p>It handles optimal format delivery and optimal implementation on your website for different devices types ( Phones, Tablets, Desktops, ... )  completely automatically.</p>'; 
								$ELEMENTS[] = $news_element; $news_element = array();
						
						///////////////////////////////////
						$news_element['title'] = 'Panorama 3D Module';
						///////////////////////////////////
						
								$news_element['extension'] = 'mod_lab5_panorama3d';
								$news_element['free'] = 1;
								$news_element['img'] = $urlbase.'/mod_lab5_panorama3d/mod_lab5_panorama3d.jpg';
								$news_element['url'] = 'https://lab5.ch/panorama-3d';
								$news_element['desc'] = '<p>Implementing Google Street View 3D Panoramas, easily and intuitively.</p><p>A gimmicky little module that may spice up your website.</p>'; 
								$ELEMENTS[] = $news_element; $news_element = array();
						
				//////////////////////////////////////////////////////////////////////////////////////////////////////////
				shuffle($ELEMENTS);
				//////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				$return .=  '  
				<table class="table table-striped table-bordered table-small">';
				//////////////////////////////////////////////////////////////////////////////////////////////////////////
				foreach( $ELEMENTS as $newitem ) :
				//////////////////////////////////////////////////////////////////////////////////////////////////////////
						if( $newitem['extension'] != $extension OR $debug ) {
							
									$css_badge = 'label label-success';
									$css_badge = 'badge badge-warning';
									$badge = '';
									if( isset($newitem['free']) && $newitem['free'] ){
										
											$badge = ' <sup class="'.$css_badge.'">*free!</sup>';
											if( $newitem['free'] == 2 ){
												
													$css_badge = 'badge badge-success';
													$badge = ' <sup class="'.$css_badge.'">*free, for now!</sup>';
											}	
									}
									$return .=  '  
								
									<tr class="lab5_newsitem">
											<td style="width:230px;"><img src="'.$newitem['img'].'" width="100%" /></td>
											<td style="max-width:100%;" >
															<h3><a href="'.$newitem['url'].' target="_blank">'.
															$newitem['title'].$badge.'</a></h3>
															'.$newitem['desc'].'
											</td>
									</tr> ';
				
						}
				//////////////////////////////////////////////////////////////////////////////////////////////////////////
				endforeach;
				//////////////////////////////////////////////////////////////////////////////////////////////////////////
				$return .=  '</table>
				<div>
					<blockquote>
							<br>
							<em class="">
									Enjoy!<br>
									<small>Lab5 - Dennis Riegelsberger</small>
									<br>
							</em>
					</blockquote>
				</div>
				';
				
				 return $return;
        }
}