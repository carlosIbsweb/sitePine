<?php
/********************************************************************
Product		: Payage
Date		: 21 January 2022
Copyright	: Les Arbres Design 2014-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class com_PayageInstallerScript
{
public function preflight($type, $parent) 
{
	$app = JFactory::getApplication();

    if (defined('JVERSION'))         // JVERSION did not exist before Joomla 2.5
        $joomla_version = JVERSION;
    else
        $joomla_version = '1.x';
    
	if (version_compare($joomla_version,"3.7.0","<"))
		{
        $app->enqueueMessage("Payage requires at least Joomla 3.7.0", 'error');
		return false;
		}
		
	$app = JFactory::getApplication();
	$dbtype = $app->get('dbtype');
	if (!strstr($dbtype,'mysql'))
		{
        $app->enqueueMessage("Payage currently only supports MYSQL databases. It cannot run with $dbtype", 'error');
		return false;
		}

	$this->_db = JFactory::getDBO();
    $db_version = $this->ladb_loadResult('select version()');
	if (version_compare($db_version,"5.5.3","<"))
		{
        $app->enqueueMessage("Payage requires at least MySql 5.5.3. Your version is $db_version", 'error');
		return false;
		}

	if (version_compare(PHP_VERSION,"5.3.0","<"))
		{
        $app->enqueueMessage("Payage requires at least PHP 5.3.0. Your version is ".PHP_VERSION, 'error');
		return false;
		}

	if (!function_exists('mb_substr'))
		{
        $app->enqueueMessage("Payage cannot run on this server because it does not support the PHP Multibyte String Functions", 'error');
		return false;
		}

// get the previously installed Payage version, if any
// also clean out some old files

	if (file_exists(JPATH_ADMINISTRATOR.'/components/com_payage/payage.xml'))
		{
		$xml_array = JInstaller::parseXMLInstallFile(JPATH_ADMINISTRATOR.'/components/com_payage/payage.xml');
		$this->previous_payage_version = $xml_array['version'];
		self::recurse_delete(JPATH_ADMINISTRATOR."/components/com_payage/assets");
		}
		
	return true;
}

public function uninstall($parent)
{ 
	$text = "You uninstalled the Payage component. If you want to remove the Payage data, execute this query in phpMyAdmin:
	         <br /><br />DROP TABLE `#__payage_accounts`, `#__payage_payments`;
             <br /><br />If you DO NOT execute the query, you can install Payage again without losing your data.
             <br />Please note that you don't have to uninstall Payage to install a new version. Simply install the new version without uninstalling the current version.";
    $app = JFactory::getApplication();
	$dbprefix = $app->get('dbprefix');
	$text = str_replace('#__', $dbprefix, $text);
    $app->enqueueMessage($text, 'notice');
	return true;
}

//-------------------------------------------------------------------------------
// The main install function
//
public function postflight($type, $parent)
{
    $app = JFactory::getApplication();
    		
// we don't support the Hathor template

	$template = JFactory::getApplication()->getTemplate();
    if ($template == 'hathor')
        $app->enqueueMessage("Payage does not support the Hathor administrative template. Please use a different template", 'error');
	
// check the Joomla version

	if (substr(JVERSION,0,1) > "4")				// if > 4
        $app->enqueueMessage("This version of Payage has not been tested on this version of Joomla", 'notice');
	
// get the component version from the component manifest xml file		

	$component_version = $parent->getManifest()->version;
    
// delete redundant files from older versions

	@unlink(JPATH_SITE.'/administrator/components/com_payage/controllers/helpcontroller.php');
	@unlink(JPATH_SITE.'/administrator/components/com_payage/assets/eye.png');
    @unlink(JPATH_SITE."/administrator/components/com_payage/falang/payage_accounts.xml");
    @rmdir (JPATH_SITE."/administrator/components/com_payage/falang");
	@unlink(JPATH_SITE.'/administrator/components/com_payage/assets/payage.css');
	@unlink(JPATH_ROOT.'/administrator/components/com_payage/latest_payage.xml');        // re-created by the about view
	@unlink(JPATH_ROOT.'/administrator/components/com_payage/latest_plg_payage.xml');    // re-created by the about view
	@unlink(JPATH_ROOT.'/administrator/components/com_payage/helpers/view_helper.php');

    self::deleteAdminViews(array('help','payment_list','payment_detail','account_choice','account_edit','account_list'));
	self::recurse_delete(JPATH_SITE."/administrator/components/com_payage/tables");
    
// clean any old .gif and .jpg files from the admin assets directory (we only use .png images now)

    foreach (glob(JPATH_SITE.'/administrator/components/com_payage/assets/*.gif') as $filename)
        @unlink($filename);
    
// create our database tables - this will display an error if it fails

	$this->_db = JFactory::getDBO();
	$this->ladb_execute("CREATE TABLE IF NOT EXISTS `#__payage_accounts` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `published` tinyint(4) NOT NULL DEFAULT '1',
		  `ordering` int(11)  NOT NULL DEFAULT '0',
		  `gateway_type` varchar(32) NOT NULL DEFAULT '',
		  `gateway_shortname` varchar(32) NOT NULL DEFAULT '',
		  `account_group` int(11) NOT NULL DEFAULT '1',
		  `account_name` varchar(60) NOT NULL DEFAULT '',
		  `account_description` text NOT NULL,
		  `account_email` varchar(80) NOT NULL DEFAULT '',
		  `account_language` char(7) NOT NULL DEFAULT '',
		  `account_currency` char(3) NOT NULL DEFAULT '',
		  `button_image` varchar(255) NOT NULL DEFAULT '',
		  `button_title` varchar(255) NOT NULL DEFAULT '',
		  `fee_type` smallint(6) NOT NULL DEFAULT 0,
		  `fee_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
		  `fee_min` decimal(10,2) NOT NULL DEFAULT '0.00',
		  `fee_max` decimal(10,2) NOT NULL DEFAULT '0.00',
		  `currency_symbol` char(10) NOT NULL DEFAULT '',
		  `currency_format` smallint(6) NOT NULL,
		  `specific_data` text NOT NULL,
		  `translations` text NOT NULL,
		  PRIMARY KEY (`id`)
		) DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1000 ;");

	$this->ladb_execute("CREATE TABLE IF NOT EXISTS `#__payage_payments` (
		  `id` int(11) NOT NULL auto_increment,
		  `date_time_initiated` timestamp NOT NULL default CURRENT_TIMESTAMP,
		  `date_time_updated` timestamp NOT NULL default 0,
		  `account_id` int(11) NOT NULL,
		  `pg_transaction_id` char(32) NOT NULL,
		  `pg_status_code` smallint(6) NOT NULL,
		  `pg_status_text` varchar(255) NOT NULL,
		  `pg_history` text NOT NULL,
		  `app_name` varchar(255) NOT NULL,
		  `app_return_url` varchar(255) NOT NULL,
		  `app_update_path` varchar(255) NOT NULL,
		  `app_transaction_id` varchar(255) NOT NULL,
		  `app_transaction_details` text NOT NULL,
		  `gw_transaction_id` varchar(255) NOT NULL,
		  `gw_pending_reason` varchar(20) NOT NULL,
		  `gw_transaction_details` text NOT NULL,
		  `item_name` varchar(255) NOT NULL,
		  `currency` char(3) NOT NULL DEFAULT '',
		  `gross_amount` float NOT NULL,
		  `tax_amount` float NOT NULL,
		  `customer_fee` float NOT NULL,
		  `gateway_fee` float NOT NULL,
		  `payer_email` varchar(255) NOT NULL,
		  `payer_first_name` varchar(80) NOT NULL,
		  `payer_last_name` varchar(80) NOT NULL,
		  `payer_address1` varchar(100) NOT NULL,
		  `payer_address2` varchar(100) NOT NULL,
		  `payer_city` varchar(50) NOT NULL,
		  `payer_state` varchar(50) NOT NULL,
		  `payer_zip_code` varchar(25) NOT NULL,
		  `payer_country` varchar(60) NOT NULL,
		  `payer_country_code` char(2) NOT NULL,
		  `client_ip` varchar(45) NOT NULL,
		  `client_ua` varchar(255) NOT NULL,
		  `processed` tinyint(4) NOT NULL,
          `offline_enabled` tinyint(2) NOT NULL DEFAULT 0,		  
		  `external_currency_code` char(3) NOT NULL DEFAULT '',
		  `external_currency_amount_requested` float NOT NULL DEFAULT 0,
		  `external_currency_amount_paid` float NOT NULL DEFAULT 0,
		  `external_currency_exchange_rate` float NOT NULL DEFAULT 0,
		  `gw_addon_version` varchar(8) NOT NULL DEFAULT '',
		  PRIMARY KEY (`id`),
          UNIQUE KEY `pg_transaction_id` (`pg_transaction_id`)
          ) DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;");

	$this->ladb_execute("CREATE TABLE IF NOT EXISTS `#__payage_syslog` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `log_type` tinyint(2) NOT NULL DEFAULT 0,
		  `client_ip` varchar(32) NOT NULL,
		  `title` varchar(255) NOT NULL DEFAULT '',
		  `detail` text NOT NULL,
		  PRIMARY KEY (`id`)
		) DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;");

// update table structures

	$this->ladb_execute_ignore("ALTER TABLE `#__payage_accounts` CHANGE  `account_language` `account_language` CHAR(7) NOT NULL DEFAULT ''");
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_payments` ADD  `client_ip` VARCHAR(32) NOT NULL DEFAULT ''");
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_payments` ADD  `client_ua` VARCHAR(255) NOT NULL DEFAULT ''");
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_payments` ADD  `processed` TINYINT(4) NOT NULL DEFAULT '0'");
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_accounts` ADD  `translations` text NOT NULL DEFAULT ''");                         // 2.00
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_payments` ADD  `external_currency_code` char(3) NOT NULL DEFAULT ''");            // 2.07
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_payments` ADD  `external_currency_amount_requested` float NOT NULL DEFAULT 0");   // 2.07
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_payments` ADD  `external_currency_amount_paid` float NOT NULL DEFAULT 0");        // 2.07
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_payments` ADD  `external_currency_exchange_rate` float NOT NULL DEFAULT 0");      // 2.07
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_payments` CHANGE  `payer_country` `payer_country` varchar(60) NOT NULL DEFAULT ''"); // 2.08
	if (!$this->column_exists('#__payage_accounts', 'ordering')) 					// 2.08
		{
    	$this->ladb_execute_ignore("ALTER TABLE `#__payage_accounts` ADD  `ordering` INT(11) NOT NULL DEFAULT '0' AFTER `published`");    // 2.08
		$this->ladb_execute("UPDATE `#__payage_accounts` set ordering = (id - 999)");       // id starts at 1000
		}
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_accounts` CHANGE `gateway_shortname` `gateway_shortname` VARCHAR(32) NOT NULL DEFAULT ''");	// 2.16	
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_payments` ADD `offline_enabled` tinyint(2) NOT NULL DEFAULT 0 AFTER `processed`");           // 2.21
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_payments` ADD `gw_addon_version` varchar(8) NOT NULL DEFAULT ''");                           // 2.23
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_payments` CHANGE  `payer_address1` `payer_address1` varchar(100) NOT NULL DEFAULT ''");      // 2.25
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_payments` CHANGE  `payer_address2` `payer_address2` varchar(100) NOT NULL DEFAULT ''");      // 2.25
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_payments` CHANGE  `payer_city` `payer_city` varchar(50) NOT NULL DEFAULT ''");               // 2.25
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_payments` CHANGE  `payer_state` `payer_state` varchar(50) NOT NULL DEFAULT ''");             // 2.25
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_payments` CHANGE  `client_ip` `client_ip` varchar(45) NOT NULL DEFAULT ''");                 // 2.26
	$this->ladb_execute_ignore("ALTER TABLE `#__payage_accounts` CHANGE  `currency_symbol` `currency_symbol` varchar(10) NOT NULL DEFAULT ''");     // 2.27

// (2.27) If the payments table doesn't have the 'payer_country_code' column, create and populate it

	if (!$this->column_exists('#__payage_payments', 'payer_country_code'))
		{
		$this->ladb_execute("ALTER TABLE `#__payage_payments` ADD `payer_country_code` char(2) NOT NULL DEFAULT '' AFTER `payer_country`");      // 2.27
		$this->ladb_execute("UPDATE `#__payage_payments` SET `payer_country_code`= `payer_country` WHERE LENGTH(`payer_country`) = 2");
		}

// write an entry to the system log (type 1 = LAPG_LOG_INFO)

	$query = "INSERT INTO `#__payage_syslog` (`log_type`, `client_ip`, `title`, `detail`) VALUES (1, '', 'Installed Payage version $component_version', '')";
	$this->ladb_execute($query);

// show the update or install message

	if (isset($this->previous_payage_version) && version_compare($this->previous_payage_version,$component_version,"<"))
		{
		$url = 'https://www.lesarbresdesign.info/version-history/payage';
		$link = JHtml::link($url, $url, 'target="_blank"');
        $app->enqueueMessage("Payage updated to version $component_version. Here's what changed: $link", 'message');
		}
    else
		{
	    $app->enqueueMessage("Payage version $component_version installed.", 'message');
        $app->enqueueMessage("Please now install the gateway addons you require.", 'message');
		}

	return true;
}

//-------------------------------------------------------------------------------
// Delete one or more back end views
//
static function deleteAdminViews($views)
{
    foreach ($views as $view)
		self::recurse_delete(JPATH_SITE."/administrator/components/com_payage/views/$view");
}

//-------------------------------------------------------------------------------
// Recursively delete a folder and all its contents
//
static function recurse_delete($dir)
{ 
	if (!file_exists($dir))
		return;
    $files = array_diff(scandir($dir), array('.','..')); 
    foreach ($files as $file)
        if (is_dir($dir.'/'.$file))
            self::recurse_delete($dir.'/'.$file);
        else
            unlink($dir.'/'.$file); 
    rmdir($dir); 
}

//-------------------------------------------------------------------------------
// Execute a SQL query and return true if it worked, false if it failed
//
function ladb_execute($query)
{
	try
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
		}
	catch (RuntimeException $e)
		{
        $message = $e->getMessage();
    	$app = JFactory::getApplication();
        $app->enqueueMessage("$message <br /> $query", 'error');
		return false;
		}
	return true;
}

//-------------------------------------------------------------------------------
// Execute a SQL query ignoring any errors
//
function ladb_execute_ignore($query)
{
	try
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
		}
	catch (RuntimeException $e)
		{
		return;
		}
	return;
}

//-------------------------------------------------------------------------------
// Get a single value from the database as an object and return it, or false if it failed
//
function ladb_loadResult($query)
{
	try
		{
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		}
	catch (RuntimeException $e)
		{
        $message = $e->getMessage();
        $this->app->enqueueMessage($message, 'error');
		return false;
		}
	return $result;
}

//-------------------------------------------------------------------------------
// Check whether a table exists in the database. Returns TRUE if exists, FALSE if it doesn't
//
function table_exists($table)
{
	$query = "SELECT 1 FROM `$table` LIMIT 1";
	try
		{
		$this->_db->setQuery($query);
		$this->_db->loadResult();
        return true;                // if no error, table exists
		}
	catch (RuntimeException $e)
		{
		return false;               // if error, table doesn't exist
		}
}

//-------------------------------------------------------------------------------
// Check whether a column exists in a table. Returns TRUE if exists, FALSE if it doesn't
//
function column_exists($table, $column)
{
	if (!$this->table_exists($table))
		return false;
		
	$fields = $this->_db->getTableColumns($table);
		
	if ($fields === null)
		return false;
		
	if (array_key_exists($column,$fields))
		return true;
	else
		return false;
}

}