<?php
/********************************************************************
Product		: Payage
Date		: 4 August 2022
Copyright	: Les Arbres Design 2014-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

define("LAPG_ADMIN_ASSETS_URL", JURI::root(true).'/administrator/components/com_payage/assets/');
define("LAPG_SITE_ASSETS_URL", JURI::root(true).'/components/com_payage/assets/');
define("LAPG_COMPONENT_LINK", "index.php?option=com_payage");
define("LAPG_COMPONENT", "com_payage");

define("LAPG_FEE_TYPE_NONE", 0);
define("LAPG_FEE_TYPE_PERCENT", 1);
define("LAPG_FEE_TYPE_FIXED", 2);

define("LAPG_STATUS_NONE",     0);	// a button has been created but nothing has yet been received from the gateway
define("LAPG_STATUS_SUCCESS",  1);	// payment was made successfully
define("LAPG_STATUS_PENDING",  2);	// the payment was made but is uncleared
define("LAPG_STATUS_FAILED",   3);	// the user failed to pay OR the payment failed to verify
define("LAPG_STATUS_CANCELLED",4);	// the user got to the payment gateway and then hit cancel
define("LAPG_STATUS_REFUNDED", 5);	// the payment has been manually refunded in Payage
define("LAPG_STATUS_MAX",      5);	// the highest status value

define("LAPG_CALLBACK_NONE",   0);	// do not call a callback function
define("LAPG_CALLBACK_CANCEL", 1);	// the user got to the payment gateway and then hit cancel
define("LAPG_CALLBACK_USER",   2);	// the user has completed payment is being redirected back to the host site
define("LAPG_CALLBACK_UPDATE", 3);	// the gateway has provided a later update to a transaction
define("LAPG_CALLBACK_BAD",    4);	// the gateway detected a malformed or illegal request

define("LAPG_OPTIONAL",  1);    // can be used by gateways for various options
define("LAPG_MANDATORY", 2);    // can be used by gateways for various options

define("LAPG_AC_UNPUBLISH",  0); // account is being unpublished
define("LAPG_AC_PUBLISH",  1);   // account is being published
define("LAPG_AC_DELETE",  2);    // account is being deleted

define("COM_PAYAGE_ERROR_BAD_GATEWAY", 1);

define("LAPG_LOG_NONE", 0);		// Log entry types
define("LAPG_LOG_INFO", 1);
define("LAPG_LOG_DATABASE_ERROR", 2);
define("LAPG_LOG_OTHER_ERROR", 3);
define("LAPG_LOG_REFUND", 4);

define("LAPG_MAX_ACCOUNT_NAME_LENGTH", 60);
define("LAPG_MAX_NAME_LENGTH", 80);
define("LAPG_MAX_EMAIL_LENGTH", 80);
define("LAPG_MAX_ADDRESS_LENGTH", 100);
define("LAPG_MAX_CITY_LENGTH", 50);    
define("LAPG_MAX_STATE_LENGTH", 50);    
define("LAPG_MAX_ZIPCODE_LENGTH", 25);
define("LAPG_MAX_COUNTRY_LENGTH", 60); 
define("LAPG_MAX_VARCHAR_LENGTH", 255); 
define("LAPG_DEFAULT_UNCONFIRMED", 60); 	// default minutes to keep unconfirmed payments (also in config.xml)
define("LAPG_DEFAULT_CONFIRMED", 1825); 	// default days to keep confirmed payments (also in config.xml)

define("LAPG_PLOTALOT_LINK", "https://www.lesarbresdesign.info/extensions/plotalot");

if (class_exists("PayageHelper"))
	return;

class PayageHelper
{

//-------------------------------------------------------------------------------
// Create an instance of the specific gateway model class
//
static function getGatewayInstance($gateway_type)
{
	$model = JPATH_ADMINISTRATOR.'/components/com_payage/models/'.strtolower($gateway_type).'.php';
	if (!file_exists($model))
		{
		LAPG_trace::trace("No file ".$model);
		return false;
		}
	require_once $model;
	$class_name = 'PayageModel'.$gateway_type;
	if (!class_exists($class_name))
		{
		LAPG_trace::trace("No class $class_name in ".$model);
		return false;
		}
	$gateway_model = new $class_name;
	if (!is_object($gateway_model))
		{
		LAPG_trace::trace("Unable to instantiate gateway model [$gateway_type]");
		return false;
		}
		
// load the gateway specific language file

	$lang = JFactory::getLanguage();
	$lang->load('com_payage_'.strtolower($gateway_type), JPATH_ADMINISTRATOR.'/components/com_payage');
	
	return $gateway_model;
}

//-------------------------------------------------------------------------------
// Return a description for a payment status
//
static function getPaymentDescription($status_code)
{
	switch ($status_code)
		{
		case LAPG_STATUS_NONE:
			return JText::_('COM_PAYAGE_UNCONFIRMED');
		case LAPG_STATUS_SUCCESS:
			return JText::_('COM_PAYAGE_SUCCESS');
		case LAPG_STATUS_PENDING:
			return JText::_('COM_PAYAGE_PENDING');
		case LAPG_STATUS_FAILED:
			return JText::_('COM_PAYAGE_FAILED');
		case LAPG_STATUS_CANCELLED:
			return JText::_('COM_PAYAGE_CANCELLED');
		case LAPG_STATUS_REFUNDED:
			return JText::_('COM_PAYAGE_REFUNDED');
		default:
			return JText::_('COM_PAYAGE_UNKNOWN_STATUS');
		}
}

//-------------------------------------------------------------------------------
// Format a money amount
//
static function format_amount($number, $format = 0, $symbol = '')
{
	if (!is_numeric($number))
		return $number;
    if ($number == 0)
        return '0';
	switch ($format)
		{
		case 0:	 return number_format($number,2,'.','');
		case 1:	 return $symbol.number_format($number,2,'.',',');
		case 2:	 return $symbol.' '.number_format($number,2,'.','');
		case 3:	 return $symbol.number_format($number,2,'.',',');
		case 4:	 return $symbol.' '.number_format($number,2,'.',',');
		case 5:	 return $symbol.number_format($number,2,',','.');
		case 6:	 return $symbol.' '.number_format($number,2,',','.');
		case 7:	 return $symbol.number_format($number,2,',',' ');
		case 8:	 return $symbol.' '.number_format($number,2,',',' ');
		case 9:	 return $symbol.number_format($number,2,'.',' ');
		case 10: return number_format($number,2,'.',',').$symbol;
		case 11: return number_format($number,2,'.',',').' '.$symbol;
		case 12: return number_format($number,2,',','.').$symbol;
		case 13: return number_format($number,2,',','.').' '.$symbol;
		case 14: return number_format($number,0,'.','');
		case 15: return $symbol.number_format($number,0,'.','');
		case 16: return $symbol.' '.number_format($number,0,'.','');
		case 17: return number_format($number,0,'.','').$symbol;
		case 18: return number_format($number,0,'.','').' '.$symbol;
		case 19: return number_format($number,0,',','');
		}
}

//-------------------------------------------------------------------------------
// Get a component version
//
static function getComponentVersion($comp = 'payage')
{
	if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_'.$comp.'/'.$comp.'.xml'))
		return '0';
	$xml_array = JInstaller::parseXMLInstallFile(JPATH_ADMINISTRATOR.'/components/com_'.$comp.'/'.$comp.'.xml');
	return $xml_array['version'];
}	

//-------------------------------------------------------------------------------
// Load Payage's main language file from the admin side
//
static function loadLanguageFile()
{
	$lang = JFactory::getLanguage();
	$lang->load('com_payage', JPATH_ADMINISTRATOR.'/components/com_payage/');
}

//-------------------------------------------------------------------------------
// Return true if supplied argument is numeric, else false
//
static function is_number($arg, $allow_blank=true, $min=0, $max=0)
{
	if ($arg === '')
		{
		if ($allow_blank)
			return true;
		else
			return false;
		}
	$filter_options = array('options' => array('min_range' => $min));
	if ($max != 0) 
		$filter_options['options']['max_range'] = $max;
	if (filter_var($arg, FILTER_VALIDATE_FLOAT, $filter_options) === false)
		return false;   // filter_var() returns the filtered data, or false if the filter fails

// min_range and max_range are not checked < PHP 7.4		

	if ($arg < $min)		
		return false;
	if (($max != 0) && ($arg > $max))
		return false;
	return true;
}

//-------------------------------------------------------------------------------
// Get client's IP address
//
static function getIPaddress()
{
	if (isset($_SERVER["REMOTE_ADDR"]))
		return $_SERVER["REMOTE_ADDR"];
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		return $_SERVER["HTTP_X_FORWARDED_FOR"];
	if (isset($_SERVER["HTTP_CLIENT_IP"]))
		return $_SERVER["HTTP_CLIENT_IP"];
	return '';
} 

//-------------------------------------------------------------------------------
// Send an email
//
static function send_email($email_to, $subject, $body_text)
{
	if (!self::is_email($email_to, false))
		{
        LAPG_trace::trace("send_email called with invalid email address $email_to");
		return;
		}
	$app = JFactory::getApplication();
    try
		{
		$mailer = JFactory::getMailer();
		$mailer->IsHTML(true);
		if (function_exists('escapeshellarg'))											// 12.14.03 handle sites that don't have escapeshellarg
			$mailer->setSender(array($app->get('mailfrom'), 'Payage'));					// with no sender, PHPMailer won't call escapeshellarg
		else
			LAPG_trace::trace("******* NOT SETTING SENDER BECAUSE THE escapeshellarg FUNCTION DOES NOT EXIST");	
		$mailer->setSubject($subject);
		$mailer->setBody($body_text);
		$mailer->addRecipient($email_to);
		$mailer->Send();
		}
	catch (Exception $e)
		{
	    $result_msg = $e->getMessage();
        LAPG_trace::trace("phpmailerException: $result_msg");
		}
}

// -------------------------------------------------------------------------------
// Validate an email address
// JMailHelper::isEmailAddress() accepts dotless domain names which cause an exception when sending mail 
//
static function is_email($arg, $allow_blank=true)
{
	if ($arg === '')
		{
		if ($allow_blank)
			return true;
		else
			return false;
		}
	if (filter_var($arg, FILTER_VALIDATE_EMAIL) === false)
		return false;
	else
		return true;
}

// -------------------------------------------------------------------------------
// get an array of languages published on the front end
// this is (more or less) the way the Joomla language switcher does it
// if $active_language is passed in, we overwrite it with the current active language
//
static function get_site_languages(&$active_language = false)
{
    $published_languages = array();
    $languages	= JLanguageHelper::getLanguages();
    $sitelangs = JLanguageHelper::getInstalledLanguages(0);
    foreach ($languages as $i => $language)
        if (array_key_exists($language->lang_code, $sitelangs))
            $published_languages[$language->lang_code] = $language->title;
            
    if (count($published_languages) == 1)
        return $published_languages;
            
    if ($active_language !== false)
        {
        $lang = JFactory::getLanguage('JPATH_SITE');
        $active_language = $lang->get('tag');
        unset($lang);       // it's huge
        }
    return $published_languages;
}

//-------------------------------------------------------------------------------
// Return true if supplied argument is a possible TID, else false
//
static function is_tid($tid, $optional)
{
	if ($optional && ($tid == 0))
		return true;
	if (preg_match("/^[a-f0-9]{32}$/", $tid))
		return true;
	else
		return false;
}

//-------------------------------------------------------------------------------
// Log an entry in the system log
//
static function syslog($log_type, $title = '', $detail = '', $add_request_data = false)
{
	require_once JPATH_ADMINISTRATOR.'/components/com_payage/models/syslog.php';
	$syslog_model = new PayageModelSyslog;
	$syslog_model->ladb_unlock();
	$syslog_model->create_new($log_type, $title, $detail, $add_request_data);
}

}