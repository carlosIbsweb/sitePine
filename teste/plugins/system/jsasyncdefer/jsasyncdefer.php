<?php
/*------------------------------------------------------------------------
# plg_iphoneicon - iPhone Icon system plugin
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.environment.browser');
jimport('joomla.filesystem.file');

class plgSystemJsAsyncDefer extends JPlugin
{

	public function __construct(& $subject, $params = array())
	{
		parent::__construct( $subject, $params );
	}

	function onBeforeCompileHead()
	{
		error_reporting(E_ALL);
		$app = JFactory::getApplication('site');
		if ( $app->isAdmin()) return; //Exit if in administration
		$doc = JFactory::getDocument();
		
		$scripts_to_handle = trim( (string) $this->params->get('scripts_to_handle', ''));
		
		if ($scripts_to_handle) {
			$paths = array_map('trim', (array) explode("\n", $scripts_to_handle));
			foreach ($paths as $path) {

				if (strpos($path,'http')===0) {
					continue;
				}
				
				$path = trim($path);

				//Get the path only
				$uri = JUri::getInstance($path);
				$pathonly = $uri->toString(array('path'));
				if ($pathonly != $path) {
					$paths[] = $pathonly;
				}
				
				/*
				$withoutroot = str_replace(JURI::root(true),'',$path);
				if ($withoutroot != $path) {
					$paths[] = $withoutroot;
				}
				$withroot = JURI::root(true).$path;
				if ($withroot != $path) {
					$paths[] = $withroot;
				}
				$withdomain = JURI::root(false).$path;
				if ($withdomain != $path) {
					$paths[] = $withdomain;
				}
				*/
				
			}
			
			$debug = '';
			
			if ($this->params->get('debug')) {
				$lang = JFactory::getLanguage();
				$lang->load('plg_system_jsasyncdefer',JPATH_SITE.'/plugins/system/jsasyncdefer');
				$debug .= '<ul><h3>'.JText::_('PLG_JSAD_SCRIPTS_TO_FIND').':</h3>';
				foreach ($paths as $url) {
					$debug .= '<li>'.$url.'</li>';
				}
				$debug .= '</ul>';
				$debug .= '<ul><h3>'.JText::_('PLG_JSAD_SCRIPTS_FOUND').':</h3>';
			}
			
			foreach ($doc->_scripts as $url => $scriptparams) {

				//Get the path only
				$searchUrl = trim($url);
				$uri = JUri::getInstance($searchUrl);
				$searchUrl = $uri->toString(array('path'));

				if (in_array($searchUrl,$paths)) {
					if ($this->params->get('defer')) {
						$debug .= '<li>'.$url.' ==> <span class="label label-success">DEFER</span></li>';
						$doc->_scripts[$url]['defer'] = true;
					}
					if ($this->params->get('async')) {
						$debug .= '<li>'.$url.' ==> <span class="label label-success">ASYNC</span></li>';
						$doc->_scripts[$url]['async'] = true;
					}
				} else {
					$debug .= '<li>'.$url.' ==> <span class="label label-important">SKIP</span></li>';
				}
			}

			if ($this->params->get('debug')) {
				$debug .= '</ul>';
				$app->enqueueMessage($debug,'Javascript Async & Defer Plugin DEBUG');
			}
		}
		
		return true;
	}
	
}