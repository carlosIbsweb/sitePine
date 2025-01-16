<?php
/********************************************************************
Product		: Payage
Date		: 5 August 2022
Copyright	: Les Arbres Design 2014-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

require_once JPATH_ADMINISTRATOR.'/components/com_payage/helpers/payage_helper.php';
require_once JPATH_ADMINISTRATOR.'/components/com_payage/helpers/db_helper.php';
require_once JPATH_ADMINISTRATOR.'/components/com_payage/helpers/trace_helper.php';
require_once JPATH_ADMINISTRATOR.'/components/com_payage/models/account.php';
require_once JPATH_ADMINISTRATOR.'/components/com_payage/models/payment.php';

class PayageApi
{

//-------------------------------------------------------------------------------
// Return an array of payment buttons
//
static function Get_payment_buttons($call_array)
{
    $lang = JFactory::getLanguage('JPATH_SITE');
	$tag = $lang->get('tag');               // get current language
	LAPG_trace::trace("Get_payment_buttons called with language $tag and ".print_r($call_array,true));
    
	$return_array = array();

// check the mandatory parameters

	if (empty($call_array['currency']) || !preg_match('/^[A-Z]{3}$/', $call_array['currency']))
		{
		$return_array[0]['status'] = 2;
		$return_array[0]['error'] = "Get_payment_buttons() called with empty or invalid currency";
		LAPG_trace::trace($return_array[0]['error']);
		return $return_array;
		}

	if (empty($call_array['app_name']))
		{
		$return_array[0]['status'] = 3;
		$return_array[0]['error'] = "Get_payment_buttons() called with no app_name";
		LAPG_trace::trace($return_array[0]['error']);
		return $return_array;
		}

	if (empty($call_array['gross_amount']))
		$call_array['gross_amount'] = 0;

	if (empty($call_array['tax_amount']))
		$call_array['tax_amount'] = 0;

	if (!is_numeric($call_array['gross_amount']) || ($call_array['gross_amount'] < 0))
		$call_array['gross_amount'] = 0;

	if (!is_numeric($call_array['tax_amount']) || ($call_array['tax_amount'] < 0))
		$call_array['tax_amount'] = 0;

	if ($call_array['tax_amount'] > $call_array['gross_amount'])
		{
		$return_array[0]['status'] = 7;
		$return_array[0]['error'] = "Get_payment_buttons() called with tax_amount > gross_amount";
		LAPG_trace::trace($return_array[0]['error']);
		return $return_array;
		}

	if (empty($call_array['app_return_url']) || (strlen($call_array['app_return_url']) > LAPG_MAX_VARCHAR_LENGTH))
		{
		$return_array[0]['status'] = 5;
		$return_array[0]['error'] = "Get_payment_buttons() called with empty or invalid app_return_url";
		LAPG_trace::trace($return_array[0]['error']);
		return $return_array;
		}

// default the optional parameters

	if (empty($call_array['group']))
		$call_array['group'] = 0;
	if (empty($call_array['item_name']))
		$call_array['item_name'] = '';
	if (empty($call_array['app_update_path']))
		$call_array['app_update_path'] = '';
	if (empty($call_array['app_transaction_id']))
		$call_array['app_transaction_id'] = '';
	if (empty($call_array['app_transaction_details']))
		$call_array['app_transaction_details'] = '';
	if (empty($call_array['firstname']))
		$call_array['firstname'] = '';
	if (empty($call_array['lastname']))
		$call_array['lastname'] = '';
	if (empty($call_array['address1']))
		$call_array['address1'] = '';
	if (empty($call_array['address2']))
		$call_array['address2'] = '';
	if (empty($call_array['city']))
		$call_array['city'] = '';
	if (empty($call_array['state']))
		$call_array['state'] = '';
	if (empty($call_array['zip_code']))
		$call_array['zip_code'] = '';
	if (empty($call_array['country']))		// country name
		$call_array['country'] = '';
	if (empty($call_array['country_code']))
		$call_array['country_code'] = '';
	if (strlen($call_array['country']) == 2)
		$call_array['country_code'] = $call_array['country'];
	if (empty($call_array['email']))
		$call_array['email'] = '';
	if (empty($call_array['button_extra']))
		$call_array['button_extra'] = '';
	if (empty($call_array['max_buttons']))
		$call_array['max_buttons'] = 0;

	$call_array['firstname'] = substr($call_array['firstname'],0,LAPG_MAX_NAME_LENGTH);
	$call_array['lastname'] = substr($call_array['lastname'],0,LAPG_MAX_NAME_LENGTH);
	$call_array['address1'] = substr($call_array['address1'],0,LAPG_MAX_ADDRESS_LENGTH);
	$call_array['address2'] = substr($call_array['address2'],0,LAPG_MAX_ADDRESS_LENGTH);
	$call_array['city'] = substr($call_array['city'],0,LAPG_MAX_CITY_LENGTH);
	$call_array['state'] = substr($call_array['state'],0,LAPG_MAX_STATE_LENGTH);
	$call_array['country'] = substr($call_array['country'],0,LAPG_MAX_COUNTRY_LENGTH);
	$call_array['country_code'] = substr($call_array['country_code'],0,2);
	$call_array['zip_code'] = substr($call_array['zip_code'],0,LAPG_MAX_ZIPCODE_LENGTH);
	$call_array['email'] = substr($call_array['email'],0,LAPG_MAX_EMAIL_LENGTH);
	$call_array['item_name'] = substr($call_array['item_name'],0,LAPG_MAX_VARCHAR_LENGTH);
	$call_array['app_update_path'] = substr($call_array['app_update_path'],0,LAPG_MAX_VARCHAR_LENGTH);
	$call_array['app_transaction_id'] = substr($call_array['app_transaction_id'],0,LAPG_MAX_VARCHAR_LENGTH);
	$call_array['app_name'] = substr($call_array['app_name'],0,LAPG_MAX_VARCHAR_LENGTH);

// load the main Payage language file (we are running in the context of the calling component here)

	PayageHelper::loadLanguageFile();

// get the list of accounts that match the group and currency of the call

	$account_model = new PayageModelAccount;
	$account_list = $account_model->getAccounts($call_array['group'],$call_array['currency']);
	
// if no accounts match, return without storing the transaction

	if (empty($account_list))
		{
		$return_array[0]['status'] = 1;
		$return_array[0]['error'] = JText::sprintf('COM_PAYAGE_NO_MATCHING_ACCOUNTS',$call_array['currency'],$call_array['group']);
		LAPG_trace::trace($return_array[0]['error']);
		return $return_array;
		}

// generate a unique transaction number and store the transaction

	$pg_transaction_id = md5(uniqid(mt_rand(),true));
	$payment_model = new PayageModelPayment;
	$payment_model->purge();					// purge any old unconfirmed transactions
	$payment_model->initData();
	$payment_model->data->app_name = $call_array['app_name'];
	$payment_model->data->item_name = $call_array['item_name'];
	$payment_model->data->app_return_url = $call_array['app_return_url'];
	$payment_model->data->app_update_path = $call_array['app_update_path'];
	$payment_model->data->app_transaction_id = $call_array['app_transaction_id'];
	$payment_model->data->app_transaction_details = $call_array['app_transaction_details'];
	$payment_model->data->pg_transaction_id = $pg_transaction_id;
	$payment_model->data->currency = $call_array['currency'];
	$payment_model->data->gross_amount = $call_array['gross_amount'];
	$payment_model->data->tax_amount = $call_array['tax_amount'];
	$payment_model->data->payer_first_name = $call_array['firstname'];
	$payment_model->data->payer_last_name = $call_array['lastname'];
	$payment_model->data->payer_address1 = $call_array['address1'];
	$payment_model->data->payer_address2 = $call_array['address2'];
	$payment_model->data->payer_city = $call_array['city'];
	$payment_model->data->payer_state = $call_array['state'];
	$payment_model->data->payer_zip_code = $call_array['zip_code'];
	$payment_model->data->payer_country = $call_array['country'];
	$payment_model->data->payer_country_code = $call_array['country_code'];
	$payment_model->data->payer_email = $call_array['email'];
	$ret = $payment_model->store();
	if ($ret === false)
		{
		$return_array[0]['status'] = 6;
		$return_array[0]['error'] = "Get_payment_buttons() failed to store the transaction ".$payment_model->ladb_error_text;
		LAPG_trace::trace($return_array[0]['error']);
		return $return_array;
		}

// get all the information about the installed gateways

	$gateway_list = $account_model->getGatewayList();

// generate a payment button for each matching account	

	$return_array[0]['status'] = 0;
	$return_array[0]['error'] = '';
	$return_array[0]['transaction_id'] = $pg_transaction_id;
	$i = 1;
	foreach ($account_list as $account_data)
		{
		$gateway_type = $account_data->gateway_type;
		if (!isset($gateway_list[$gateway_type]))
			continue;										// gateway type not installed
		$gateway_model = PayageHelper::getGatewayInstance($gateway_type);
		if ($gateway_model === false)
			continue;										// gateway not properly installed
		$gateway_model->getOne($account_data->id);
		$app_fee = $gateway_model::calculate_gateway_fee($gateway_model->common_data, $payment_model->data->gross_amount);
		$return_array[$i]['type'] = $gateway_list[$gateway_type]['shortName'];
		$button = $gateway_model->Gateway_make_button($payment_model->data, $call_array, $app_fee);
		$return_array[$i]['button'] = $button;
		$return_array[$i]['description'] = $gateway_model->common_data->account_description;
		$return_array[$i]['status'] = 0;
		$return_array[$i]['error'] = '';
		if ($app_fee == 0)
			$return_array[$i]['fee'] = '';
		else
			$return_array[$i]['fee'] = PayageHelper::format_amount($app_fee, $account_data->currency_format, $account_data->currency_symbol);
		if (substr($button,0,1) == '-')
			{
			$return_array[$i]['status'] = substr($button,1.2);
			$return_array[$i]['error'] = substr($button,4);			// the button text has details of the error
			LAPG_trace::trace($return_array[$i]['error']);
			}
		if ($i == $call_array['max_buttons'])
			break;
		$i ++;
		}
	LAPG_trace::trace("Get_payment_buttons() returning ".print_r($return_array,true));
	return $return_array;
}

//-------------------------------------------------------------------------------
// Return payment data for a payment
// - default index is the payage transaction id, but it can be any field
//
static function Get_Payment_Data($index, $field='pg_transaction_id')
{
	$payment_model = new PayageModelPayment;
	if ($payment_model->getOnePlus($index, $field) === false)
		{
		if (isset($payment_model->ladb_error_text))
			$error = $payment_model->ladb_error_text;
		else
			$error = "Payment not found";
		LAPG_trace::trace($error);
		return false;
		}
	$payment_data = $payment_model->data;
	$payment_model->data->formatted_gross_amount = PayageHelper::format_amount($payment_data->gross_amount, $payment_data->currency_format, $payment_data->currency_symbol);
	return $payment_data;
}

//-------------------------------------------------------------------------------
// Change the state of a payment
//
static function Change_Payment_State($tid, $new_state, $app_name)
{
	if (($new_state < LAPG_STATUS_NONE) or ($new_state > LAPG_STATUS_MAX))
		{
		LAPG_trace::trace("API: Change_Payment_State() incorrect new state ".$new_state);
		return false;
		}
	if (empty($app_name))
		{
		LAPG_trace::trace("API: Change_Payment_State() app_name is empty");
		return false;
		}
		
	PayageHelper::loadLanguageFile(); // we are running in the context of the calling component
	
	$payment_model = new PayageModelPayment;
	
	if ($payment_model->getOne($tid,'pg_transaction_id') === false)
		{
		LAPG_trace::trace("API: Change_Payment_State() failed to get payment ".$tid);
		return false;
		}
	$app_name = $app_name.': ';
	return $payment_model->change_status($new_state, $app_name);
}

//-------------------------------------------------------------------------------
// Mark a payment as processed by the application
//
static function Set_Payment_Processed($tid, $value)
{
	if (empty($value))
		{
		LAPG_trace::trace("API: Set_Payment_Processed() value is empty");
		return false;
		}

	PayageHelper::loadLanguageFile(); 	// $payment_model->set_processed() needs the language file
	
	$payment_model = new PayageModelPayment;
	
	if ($payment_model->getOne($tid,'pg_transaction_id') === false)
		{
		LAPG_trace::trace("API: Set_Payment_Processed() failed to get payment ".$tid);
		return false;
		}
	return $payment_model->set_processed($value);
}

//-------------------------------------------------------------------------------
// Set the app_transaction_id for a payment
//
static function Set_App_Transaction_Id($tid, $value)
{
	if (empty($value))
		{
		LAPG_trace::trace("API: Set_App_Transaction_Id() value is empty");
		return false;
		}
	
	$payment_model = new PayageModelPayment;
	
	if ($payment_model->getOne($tid,'pg_transaction_id') === false)
		{
		LAPG_trace::trace("API: Set_App_Transaction_Id() failed to get payment ".$tid);
		return false;
		}
	return $payment_model->set_app_transaction_id($value);
}

//-------------------------------------------------------------------------------
// Return a list of currency codes that we have accounts for
//
static function Get_Currencies($group)
{
	$account_model = new PayageModelAccount;
	$account_list = $account_model->get_currency_array($group);
	return $account_list;
}

//-------------------------------------------------------------------------------
// Return a list of account groups that we have accounts for
//
static function Get_Groups($include_all)
{
	$account_model = new PayageModelAccount;
	$account_list = $account_model->getGroups($include_all);
	return $account_list;
}

//-------------------------------------------------------------------------------
// Return the description of a status value
//
static function Get_Status_Description($status_value)
{
	PayageHelper::loadLanguageFile(); // we are running in the context of the calling component
	return PayageHelper::getPaymentDescription($status_value);
}

//-------------------------------------------------------------------------------
// Return our version number
//
static function Get_Version()
{
	return PayageHelper::getComponentVersion();
}

}