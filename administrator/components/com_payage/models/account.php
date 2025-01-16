<?php
/********************************************************************
Product		: Payage
Date		: 22 January 2022
Copyright	: Les Arbres Design 2014-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class PayageModelAccount extends LAPG_model
{
var $common_data = null;
var $specific_data = null;
var $gateways = null;
var $pagination = null;        // used for ordering, not pagination

//-------------------------------------------------------------------------------
// get the list of all the gateway types by reading the xml files
//
function getGatewayList()
{
	$this->gateways = array();
	
	$files = glob(JPATH_ADMINISTRATOR.'/components/com_payage/payage_*.xml');
	
	if (empty($files))
		{
		$this->app->enqueueMessage(JText::_('COM_PAYAGE_NO_GATEWAYS'), 'error');
		return array();
		}		
		
	$this->gateways = array();
	
	foreach ($files as $xmlfile)
		{
		$xml = self::getXML($xmlfile,true);
		if (!isset($xml->gateway_info))
			continue;
		$gateway_info = $xml->gateway_info;
		$gateway_type = (string) $gateway_info->type;
		if (!strpos($gateway_type,'_') || (strlen($gateway_type) > 32))
			{
			if ($this->app->isClient('administrator'))
				$this->app->enqueueMessage(JText::_('COM_PAYAGE_INVALID').' (gateway_info->type) '.basename($xmlfile), 'notice');
			continue;
			}
		$gateway_shortName = (string) $gateway_info->shortName;
		if (empty($gateway_shortName))
			{
			if ($this->app->isClient('administrator'))
				$this->app->enqueueMessage(JText::_('COM_PAYAGE_INVALID').' (gateway_info->shortName) '.basename($xmlfile), 'notice');
			continue;
			}
		if (isset($this->gateways[$gateway_type]))
			{
			if ($this->app->isClient('administrator'))
				$this->app->enqueueMessage(JText::sprintf('COM_PAYAGE_GATEWAY_DUPLICATE',basename($xmlfile),$this->gateways[$gateway_type]['xmlFile']), 'notice');
			continue;
			}
		$supported = true;
		switch ($gateway_type)		// modify shortname and longName for deprecated or discontinued gateways
			{
			case 'Skrill_LesArbres': 
				$gateway_info->shortName = 'Skrill - '.strtoupper(JText::_('COM_PAYAGE_DISCONTINUED'));
				$gateway_info->longName = 'Skrill - '.strtoupper(JText::_('COM_PAYAGE_DISCONTINUED'));
				$supported = false;
				break;
			case 
				'PayPlug_LesArbres': 
				$gateway_info->shortName = 'PayPlug - '.strtoupper(JText::_('COM_PAYAGE_DISCONTINUED'));
				$gateway_info->longName = 'PayPlug - '.strtoupper(JText::_('COM_PAYAGE_DISCONTINUED'));
				$supported = false;
				break;
			case 
				'Stripe_LesArbres': 
				$gateway_info->shortName = 'Stripe Legacy - '.strtoupper(JText::_('COM_PAYAGE_DISCONTINUED'));
				$gateway_info->longName = 'Stripe Legacy - '.strtoupper(JText::_('COM_PAYAGE_DISCONTINUED'));
				$supported = false;
				break;
			}
		$this->gateways[$gateway_type]['xmlFile'] = basename($xmlfile);
		$this->gateways[$gateway_type]['type'] = (string) $gateway_info->type;
		$this->gateways[$gateway_type]['shortName'] = (string) $gateway_info->shortName;
		$this->gateways[$gateway_type]['longName'] = (string) $gateway_info->longName;
		$this->gateways[$gateway_type]['author'] = (string) $xml->author;
		$this->gateways[$gateway_type]['authorUrl'] = (string) $xml->authorUrl;
		$this->gateways[$gateway_type]['version'] = (string) $xml->version;
		$this->gateways[$gateway_type]['defaultButton'] = (string) $gateway_info->defaultButton;
		$this->gateways[$gateway_type]['defaultTitle'] = (string) $gateway_info->defaultTitle;
		$this->gateways[$gateway_type]['gatewayUrl'] = (string) $gateway_info->gatewayUrl;
		$this->gateways[$gateway_type]['helpUrl'] = (string) $gateway_info->helpUrl;
		$this->gateways[$gateway_type]['docUrl'] = (string) $gateway_info->docUrl;
		$this->gateways[$gateway_type]['supported'] = $supported;
			
		if (!$this->app->isClient('administrator'))
			continue;
		$model = JPATH_ADMINISTRATOR.'/components/com_payage/models/'.strtolower($gateway_type).'.php';
		if (!file_exists($model))
			$this->app->enqueueMessage(JText::sprintf('COM_PAYAGE_GATEWAY_BAD_INSTALL',$gateway_type).' - '.JText::_('COM_PAYAGE_MISSING').' '.$model, 'notice');
		$form = JPATH_ADMINISTRATOR.'/components/com_payage/forms/'.strtolower($gateway_type).'.xml';
		if (!file_exists($form))
			$this->app->enqueueMessage(JText::sprintf('COM_PAYAGE_GATEWAY_BAD_INSTALL',$gateway_type).' - '.JText::_('COM_PAYAGE_MISSING').' '.$form, 'notice');
		}
	return $this->gateways;
}

//-------------------------------------------------------------------------------
// get the xml data for one specified gateway type
//
function getGatewayInfo($gateway_type)
{
	$this->getGatewayList();
	if (isset($this->gateways[$gateway_type]))
		return $this->gateways[$gateway_type];
	else
		return array();
}

//-------------------------------------------------------------------------------
// Initialise the common data for a new account
// each gateway model also has its own initData() function
//
function initData($gateway_info)
{
	$this->common_data = new stdClass();
	$this->common_data->id = 0;
	$this->common_data->published = 0;	// New accounts are created unpublished so that publishing them calls the Gateway_activate() function
	$this->common_data->ordering = 0;
	if (isset($gateway_info['type']))
		$this->common_data->gateway_type = $gateway_info['type'];
	else
		$this->common_data->gateway_type = '?';
	if (isset($gateway_info['shortName']))
		$this->common_data->gateway_shortname = $gateway_info['shortName'];
	else
		$this->common_data->gateway_shortname = '?';
	$this->common_data->account_group = 1;
	$this->common_data->account_name = '';
	$this->common_data->account_description = '';
	$this->common_data->account_email = '';
	$this->common_data->account_language = '';
	$this->common_data->account_currency = '';
	if (isset($gateway_info['defaultButton']))
		$this->common_data->button_image = $gateway_info['defaultButton'];
	else
		$this->common_data->button_image = '';
	if (isset($gateway_info['defaultTitle']))
		$this->common_data->button_title = $gateway_info['defaultTitle'];
	else
		$this->common_data->button_title = '';
	$this->common_data->fee_type = 0;
	$this->common_data->fee_amount = 0;
	$this->common_data->fee_min = 0;
	$this->common_data->fee_max = 0;
	$this->common_data->currency_symbol = '';
	$this->common_data->currency_format = 0;
	$this->common_data->specific_data = '';
	$this->common_data->translations = '';
	return $this->common_data;
}

//-------------------------------------------------------------------------------
// Initialise `ordering` to one more than the highest current value
//
function initOrdering()
{
	$max_ordering = $this->ladb_loadResult("SELECT MAX(`ordering`) FROM `#__payage_accounts`");
	if ($max_ordering)
	    $this->common_data->ordering = $max_ordering + 1;
	else
	    $this->common_data->ordering = 1;
}

//-------------------------------------------------------------------------------
// validate the data that is common to all gateways
// each gateway model also has its own check_post_data() function
//
function check_post_data()
{
    $errors = array();
	
	if (!LAPG_admin::is_string($this->common_data->account_name, 2, LAPG_MAX_ACCOUNT_NAME_LENGTH))
		$errors[] = JText::_('COM_PAYAGE_INVALID').' '.JText::_('COM_PAYAGE_ACCOUNT_NAME');
        
    if (!preg_match('/^[A-Z]{3}$/', $this->common_data->account_currency))
		$errors[] = JText::_('COM_PAYAGE_INVALID_CURRENCY');

	if (!LAPG_admin::is_string($this->common_data->currency_symbol, 1, 10, false))		
		$errors[] = JText::_('COM_PAYAGE_INVALID').' '.JText::_('COM_PAYAGE_CURRENCY_SYMBOL');	      

   	if (stristr($this->common_data->button_image, 'http') !== false)
 		$errors[] = JText::_('COM_PAYAGE_INVALID').' '.JText::_('COM_PAYAGE_BUTTON');
   		
	if (!LAPG_admin::is_string($this->common_data->button_title, 0, LAPG_MAX_VARCHAR_LENGTH))
		$errors[] = JText::_('COM_PAYAGE_INVALID').' '.JText::_('COM_PAYAGE_BUTTON_TITLE');

	if (!LAPG_admin::is_posint($this->common_data->account_group, false))
		$errors[] = JText::_('COM_PAYAGE_INVALID').' '.JText::_('COM_PAYAGE_GROUP');

	if (!PayageHelper::is_number($this->common_data->fee_min, true, 0))
		$errors[] = JText::_('COM_PAYAGE_INVALID').' '.JText::_('COM_PAYAGE_FEE_MIN');	      

	if (!PayageHelper::is_number($this->common_data->fee_max, true, 0))
		$errors[] = JText::_('COM_PAYAGE_INVALID').' '.JText::_('COM_PAYAGE_FEE_MAX');	      

	if (!PayageHelper::is_number($this->common_data->fee_amount, true, 0))
		$errors[] = JText::_('COM_PAYAGE_INVALID').' '.JText::_('COM_PAYAGE_FEE').' '.JText::_('COM_PAYAGE_AMOUNT');	      
		
	if (($this->common_data->fee_max > 0) && ($this->common_data->fee_min > $this->common_data->fee_max))
		$errors[] = JText::_('COM_PAYAGE_FEE_MIN_MAX');	      
				
	if (!empty($errors))
		{
		$this->app->enqueueMessage(implode('<br />',$errors), 'error');
		return false;
		}
    return true;
}

//-------------------------------------------------------------------------------
// get an existing row
// return false if not found
// called in the front end and the back end
//
function getOne($id)
{
    if (!is_numeric($id))
        return false;
	$query = "SELECT * FROM `#__payage_accounts` WHERE id = $id";
	$this->common_data = $this->ladb_loadObject($query);

	if (empty($this->common_data))
		{
		if ($this->app->isClient('administrator'))
			$this->app->enqueueMessage(JText::_('COM_PAYAGE_ACCOUNT_NO_RECORD'), 'error');
		else
			LAPG_trace::trace("Account $id not found");
		$this->common_data = false;
		return $this->common_data;
		}
	$this->specific_data = unserialize($this->common_data->specific_data);
	$this->translations = unserialize($this->common_data->translations);
    if ($this->app->isClient('administrator'))
    	return $this->common_data;

// for the front end we overwrite translatable fields with the data for the current site language
        
    $lang = JFactory::getLanguage('JPATH_SITE');
	$tag = $lang->get('tag');               // get current language
    $languages = PayageHelper::get_site_languages();
    $num_languages = count($languages);
    if ($num_languages <= 1)
    	return $this->common_data;

    if (empty($this->translations[$tag]))
    	return $this->common_data;        
    
// get the translations for the current language
// if they exist in the common_data that's where they belong
// if not, they belong in the specific_data

    foreach ($this->translations[$tag] as $key => $value)
        if (!empty($value))
            if (isset($this->common_data->$key))
                $this->common_data->$key = $value;
            else
                $this->specific_data->$key = $value;

	return $this->common_data;
}

//-------------------------------------------------------------------------------
// Get the common post data
// each gateway model also has its own getPostData() function that handles its specific data
//
function getPostData()
{
	$this->common_data = new stdClass();
	$jinput = JFactory::getApplication()->input;
	$this->common_data->id = $jinput->get('id', 0, 'int');
	$this->common_data->gateway_type = $jinput->get('gateway_type', '', 'string');
	$this->common_data->gateway_shortname = $jinput->get('gateway_shortname', '', 'string');
	$this->common_data->published = $jinput->get('published', 0, 'int');
	$this->common_data->ordering = $jinput->get('ordering', 0, 'int');
	$this->common_data->gateway_type = $jinput->get('gateway_type', '', 'string');
	$this->common_data->account_group = $jinput->get('account_group', 0, 'int');
	$this->common_data->account_name = $jinput->get('account_name', '', 'string');
	$this->common_data->account_description = $jinput->get('account_description', '', 'raw');   // Allow html
	$this->common_data->account_email = $jinput->get('account_email', '', 'string');
	$this->common_data->account_language = $jinput->get('account_language', '', 'string');
	$this->common_data->account_currency = $jinput->get('account_currency', '', 'string');
	$this->common_data->button_image = $jinput->get('button_image', '', 'string');
	$this->common_data->button_title = $jinput->get('button_title', '', 'string');
	$this->common_data->fee_min = $jinput->get('fee_min', '0', 'string');
	$this->common_data->fee_max = $jinput->get('fee_max', '0', 'string');
	$this->common_data->fee_type = $jinput->get('fee_type', '0', 'string');
	$this->common_data->fee_amount = $jinput->get('fee_amount', '0', 'string');
	$this->common_data->currency_symbol = $jinput->get('currency_symbol', '', 'string');
	$this->common_data->currency_format = $jinput->get('currency_format', 0, 'int');
    
    $languages = PayageHelper::get_site_languages();
	foreach ($languages as $tag => $name)
		{
        $this->translations[$tag]['button_title'] = $jinput->get($tag.'_button_title', '', 'string');
        $this->translations[$tag]['button_image'] = $jinput->get($tag.'_button_image', '', 'string');
        $this->translations[$tag]['account_description'] = $jinput->get($tag.'_account_description', '', 'string');
        }

	return $this->common_data;
}

//-------------------------------------------------------------------------------
// Store a record
//
function store()
{
	$this->common_data->specific_data = serialize($this->specific_data);	// the gateway specific data
	$this->common_data->translations = serialize($this->translations);	    // the language translations
    
    $query = $this->ladb_makeQuery($this->common_data, '#__payage_accounts');
			
	LAPG_trace::trace($query);
    
	$result = $this->ladb_execute($query);
	
	if ($result === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text.'<br />'.$query, 'error');
		return false;
		}

	if ($this->common_data->id == 0)						// if it was an insert
		$this->common_data->id = $this->_db->insertId();	// get the new id

	return true;
}

//-------------------------------------------------------------------------------
// Return a pointer to our pagination object
//
function getPagination()
{
	if ($this->pagination == Null)
		$this->pagination = new JPagination(0,0,0);
	return $this->pagination;
}

//-------------------------------------------------------------------------------
// Get the list of accounts for the main account list screen
//
function getList()
{
// get the filter and order states

	$jinput = JFactory::getApplication()->input;
	$filter_state = $this->app->getUserStateFromRequest('com_payage.account','filter_state','0','word');

// build the query

	$query_count = "Select count(*) ";
	$query_cols  = "Select `id`, `published`, `ordering`, `account_name`, `account_group`, `gateway_type`, `gateway_shortname`,
					`account_email`, `account_language`, `account_currency`, `button_image`, `button_title`,
					`fee_type`, `fee_amount`, `fee_min`, `fee_max`, `currency_symbol`, `currency_format` ";
	$query_from  = "From `#__payage_accounts` ";
	$query_where = "Where 1";
	if ($filter_state == 'P')
		$query_where .= " And `published`=1";
	if ($filter_state == 'U')
		$query_where .= " And `published`=0";
	$query_order = " Order by `ordering`";
    
// get the total row count

	$count_query = $query_count.$query_from.$query_where;
	$total = $this->ladb_loadResult($count_query);
	if ($total === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return array();
		}

// setup the pagination object so that we can use its ordering functions
// - but we don't allow pagination for accounts

	$this->pagination = new JPagination($total, 0, 10000);      // no pagination limits

// get the data

	$main_query = $query_cols.$query_from.$query_where.$query_order;
	$this->common_data = $this->ladb_loadObjectList($main_query);
	if ($this->common_data === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return array();
		}
        
	return $this->common_data;
}

//-------------------------------------------------------------------------------
// Get all data for all published accounts that match the group and currency specified
// - if group specified is zero it means get accounts of all groups
//
function getAccounts($group,$currency)
{
	$query = "Select * From `#__payage_accounts` Where `published` = '1' And `account_currency` = ".$this->_db->quote($currency);
	if ($group != 0)
		$query .= " And `account_group` = ".$this->_db->quote($group);
    $query .= " Order by `ordering`";
    
	$data = $this->ladb_loadObjectList($query);
	
	if ($data === false)
		{
		if ($this->app->isClient('administrator'))
			$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return array();
		}

	return $data;
}

//-------------------------------------------------------------------------------
// Get an array of all accounts for the dropdown account selector
//
function get_account_array($currency = '0', $all=true)
{
    if ($currency == '0')
        $where = '';
    else
        $where = "Where `account_currency` = ".$this->_db->quote($currency);
        
	$query = "Select `id`, `account_name`, `account_currency` From `#__payage_accounts` $where Order by `account_name`";

	$data = $this->ladb_loadObjectList($query);
	
	if ($data === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return array();
		}

	$accounts = array();
    if ($all)
    	$accounts[0] = JText::_('COM_PAYAGE_ALL_ACCOUNTS');
	foreach ($data as $row)
		$accounts[$row->id] = $row->account_name.', '.$row->account_currency;

	return $accounts;
}

//-------------------------------------------------------------------------------
// Get a list of all the currencies we have accounts for, to make a select list
//
function get_currency_array($group)
{
	$query = "Select Distinct(`account_currency`) From `#__payage_accounts` Where `published` = '1' ";
	if ($group != 0)
		$query .= " And `account_group` = ".$this->_db->quote($group);

	$data = $this->ladb_loadObjectList($query);
	
	if ($data === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return array();
		}

	$accounts = array();
	foreach ($data as $row)
		$accounts[$row->account_currency] = $row->account_currency;

	return $accounts;
}

//-------------------------------------------------------------------------------
// Get a list of all the account groups we have accounts for
//
function getGroups($include_all)
{
	$query = "Select Distinct(`account_group`) From `#__payage_accounts` Where `published` = '1' ";

	$data = $this->ladb_loadObjectList($query);
	
	if ($data === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return array();
		}

	$accounts = array();
	
	if ($include_all)
		$accounts[0] = '0';
	
	foreach ($data as $row)
		$accounts[$row->account_group] = $row->account_group;

	return $accounts;
}

//-------------------------------------------------------------------------------
// $p is 0 if unpublishing, 1 if publishing
//
function publish($id, $p)
{
    if (!is_numeric($id))
        return false;
	$result = $this->ladb_execute("UPDATE `#__payage_accounts` SET `published` = $p WHERE `id` = $id");
	if ($result === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return false;
		}
	return true;
}

//-------------------------------------------------------------------------------
// delete an account - don't delete accounts with payments
//
function delete($id)
{
    if (!is_numeric($id))
        return false;
	$message = '';
	$query = "select count(*) from `#__payage_payments` where `account_id` = $id";
	$count = $this->ladb_loadResult($query);
	if ($count === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return false;
		}
	if ($count != 0)
		{
		$this->app->enqueueMessage(JText::_("COM_PAYAGE_ACCOUNT_NO_DELETE_PAYMENT"));
		return false;
		}

	$query = "delete from `#__payage_accounts` where `id` = ".$this->_db->quote($id);
	$result = $this->ladb_execute($query);
	if ($result === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return false;
		}

	return true;
}

//-------------------------------------------------------------------------------
// Calculate the gateway surcharge fee
//
public static function calculate_gateway_fee($account_data, $amount)
{
	LAPG_trace::trace("calculate_gateway_fee: [Type ".$account_data->fee_type.": ".$account_data->fee_min." - ".$account_data->fee_amount." - ".$account_data->fee_max."] ".$amount);
	switch ($account_data->fee_type)
		{
		case LAPG_FEE_TYPE_NONE:
			return 0;
		case LAPG_FEE_TYPE_FIXED:
			return round($account_data->fee_amount,2);
		case LAPG_FEE_TYPE_PERCENT:
			$fee_amount = ($amount * $account_data->fee_amount) / 100;
			if ($fee_amount < $account_data->fee_min)
				return round($account_data->fee_min,2);
			if ($account_data->fee_max == 0)					// 0 means no maximum
				return round($fee_amount,2);
			if ($fee_amount > $account_data->fee_max)
				return round($account_data->fee_max,2);
			return round($fee_amount,2);
		default:
			return 0;
		}
}

//-------------------------------------------------------------------------------
// Move the ordering of an account up or down
//
function move($id, $direction)
{
    if (!is_numeric($id))
        return false;
	$query = "SELECT `id`, `ordering` FROM `#__payage_accounts` ORDER BY `ordering`";
	$data = $this->ladb_loadObjectList($query);
	if ($data === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return;
		}
	$i = 0;
	while ($data[$i]->id != $id)
		$i ++;					// find the target $id in the list
	if ($direction == 1)		// move down
		$swap_row = $i + 1;     // swap the ordering of the target row with the ordering of the next row
	else
		$swap_row = $i - 1;     // swap the ordering of the target row with the ordering of the previous row
	if (!isset($data[$swap_row]))
		return;					// swap row does not exist so target is already at top or bottom
	$this->ladb_execute("UPDATE `#__payage_accounts` SET `ordering` = ".$data[$swap_row]->ordering." WHERE `id` = $id");
	$this->ladb_execute("UPDATE `#__payage_accounts` SET `ordering` = ".$data[$i]->ordering." WHERE `id` = ".$data[$swap_row]->id);
}

//-------------------------------------------------------------------------------
// Save new ordering of all accounts
// $cids is an array of all the account id's
// $order is an array of required ordering values
// This only works if $cids and $order represent the entire table, i.e. there is no pagination
//
function saveorder($cids, $order)
{
	Joomla\Utilities\ArrayHelper::toInteger($cids);	
	Joomla\Utilities\ArrayHelper::toInteger($order);	
	asort($order);
	foreach ($order as $key => $value)
		$new_orders[] = $cids[$key];
	foreach ($new_orders as $i => $id)
		{
		$result = $this->ladb_execute("UPDATE `#__payage_accounts` SET `ordering` = ".($i + 1)." WHERE `id` = $id");
		if ($result === false)
			{
			$this->app->enqueueMessage($this->ladb_error_text, 'error');
			return;
			}
		}
}

// -------------------------------------------------------------------------------
// JFactory::getXML() was removed in Joomla 4.0 so it's here now
//
static function getXml($data, $isFile = true)
{
    $class = 'SimpleXMLElement';
    if (class_exists('JXMLElement'))
        $class = 'JXMLElement';
    libxml_use_internal_errors(true);       // Disable libxml errors and allow to fetch error information as needed
    if ($isFile)
        $xml = simplexml_load_file($data, $class);
    else
        $xml = simplexml_load_string($data, $class);
    if ($xml === false)
        {
		LAPG_trace::trace("Error loading xml file $data");
		if ($this->app->isClient('administrator'))
			$this->app->enqueueMessage("Error loading xml file $data", 'error');
        foreach (libxml_get_errors() as $error)
			LAPG_trace::trace($error->message);
        }
    return $xml;
}

//-------------------------------------------------------------------------------
// Get the plugin TID if it has been stored on the update_sites record
// Return false if there is no update_sites record for the plugin
//
function get_plugin_tid()
{
    $component = JComponentHelper::getComponent('com_installer');
    $params = $component->params;
    $cache_timeout = $params->get('cachetimeout', 6, 'int');
    if ($cache_timeout == 0)
        $this->ladb_execute("UPDATE `#__update_sites` SET `enabled` = 0 WHERE `name` LIKE '%Payage%'");		

    $query = "SELECT count(*) FROM `#__update_sites` WHERE `type` = 'extension' AND `name` = 'Payage Article Plugin'";
    $result = $this->ladb_loadResult($query);
	if ($result == 0)
		return false;	// not installed

    $query = "SELECT `extra_query` FROM `#__update_sites` WHERE `type` = 'extension' AND `name` = 'Payage Article Plugin'";
    $result = $this->ladb_loadResult($query);

    if (strlen($result) == 36)
        return substr($result,4);
    else
        return true;	// installed but no TID
}

//-------------------------------------------------------------------------------
// save the extra_query on the update site record
//
function save_plugin_tid($purchase_id)
{
    $extra_query = 'tid='.$purchase_id;
    $query = "UPDATE `#__update_sites` SET `extra_query` = ".$this->_db->quote($extra_query).
        " WHERE `type` = 'extension' AND `name` = 'Payage Article Plugin'";
	$result = $this->ladb_execute($query);
	if ($result === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return false;
		}
	return true;
}

}