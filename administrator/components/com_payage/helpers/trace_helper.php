<?php
/********************************************************************
Product		: Payage
Date		: 5 August 2022
Copyright	: Les Arbres Design 2014-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

define("LAPG_TRACE_FILE_NAME", 'trace.txt');
define("LAPG_TRACE_FILE_PATH", JPATH_ROOT.'/components/com_payage/trace.txt');
define("LAPG_TRACE_FILE_URL", JURI::root().'components/com_payage/trace.txt');
define("LAPG_MAX_TRACE_SIZE", 2000000);	// about 2Mb
define("LAPG_MAX_TRACE_AGE",   21600);		// maximum trace file age in seconds (6 hours)
define("LAPG_UTF8_HEADER",     "\xEF\xBB\xBF");	// UTF8 file header

if (class_exists("LAPG_trace"))
	return;

class LAPG_trace
{

//-------------------------------------------------------------------------------
// Write an entry to the trace file
// Tracing is ON if the trace file exists
// if $no_time is true, the date time is not added
//
static function trace($data)
{
	if (@!file_exists(LAPG_TRACE_FILE_PATH))
		return;
	if (filesize(LAPG_TRACE_FILE_PATH) > LAPG_MAX_TRACE_SIZE)
		{
		@unlink(LAPG_TRACE_FILE_PATH);
		@file_put_contents(LAPG_TRACE_FILE_PATH, LAPG_UTF8_HEADER.date("d/m/y H:i").' New trace file created'."\n");
		}
	@file_put_contents(LAPG_TRACE_FILE_PATH, $data."\n",FILE_APPEND);
}

//-------------------------------------------------------------------------------
// Start a new trace file
//
static function init_trace($gateway_list)
{
	self::delete_trace_file();
	@file_put_contents(LAPG_TRACE_FILE_PATH, LAPG_UTF8_HEADER.date("d/m/y H:i").' Tracing Initialised'."\n");
	
	$locale = setlocale(LC_ALL,0);
	$locale_string = print_r($locale, true);
	$langObj = JFactory::getLanguage();
	$language = $langObj->get('tag');
	$php_version = phpversion();
	$app = JFactory::getApplication();
    if (function_exists('curl_init'))
		{
        $curl_info = curl_version();
		$curl_version = $curl_info['version']; // curl 7.34.0 was the first to claim support for TLSv1.2
        }
    else
        $curl_version = 'Not installed';

	self::trace('Payage version : '.PayageHelper::getComponentVersion());
	self::trace("PHP version      : ".PHP_VERSION);
	self::trace("PHP locale       : ".$locale_string);
	self::trace("allow_url_fopen  : ".ini_get('allow_url_fopen'));
	self::trace("Server           : ".PHP_OS);
	self::trace("Joomla version   : ".JVERSION);
	self::trace("Open SSL version : ".OPENSSL_VERSION_TEXT);
	self::trace("CURL version     : ".$curl_version);
	self::trace("Joomla language  : ".$language);
	self::trace("JPATH_SITE       : ".JPATH_SITE);
	self::trace("JURI::root()     : ".JURI::root());
	self::trace("Config live_site : ".$app->get('live_site'));
	self::trace("Sef              : ".self::getSefStatus());
	$plugin_status = self::getPluginStatus();
	if ($plugin_status)
		self::trace('Payage Plugin    : '.$plugin_status);
	self::trace("Joomla Caching   : ".$app->get('caching'));
	if (JPluginHelper::isEnabled('system', 'cache'))
		self::trace("Sys Cache Plugin : Enabled");
	else
		self::trace("Sys Cache Plugin : Not enabled");
	self::trace("allow_url_fopen  : ".ini_get('allow_url_fopen'));
    $published_languages = PayageHelper::get_site_languages();
    $keys = array_keys($published_languages);
    $lang_list = implode(', ',$keys);
	self::trace("Published languages  : ".$lang_list);
	$default_language = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
	self::trace("Default site language: ".$default_language);
	self::trace("Gateways:");
	foreach ($gateway_list as $gateway_info)
		self::trace("    ".$gateway_info['longName'].' v'.$gateway_info['version']);
}

//-------------------------------------------------------------------------------
// Trace an entry point
// Tracing is ON if the trace file exists
//
static function trace_entry_point($front=false)
{
	if (@!file_exists(LAPG_TRACE_FILE_PATH))
		return;
		
// if the trace file is more than 6 hours old, delete it, which will switch tracing off
//  - we don't want trace to be left on accidentally

	$filetime = @filemtime(LAPG_TRACE_FILE_PATH);
	if (time() > ($filetime + LAPG_MAX_TRACE_AGE))
		{
		self::delete_trace_file();
		return;
		}
		
	$date_time = date("d/m/y H:i:s").' ';	
	
	if ($front)
		self::trace("\n".$date_time.'================================ [Front Entry Point] ================================');
	else
		self::trace("\n".$date_time.'================================ [Admin Entry Point] ================================');
		
	if ($front)
		{
		if (isset($_SERVER["REMOTE_ADDR"]))
			$ip_address = $_SERVER["REMOTE_ADDR"];
		else
			$ip_address = '';

		if (isset($_SERVER["HTTP_USER_AGENT"]))
			$user_agent = 'HTTP_USER_AGENT: '.$_SERVER["HTTP_USER_AGENT"];
		else
			$user_agent = '';

		if (isset($_SERVER["HTTP_REFERER"]))
			$referer = 'HTTP_REFERER: '.$_SERVER["HTTP_REFERER"];
		else
			$referer = '';

		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$content_type = 'HTTP_CONTENT_TYPE: '.$_SERVER["HTTP_CONTENT_TYPE"];
		else
			$content_type = '';
			
		$method = $_SERVER['REQUEST_METHOD'];
		self::trace("$method from $ip_address $user_agent $referer $content_type");
		$session = JFactory::getApplication()->getSession();
		$session_id = $session->getId();
		self::trace("Joomla session ID: $session_id");
		}

	if (!empty($_POST))
		self::trace("Post data: ".print_r($_POST,true));
	if (!empty($_GET))
		self::trace("Get data: ".print_r($_GET,true));
}

//-------------------------------------------------------------------------------
// Delete the trace file
//
static function delete_trace_file()
{
	if (@file_exists(LAPG_TRACE_FILE_PATH))
		@unlink(LAPG_TRACE_FILE_PATH);
}

//-------------------------------------------------------------------------------
// Return true if tracing is currently active
//
static function tracing()
{
	if (@file_exists(LAPG_TRACE_FILE_PATH))
		return true;
	else
		return false;
}

//-------------------------------------------------------------------------------
// Make the html for the About page
// The controller must have the trace_on() and trace_off() functions
//
static function make_trace_controls()
{
	$label = 'Diagnostic Trace Mode';
	$title = 'Create a trace file to send to support. Please remember to switch off after use.';
    $onclick = ' onclick="document.adminForm.task.value=\'trace_on\'; document.adminForm.submit();"';
    $controls = ' <button type="button" class="btn btn-primary"'.$onclick.'>On</button>';
    $onclick = ' onclick="document.adminForm.task.value=\'trace_off\'; document.adminForm.submit();"';
    $controls .= ' <button type="button" class="btn btn-primary"'.$onclick.'>Off</button>';
	if (file_exists(LAPG_TRACE_FILE_PATH))
		$controls .= ' <span><a href="'.LAPG_TRACE_FILE_URL.'" target="_blank"> Trace File</a></span>';
	else
		$controls .= ' <span>Tracing is currently OFF</span>';
	$html = LAPG_admin::make_field($label, $controls, '', $title);
	return $html;

}

//-------------------------------------------------------------------------------
// Get the plugin status
//
static function getPluginStatus()
{
	$plugin_path = '/plugins/content/payage/payage.xml';

	if (!file_exists(JPATH_ROOT.$plugin_path))
		return false;		// the About page tests this
		
	$xml_array = JInstaller::parseXMLInstallFile(JPATH_ROOT.$plugin_path);
	$version = $xml_array['version'];
		
	if (JPluginHelper::isEnabled('content', 'payage'))
		return 'Version '.$version.' installed and enabled';
		
	return 'Version '.$version.' installed but disabled';
}

//-------------------------------------------------------------------------------
// Get the SEF status
//
static function getSefStatus()
{
	$app = JFactory::getApplication();
	if ($app->get('sef') == 0)
		return 'No';
	if ($app->get('sef_rewrite') == 0)
		return 'No rewrite';
	if (file_exists(JPATH_SITE.'/.htaccess'))
		return 'Rewrite with .htaccess';
	else
		return 'Rewrite no .htaccess';
}

}