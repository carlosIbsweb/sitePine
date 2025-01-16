<?php
/********************************************************************
Product		: Payage
Date		: 5 August 2022
Copyright	: Les Arbres Design 2014-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class PayageModelPayment extends LAPG_model
{
var $data = null;
var $pagination = null;

//-------------------------------------------------------------------------------
// initialise the data for a new transaction
//
function initData()
{
	$this->data = new stdClass();
	$this->data->id = 0;
	$this->data->date_time_initiated = 0;
	$this->data->date_time_updated = 0;
	$this->data->account_id = 0;
	$this->data->pg_transaction_id = 0;
	$this->data->pg_status_code = 0;		// a Payage status code, e.g. LAPG_STATUS_SUCCESS
	$this->data->pg_status_text = '';
	$this->data->pg_history = '';
	$this->data->app_name = '';
	$this->data->app_return_url = '';
	$this->data->app_update_path = '';
	$this->data->app_transaction_id = 0;
	$this->data->app_transaction_details = '';
	$this->data->gw_transaction_id = 0;
	$this->data->gw_pending_reason = '';
	$this->data->gw_transaction_details = '';
	$this->data->item_name = '';
	$this->data->currency = '';
	$this->data->gross_amount = 0;
	$this->data->tax_amount = 0;
	$this->data->customer_fee = 0;
	$this->data->gateway_fee = 0;
	$this->data->payer_email = '';
	$this->data->payer_first_name = '';
	$this->data->payer_last_name = '';
	$this->data->payer_address1 = '';
	$this->data->payer_address2 = '';
	$this->data->payer_city = '';
	$this->data->payer_state = '';
	$this->data->payer_zip_code = '';
	$this->data->payer_country = '';
	$this->data->payer_country_code = '';
	$this->data->client_ip = PayageHelper::getIPaddress();
    if (isset($_SERVER["HTTP_USER_AGENT"]))
	    $this->data->client_ua = substr($_SERVER["HTTP_USER_AGENT"],0,LAPG_MAX_VARCHAR_LENGTH);
    else
        $this->data->client_ua = '';
	$this->data->processed = 0;
	$this->data->external_currency_code = '';             // these four are for crypto-currencies but could be used for any cross-currency gateway
	$this->data->external_currency_amount_requested = 0;
	$this->data->external_currency_amount_paid = 0;
	$this->data->external_currency_exchange_rate = 0;
	$this->data->offline_enabled = 0;
	$this->data->gw_addon_version = '';
	return $this->data;
}

//-------------------------------------------------------------------------------
// get an existing row
// - this can be called by the API so cannot enqueue GUI messages
//
function getOne($index, $field='id')
{
	$query = "SELECT *, NOW() AS now FROM `#__payage_payments` WHERE ".$this->_db->quoteName($field)." = ".$this->_db->quote($index);
	$this->data = $this->ladb_loadObject($query);
	if ($this->data === false)
		{
		LAPG_trace::trace($query."\n->".$this->ladb_error_text);
		return false;
		}
	if (empty($this->data))
		{
		LAPG_trace::trace("No payment for $field = $index");
		return false;
		}
	$this->data->app_transaction_details = unserialize($this->data->app_transaction_details);
	$this->data->gw_transaction_details = unserialize($this->data->gw_transaction_details);
	return $this->data;
}

//-------------------------------------------------------------------------------
// get an existing row with some account data
// - this can be called by the API so cannot enqueue GUI messages
//
function getOnePlus($index, $field='id')
{
	$query = "SELECT P.*, NOW() AS now, A.`account_name`, A.`gateway_shortname` as gateway_name, A.`currency_format`, A.`currency_symbol`
		FROM `#__payage_payments` AS P
		LEFT OUTER JOIN `#__payage_accounts` AS A ON P.`account_id` = A.`id`
		WHERE ".$this->_db->quoteName($field)." = ".$this->_db->quote($index);
	$this->data = $this->ladb_loadObject($query);
	if (empty($this->data))
		{
		LAPG_trace::trace("No payment+ for $field = $index");
		return false;
		}
	$this->data->app_transaction_details = unserialize($this->data->app_transaction_details);
	$this->data->gw_transaction_details = unserialize($this->data->gw_transaction_details);
	return $this->data;
}

//-------------------------------------------------------------------------------
// Store a record
// - this can be called by the API so cannot enqueue GUI messages
//
function store()
{
    if ($this->data->id != 0)               // if it's an Update, make sure the record still exists
        if (!$this->payment_exists($this->data->id))
            return false;        
        
    $data = clone $this->data;                        // we don't want to change the public data
	$data->app_transaction_details = serialize($this->data->app_transaction_details);
	$data->gw_transaction_details = serialize($this->data->gw_transaction_details);

    unset($data->date_time_initiated);    // defaults to CURRENT_TIMESTAMP, never updated
    unset($data->now);                    // not a real column in the table
    $data->date_time_updated = 'CURRENT_TIMESTAMP';

	$data->pg_status_text = substr($data->pg_status_text, 0, LAPG_MAX_VARCHAR_LENGTH);
	$data->gw_pending_reason = substr($data->gw_pending_reason, 0, LAPG_MAX_VARCHAR_LENGTH);
    
    $query = $this->ladb_makeQuery($data, '#__payage_payments', array('date_time_updated'));

	if (LAPG_trace::tracing())
		{
		if ($this->data->id == 0)
			$trace_info = "CREATING NEW PAYMENT RECORD ";
		else
			$trace_info = "UPDATING PAYMENT RECORD ".$this->data->id.' ';
		$trace_info .= "AID: ".$data->account_id.", TID: ".$data->pg_transaction_id.", STATUS ".$data->pg_status_code." (".
			PayageHelper::getPaymentDescription($data->pg_status_code).") [".$data->pg_status_text."]";
		LAPG_trace::trace($trace_info);
		}

	$result = $this->ladb_execute($query);	
	if ($result === false)
		{
	    PayageHelper::syslog(LAPG_LOG_DATABASE_ERROR, '', $query.'<br /><br />'.$this->ladb_error_text);
		LAPG_trace::trace($query."\n".$this->ladb_error_text);
		return false;
		}

	if ($this->data->id == 0)						// if it was an insert
		$this->data->id = $this->_db->insertId();	// get the new id (yes, in the public data)
	return true;
}

//-------------------------------------------------------------------------------
// delete one or more payments
//
function delete()
{
	$message = '';
	$jinput = JFactory::getApplication()->input;
	$cids = $jinput->get('cid', array(), 'ARRAY');
	Joomla\Utilities\ArrayHelper::toInteger($cids);	
	foreach ($cids as $cid)
		{
		$query = "delete from `#__payage_payments` where `id` = $cid";
		$result = $this->ladb_execute($query);
		if ($result === false)
			{
			LAPG_trace::trace($query."\n".$this->ladb_error_text);
			return false;
			}
		}
	return true;
}

//-------------------------------------------------------------------------------
// change the status of the current payment
// - this can be called by the API so cannot enqueue GUI messages
//
function change_status($new_status, $extra='')
{	
    if (!$this->payment_exists($this->data->id))
        return false;
	$old_status_description = PayageHelper::getPaymentDescription($this->data->pg_status_code);
	$new_status_description = PayageHelper::getPaymentDescription($new_status);
	$query = "UPDATE `#__payage_payments` 
		SET `pg_status_code` = ".$this->_db->quote($new_status).",
			`pg_status_text` = '',
			`gw_pending_reason` = '',
			`date_time_updated` = CURRENT_TIMESTAMP,
		    `pg_history` = ".$this->_db->quote($this->data->pg_history."\n".$this->data->now." - ".$extra.$old_status_description." -> ".$new_status_description).
		" WHERE `id` = ".$this->data->id;
	
	$result = $this->ladb_execute($query);
	if ($result === false)
		{
	    PayageHelper::syslog(LAPG_LOG_DATABASE_ERROR, '', $query.'<br /><br />'.$this->ladb_error_text);
		LAPG_trace::trace($query."\n".$this->ladb_error_text);
		return false;
		}
	return true;
}

//-------------------------------------------------------------------------------
// set the current payment to processed by the application
// - this can be called by the API so cannot enqueue GUI messages
//
function set_processed($value)
{
    if (!$this->payment_exists($this->data->id))
        return false;
	$query = "UPDATE `#__payage_payments` SET `processed` = ".$this->_db->quote($value).", `date_time_updated` = CURRENT_TIMESTAMP,
		    `pg_history` = ".$this->_db->quote($this->data->pg_history."\n".$this->data->now." - ".JText::_('COM_PAYAGE_PROCESSED_BY').' '.$this->data->app_name).
		" WHERE `id` = ".$this->_db->quote($this->data->id);
        
	$result = $this->ladb_execute($query);		
	if ($result === false)
		{
	    PayageHelper::syslog(LAPG_LOG_DATABASE_ERROR, '', $query.'<br /><br />'.$this->ladb_error_text);
		LAPG_trace::trace($query."\n".$this->ladb_error_text);
		return false;
		}
	return true;
}

//-------------------------------------------------------------------------------
// set the app_transaction_id for a payment
// - this is called by the API so cannot enqueue GUI messages
//
function set_app_transaction_id($value)
{
    if (!$this->payment_exists($this->data->id))
        return false;
        
	$query = "UPDATE `#__payage_payments` SET `app_transaction_id` = ".$this->_db->quote($value).", `date_time_updated` = CURRENT_TIMESTAMP
		WHERE `id` = ".$this->_db->quote($this->data->id);
	$result = $this->ladb_execute($query);
		
	if ($result === false)
		{
	    PayageHelper::syslog(LAPG_LOG_DATABASE_ERROR, '', $query.'<br /><br />'.$this->ladb_error_text);
		LAPG_trace::trace($query."\n".$this->ladb_error_text);
		return false;
		}

	return true;
}

//-------------------------------------------------------------------------------
// check if a payment record exists
//
function payment_exists($id)
{	
    if ($id == 0)
        return false;

    $this->count_query = "Select count(*) From `#__payage_payments` Where `id` = ".$this->_db->quote($id);
    $count = $this->ladb_loadResult($this->count_query);
    if ($count == 0)
        {
        LAPG_trace::trace("Payment record ".$this->data->id." does not exist");
        return false;
        }

	return true;
}

//-------------------------------------------------------------------------------
// Return a pointer to our pagination object
// This should normally be called after getList()
//
function getPagination()
{
	if ($this->pagination == Null)
		$this->pagination = new JPagination(0,0,0);
	return $this->pagination;
}

//-------------------------------------------------------------------------------
// Get the list of payments for the payments list screen
// - we don't show payments with status zero on the payments list screen
//
function getList()
{        
// get the filter states and the pagination variables

	$limit             = $this->app->get('list_limit');
	$limitstart        = $this->app->getUserStateFromRequest('com_payage.payment', 'limitstart', 0, 'int');
	$limitstart        = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0); // In case limit has been changed
	$filter_order      = $this->app->getUserStateFromRequest('com_payage.payment_filter_order', 'filter_order', 'DATE_TIME');
	$filter_order_Dir  = $this->app->getUserStateFromRequest('com_payage.payment_filter_order_Dir', 'filter_order_Dir', 'DESC');
	$search            = $this->app->getUserStateFromRequest('com_payage.payment_search','search','','RAW');
    $one_month_future  = date('Y-m-d', strtotime('+1 month'));
    $one_week_ago      = date('Y-m-d', strtotime('-1 week'));
	$filter_start_date = $this->app->getUserStateFromRequest('com_payage.payment_filter_start_date','filter_start_date',$one_week_ago,'STRING');
	$filter_end_date   = $this->app->getUserStateFromRequest('com_payage.payment_filter_end_date','filter_end_date',$one_month_future,'STRING');
	$filter_app        = $this->app->getUserStateFromRequest('com_payage.payment_filter_app','filter_app',0,'STRING');
	$filter_currency   = $this->app->getUserStateFromRequest('com_payage.payment_filter_currency','filter_currency','0','string');
	$filter_account    = $this->app->getUserStateFromRequest('com_payage.payment_filter_account','filter_account',0,'int');

// build the query

	$query_count = "Select count(*) ";
	$query_cols  = "Select P.*, A.`account_name`, A.`currency_format`, A.`currency_symbol` ";
	$query_from  = "From `#__payage_payments` AS P
					LEFT OUTER JOIN `#__payage_accounts` as A ON P.`account_id` = A.`id` ";
	$query_where = "Where P.`pg_status_code` != 0 ";

// search
// only include other filters if search is blank

	if ($search != '')
		$query_where .= $this->make_search($search);
    else
        {
        $query_where .= " AND DATE(`date_time_initiated`) >= ".$this->_db->quote($filter_start_date)." AND DATE(`date_time_initiated`) <= ".$this->_db->quote($filter_end_date);
        if ($filter_currency != '0')
            $query_where .= " AND `currency` = ".$this->_db->quote($filter_currency);
    
        if ($filter_app != '0')
            $query_where .= " AND `app_name` = ".$this->_db->quote($filter_app);
    
        if ($filter_account != 0)
            $query_where .= " AND `account_id` = ".$this->_db->quote($filter_account);
        }

// order by

	if (strcasecmp($filter_order_Dir,'ASC') != 0)
		$filter_order_Dir = 'DESC';

	switch ($filter_order)
		{
		case 'APP_NAME':
			$query_order = ' ORDER BY `app_name` '.$filter_order_Dir;
			break;
		case 'ITEM_NAME':
			$query_order = ' ORDER BY `item_name` '.$filter_order_Dir;
			break;
		case 'ACCOUNT_NAME':
			$query_order = ' ORDER BY `account_name` '.$filter_order_Dir;
			break;
		case 'PAYER_NAME':
			$query_order = ' ORDER BY `payer_last_name` '.$filter_order_Dir.', `payer_first_name` ASC';
			break;
		case 'EMAIL':
			$query_order = ' ORDER BY `payer_email` '.$filter_order_Dir;
			break;
		default:
			$query_order = ' ORDER BY `date_time_initiated` '.$filter_order_Dir;
		}

// get the total row count and initialise pagination

	$this->count_query = $query_count.$query_from.$query_where;
    LAPG_trace::trace("getList() count query: ".$this->count_query);
	$this->count_query_total = $this->ladb_loadResult($this->count_query);
	if ($this->count_query_total === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text,'error');
		return array();
		}

	if ($limitstart > $this->count_query_total)
		$limitstart = 0;

	$this->pagination = new JPagination($this->count_query_total, $limitstart, $limit);

// Get the data

	$this->main_query = $query_cols.$query_from.$query_where.$query_order;
    LAPG_trace::trace("getList() main query: ".$this->main_query);
	$this->data = $this->ladb_loadObjectList($this->main_query, $limitstart, $limit);
	if ($this->data === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text,'error');
		return array();
		}
	return $this->data;
}

//-------------------------------------------------------------------------------
// Get the list of unconfirmed payments, i.e with status zero
//
function getUnconfirmedList()
{
    $this->purge();     // purge expired pending payments
    
// get the pagination variables

	$limit      = $this->app->get('list_limit');
	$limitstart = $this->app->getUserStateFromRequest('com_payage.pending', 'limitstart', 0, 'int');
	$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0); // In case limit has been changed
	$search     = $this->app->getUserStateFromRequest('com_payage.payment_search','search','','RAW');

// build the query

	$query_count = "Select count(*) ";
	$query_cols  = "Select P.`id`, P.`date_time_initiated`, P.`app_name`, P.`item_name`, P.`payer_country_code`,
					P.`client_ip`, P.`client_ua`, P.`currency`, P.`gross_amount`, P.`tax_amount`,
					P.`customer_fee` ";
	$query_from  = "From `#__payage_payments` AS P ";
	$query_where = "Where P.`pg_status_code` = 0 ";
	if ($search != '')
		$query_where .= $this->make_search($search);
	$query_order = " Order By P.`date_time_initiated` Desc";    

// get the total row count and initialise pagination

	$this->count_query = $query_count.$query_from.$query_where;
	$this->total_unconfirmed = $this->ladb_loadResult($this->count_query);
	if ($this->total_unconfirmed === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text,'error');
		return array();
		}

	if ($limitstart > $this->total_unconfirmed)
		$limitstart = 0;

	$this->pagination = new JPagination($this->total_unconfirmed, $limitstart, $limit);

// Get the data

	$this->main_query = $query_cols.$query_from.$query_where.$query_order;
	$this->data = $this->ladb_loadObjectList($this->main_query, $limitstart, $limit);
	if ($this->data === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text,'error');
		return array();
		}
	return $this->data;
}

//-------------------------------------------------------------------------------
// Make the where clause for the search string
//
function make_search($search)
{
// is it a transaction ID?

	if (PayageHelper::is_tid($search, false))
		{
		$tid = $this->_db->quote('%'.$search.'%');
		return " AND LOWER(P.`pg_transaction_id`) LIKE LOWER($tid)";
		}
		
// any other string searches first name, last name, email address and item_name

	$search_like = $this->_db->quote('%'.$search.'%');
	
	return " AND (LOWER(P.`payer_last_name`) LIKE LOWER($search_like)
				OR LOWER(P.`payer_first_name`) LIKE LOWER($search_like)
				OR LOWER(P.`payer_email`) LIKE LOWER($search_like)
				OR LOWER(P.`item_name`) LIKE LOWER($search_like)
				OR LOWER(P.`gw_transaction_id`) LIKE LOWER($search_like))";
}

//-------------------------------------------------------------------------------
// Get a list of all the applications that we have payments for
//
function get_app_array()
{
	$query = "Select Distinct(`app_name`) From `#__payage_payments` Where `pg_status_code` != ".LAPG_STATUS_NONE;
	$data = $this->ladb_loadObjectList($query);
	if ($data === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text,'error');
		return array();
		}
	$apps = array();
	$apps[0] = JText::_('COM_PAYAGE_ALL_APPLICATIONS');
	foreach ($data as $row)
		$apps[$row->app_name] = $row->app_name;
	return $apps;
}

//-------------------------------------------------------------------------------
// Get currencies that we have payments for
//
function get_currency_array($all=true)
{
    $query = "SELECT DISTINCT(`currency`) FROM `#__payage_payments`
        WHERE `pg_status_code` IN (".LAPG_STATUS_SUCCESS.", ".LAPG_STATUS_PENDING.")"."
        ORDER BY `currency`";
	$data = $this->ladb_loadObjectList($query);
	if ($data === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text,'error');
		return array();
		}
	$currencies = array();
    if ($all)
    	$currencies[0] = JText::_('COM_PAYAGE_ALL_CURRENCIES');
	foreach ($data as $row)
		$currencies[$row->currency] = $row->currency;
	return $currencies;
}

//-------------------------------------------------------------------------------
// Delete or anonymise payment records as configured
//
function purge()
{
	$params = JComponentHelper::getParams('com_payage');		// get component parameters	
	$time_to_keep_unconfirmed = $params->get('time_to_keep_unconfirmed', LAPG_DEFAULT_UNCONFIRMED);
	if (!is_numeric($time_to_keep_unconfirmed))
		return;
	$anonymise_days = $params->get('anonymise_days',0);
	if (!is_numeric($anonymise_days))
		return;
	$time_to_keep_confirmed = $params->get('time_to_keep_confirmed', LAPG_DEFAULT_CONFIRMED);
	if (!is_numeric($time_to_keep_confirmed))
		return;

	$query = "DELETE FROM `#__payage_payments` WHERE `pg_status_code` IN (".LAPG_STATUS_NONE.",".LAPG_STATUS_CANCELLED.") AND TIMESTAMPDIFF(MINUTE,`date_time_initiated`,CURRENT_TIMESTAMP) > $time_to_keep_unconfirmed";
	LAPG_trace::trace($query);
	$result = $this->ladb_execute($query);
	if ($result === false)
		LAPG_trace::trace($this->ladb_error_text);

	if ($anonymise_days != 0)
		{
		$query = "UPDATE `#__payage_payments` 
					SET `payer_email` = '', `client_ip` = '',
						`payer_first_name` = SUBSTRING(`payer_first_name`,1, 1), 
						`payer_last_name` = SUBSTRING(`payer_last_name`,1, 1), 
						`payer_address1` = '', `payer_address2` = '', `payer_zip_code` = '',
						`gw_transaction_details` = '', `app_transaction_details` = ''
					WHERE `pg_status_code` NOT IN (".LAPG_STATUS_NONE.",".LAPG_STATUS_CANCELLED.") 
						AND TIMESTAMPDIFF(DAY,`date_time_initiated`,CURRENT_TIMESTAMP) > $anonymise_days";
		LAPG_trace::trace($query);
		$result = $this->ladb_execute($query);
		if ($result === false)
			{
			PayageHelper::syslog(LAPG_LOG_DATABASE_ERROR, '', $query.'<br /><br />'.$this->ladb_error_text);
			LAPG_trace::trace($query."\n".$this->ladb_error_text);
			}
		}

	if ($time_to_keep_confirmed != 0)
		{
		$query = "DELETE FROM `#__payage_payments` WHERE `pg_status_code` NOT IN (".LAPG_STATUS_NONE.",".LAPG_STATUS_CANCELLED.") AND TIMESTAMPDIFF(DAY,`date_time_initiated`,CURRENT_TIMESTAMP) > $time_to_keep_confirmed";
		LAPG_trace::trace($query);
		$result = $this->ladb_execute($query);
		if ($result === false)
			{
			PayageHelper::syslog(LAPG_LOG_DATABASE_ERROR, '', $query.'<br /><br />'.$this->ladb_error_text);
			LAPG_trace::trace($query."\n".$this->ladb_error_text);
			}
		}
}

//-------------------------------------------------------------------------------
// add a new set of gateway transaction details to a payment record
// the first set of details is simply added as an object. The store() function will serialize it.
// subsequent sets of data are appended using the $info['Name'] if passed, or 'Update' if not, with a sequence number added if necessary
// e.g. "Webhook", "Webhook_2", "Webhook_3", etc
//
function add_transaction_details($obj, $info=array())
{
    if (!is_object($obj))
        $obj = json_decode(json_encode($obj), FALSE);   // make sure it's an object
        
	if (empty($this->data->gw_transaction_details))
		{
		$this->data->gw_transaction_details = $obj;
		return;
		}
	if (empty($info['Name']))
		$name = 'Update';
	else
		$name = $info['Name'];
	$varname = $name;
	for ($i=2; $i<99; $i++)
		{
		if (!isset($this->data->gw_transaction_details->$varname))
			break;
		$varname = $name.'_'.$i;
		}
	if ($i >= 99)
		{
		LAPG_trace::trace("Too many transaction details");
		return;
		}
    if (empty($info))
        $this->data->gw_transaction_details->$varname = $obj;
    else
        {
        $this->data->gw_transaction_details->$varname = new stdClass();
		foreach ($info as $name => $value)
			{
			if ($name == 'Name')
				continue;
			$this->data->gw_transaction_details->$varname->$name = $value;
			}
        $this->data->gw_transaction_details->$varname->data = $obj;
        }
}

//-------------------------------------------------------------------------------
// add an entry to the history field
//
function add_history_entry($text)
{
	if (!empty($this->data->pg_history))
		$this->data->pg_history .= "\n";
	$this->data->pg_history .= $this->data->now.' '.$text;
}

//-------------------------------------------------------------------------------
// export the currently selected payments
//
function export_list()
{
	$this->getList();	// we don't use the payments this gets, just the SQL it generates	
	
// we could check $this->count_query_total and refuse very large numbers, but for now we just get all the rows with no limits

	LAPG_trace::trace("Export list query: ".$this->main_query);

	$payments = $this->ladb_loadObjectList($this->main_query);
	if ($payments === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text,'error');
		return;
		}

// Headings

	$delim = ", ";
	$output = JText::_('COM_PAYAGE_DATE_TIME').$delim;
	$output .= JText::_('COM_PAYAGE_OUR_TRANSACTION_ID').$delim;
	$output .= JText::_('COM_PAYAGE_STATUS').$delim;
	$output .= JText::_('COM_PAYAGE_ITEM').$delim;
	$output .= JText::_('COM_PAYAGE_CURRENCY').$delim;
	$output .= JText::_('COM_PAYAGE_GROSS').$delim;
	$output .= JText::_('COM_PAYAGE_TAX').$delim;
	$output .= JText::_('COM_PAYAGE_TOTAL').$delim;
	$output .= JText::_('COM_PAYAGE_FEE_CUSTOMER').$delim;
	$output .= JText::_('COM_PAYAGE_FEE_GATEWAY').$delim;
	$output .= JText::_('COM_PAYAGE_PAYER_EMAIL').$delim;
	$output .= JText::_('COM_PAYAGE_FIRST_NAME').$delim;
	$output .= JText::_('COM_PAYAGE_LAST_NAME').$delim;
	$output .= JText::_('COM_PAYAGE_PAYER_ADDRESS').' 1'.$delim;
	$output .= JText::_('COM_PAYAGE_PAYER_ADDRESS').' 2'.$delim;
	$output .= 'City'.$delim;
	$output .= 'State'.$delim;
	$output .= 'Zip'.$delim;
	$output .= JText::_('COM_PAYAGE_PAYER_COUNTRY')."\r\n";
		
// build the output buffer
	
	foreach ($payments as $row)
		{
		$status = PayageHelper::getPaymentDescription($row->pg_status_code);
		$gross_amount = PayageHelper::format_amount($row->gross_amount, $row->currency_format);
		$tax_amount = PayageHelper::format_amount($row->tax_amount, $row->currency_format);
		$total_amount = $row->gross_amount + $row->customer_fee;
		$total_amount = PayageHelper::format_amount($total_amount, $row->currency_format);
		$customer_fee = PayageHelper::format_amount($row->customer_fee, $row->currency_format);
		$gateway_fee = PayageHelper::format_amount($row->gateway_fee, $row->currency_format);

		$output .= '"'.$row->date_time_initiated.'"'.$delim;
		$output .= '"'.$row->pg_transaction_id.'"'.$delim;
		$output .= '"'.$status.'"'.$delim;
		$output .= '"'.$row->item_name.'"'.$delim;
		$output .= '"'.$row->currency.'"'.$delim;
		$output .= '"'.$gross_amount.'"'.$delim;
		$output .= '"'.$tax_amount.'"'.$delim;
		$output .= '"'.$total_amount.'"'.$delim;
		$output .= '"'.$customer_fee.'"'.$delim;
		$output .= '"'.$gateway_fee.'"'.$delim;
		$output .= '"'.$row->payer_email.'"'.$delim;
		$output .= '"'.$row->payer_first_name.'"'.$delim;
		$output .= '"'.$row->payer_last_name.'"'.$delim;
		$output .= '"'.$row->payer_address1.'"'.$delim;
		$output .= '"'.$row->payer_address2.'"'.$delim;
		$output .= '"'.$row->payer_city.'"'.$delim;
		$output .= '"'.$row->payer_state.'"'.$delim;
		$output .= '"'.$row->payer_zip_code.'"'.$delim;
		$output .= '"'.$row->payer_country.'"'."\r\n";
		}

	LAPG_admin::file_download($output, 'payage_payments.csv', 'text/plain');	
	exit;
}

}