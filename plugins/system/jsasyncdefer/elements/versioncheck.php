<?php
/*------------------------------------------------------------------------
# Daycounts Version checker custom param/field
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.cache.cache');
jimport('joomla.application.helper');
jimport('joomla.filesystem.file');
jimport('joomla.html.parameter.element');

if(!class_exists('DaycountsVersionChecker')) {
	class DaycountsVersionChecker {
		public static function getVersionInfo($versioncat=0,$version=0) {
	
			$url = 'http://www.daycounts.com/index.php?option=com_versions&catid='.$versioncat.'&myVersion='.$version.'&task=checkjson';
			if(function_exists('curl_exec')) {
				// Use cURL
				$curl_options = array(
					CURLOPT_AUTOREFERER		=> true,
					CURLOPT_FAILONERROR		=> true,
					CURLOPT_FOLLOWLOCATION	=> true,
					CURLOPT_HEADER			=> false,
					CURLOPT_RETURNTRANSFER	=> true,
					CURLOPT_SSL_VERIFYPEER	=> false,
					CURLOPT_CONNECTTIMEOUT	=> 5,
					CURLOPT_MAXREDIRS		=> 20,
					CURLOPT_USERAGENT		=> 'Daycounts Updater'
				);
				$ch = curl_init($url);
				foreach($curl_options as $option => $value)	{
					@curl_setopt($ch, $option, $value);
				}
				$data = curl_exec($ch);
			} elseif( ini_get('allow_url_fopen') ) {
				// Use fopen() wrappers
				$options = array( 'http' => array(
					'max_redirects' => 10,          // stop after 10 redirects
					'timeout'       => 20,         // timeout on response
					'user_agent'	=> 'Daycounts Updater'
				) );
				$context = stream_context_create( $options );
				$data = @file_get_contents( $url, false, $context );
			} else {
				$data = false;
			}
	
			$json = @json_decode($data, true);
			$json = JFilterInput::getInstance()->clean($json,'none');
			return $json;
		}
	}
}

class JFormFieldVersionCheck extends JFormField {

	public function getInput()	{
		
		$app = JFactory::getApplication();
		if ($this->element['pluginfolder'] && $this->element['pluginname']) {
			$pluginfolder = $this->element['pluginfolder'].'';
			$pluginname = $this->element['pluginname'].'';
			$xml = JFactory::getXML(JPATH_SITE.'/plugins/'.$pluginfolder.'/'.$pluginname.'/'.$pluginname.'.xml');
		} else {
			$component = $app->input->getCmd('component');
			$xml = JFactory::getXML(JPATH_ADMINISTRATOR.'/components/'.$component.'/'.$component.'.xml');
		}

		$version = (string) $xml->version;
		$versioncat  = $this->element['versioncat'].'';

		$versionCheck = DaycountsVersionChecker::getVersionInfo($versioncat,$version);
		
		$msg = JText::_('PLG_DAYCOUNTS_YOUR_VERSION').$versionCheck['current'].'<br/>';
		$msg .= JText::_('PLG_DAYCOUNTS_LATEST_VERSION').(isset($versionCheck['latest']) ? $versionCheck['latest'] : '').'&nbsp;';
		
		if ($versionCheck['valid']===-1) {
			$msg .= '<font color=\'red\'>'.JText::_('PLG_DAYCOUNTS_VERSION_UNKNOWN').'</font>';
		} elseif ($versionCheck['valid']===0) {
			$msg .= '<font color=\'red\'>'.JText::_('PLG_DAYCOUNTS_VERSION_NEW').'</font>';
		} else {
			$msg .= '<font color=\'green\'>'.JText::_('PLG_DAYCOUNTS_VERSION_OK').'</font>';
		}

		return $msg;

	}	
	
}


