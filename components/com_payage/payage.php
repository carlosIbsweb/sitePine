<?php
/********************************************************************
Product		: Payage
Date		: 20 January 2022
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
	$app = JFactory::getApplication();

	LAPG_trace::trace_entry_point(true);

// this entry point should only be called by a payment gateway and should always include an account id and a transaction id
// However, occasionally PayPal only returns the first URL parameter.
// In order to recover from this, the PayPal addon places the AID and the TID in the 'custom' parameter ...

	$jinput = JFactory::getApplication()->input;
	$aid = $jinput->get('aid','', 'STRING');
	$tid = $jinput->get('tid','', 'STRING');

	if (($aid == '') || ($tid == ''))	    // if either is missing this is not a valid entry
        {                                   // however, we have seen some cases where PayPal returns without the expected parameters ...
		LAPG_trace::trace("PFE: Missing AID/TID");
        $custom = $jinput->get('custom','', 'STRING');          // do we have a 'custom' parameter? (IPN)
        $jinput->set('task','notify');                          // if 'custom' is set, this is an IPN notify
		LAPG_trace::trace(" - custom = $custom");
        if ($custom == '')
            {
            $custom = $jinput->get('cm','', 'STRING');          // do we have a 'cm' parameter? (website return)
            $jinput->set('task','return');                      // if 'cm' is set, this is a website return
    		LAPG_trace::trace(" - cm = $custom");
            }
        $ppta = substr($custom,0,4);            // first 4 characters should be 'PPTA'
        $tid = substr($custom,5,32);            // TID is next 32 characters after the comma
        $aid = substr($custom,38);              // AID is remaining chars
        if (($ppta != 'PPTA') || !PayageHelper::is_tid($tid) || !is_numeric($aid))
            {    
    		LAPG_trace::trace(" - can't recover");
	        http_response_code(400);
			exit;
            }
		LAPG_trace::trace("PFE: Recovered AID $aid, TID $tid");
        }

// check the aid

	if (!is_numeric($aid))
		{
		LAPG_trace::trace("PFE: Bad AID: $aid");
        http_response_code(400);    // bad request
		exit;
		}
	$account_model = new PayageModelAccount;
	$account_data = $account_model->getOne($aid);
	if ($account_data === false)
		{
		LAPG_trace::trace("PFE: Account $aid not found");
		http_response_code(400);
		exit;
		}
	if ($account_data->published != 1)
		{
		LAPG_trace::trace("PFE: Account $aid is unpublished");
		http_response_code(400);
		exit;
		}

// check the tid

	if (isset($account_model->specific_data->tid_optional))
		$tid_optional = true;
	else
		$tid_optional = false;
	if (!PayageHelper::is_tid($tid, $tid_optional))
		{
		LAPG_trace::trace("PFE: Bad TID: $tid");
        http_response_code(400);
		exit;
		}

// load the Payage language file from the back end

	$lang = JFactory::getLanguage();
	$lang->load('com_payage', JPATH_ADMINISTRATOR.'/components/com_payage');

// if we have a tid, get the payment
// webhook calls have tid=0 (and tid_optional==true)

	$payment_model = new PayageModelPayment;
	if ($tid != '0')
		{
		$payment_data = $payment_model->getOne($tid,'pg_transaction_id');
		if (empty($payment_data))
			{
			echo JText::_('COM_PAYAGE_TRANSACTION_NO_LONGER_VALID');	// Some AJAX calls use this
		    PayageHelper::syslog(LAPG_LOG_OTHER_ERROR, JText::sprintf('COM_PAYAGE_PAYMENT_X_NOT_FOUND', $tid), '', true);
	        http_response_code(400);
			return;
			}
		LAPG_trace::trace("PFE: Gateway_handle_request ".$payment_data->pg_transaction_id);
		}
	
// we have a valid request with a correct account id and transaction id
// get the gateway info for the relevant account and create an instance of the relevant model

	$gateway_model = PayageHelper::getGatewayInstance($account_data->gateway_type);
	if ($gateway_model === false)
		return;						// getGatewayInstance() has already traced the reason

// call the gateway model to handle the request

	if ($gateway_model->getOne($aid) === false)
		return;						// getOne() has already traced the reason
	
	$ret = $gateway_model->Gateway_handle_request($payment_model);
	$payment_data = $payment_model->data;				// for tid=0 we didn't get this earlier
	switch ($ret)
		{
		case LAPG_CALLBACK_NONE:		// end the gateway request with no external actions 
			return;
			
		case LAPG_CALLBACK_CANCEL;		// redirect the request to the Payage application that originated the transaction
		case LAPG_CALLBACK_USER:		// - this is used to return to the application after an online payment transaction 
			if (empty($payment_data->app_return_url))
				{
				LAPG_trace::trace("PFE: Callback $ret but no app_return_url");
				return;
				}
			if (strstr($payment_data->app_return_url,'?'))
				$url = $payment_data->app_return_url.'&tid='.$payment_data->pg_transaction_id;
			else
				$url = $payment_data->app_return_url.'?tid='.$payment_data->pg_transaction_id;
			LAPG_trace::trace('PFE: Redirecting to '.$url);
			$app->redirect($url);
			return;
			
		case LAPG_CALLBACK_UPDATE:		// call an external PHP file, for example after a notification from a gateway
			if (empty($payment_data->app_update_path))
				{
				LAPG_trace::trace("PFE: Update callback but no app_update_path");
				return;
				}
			if (!file_exists($payment_data->app_update_path))
				{
				LAPG_trace::trace("PFE: Update callback cannot find file ".$payment_data->app_update_path.' - pg_transaction_id: '.$payment_data->pg_transaction_id);
			    PayageHelper::syslog(LAPG_LOG_OTHER_ERROR, "Update callback cannot find file ".$payment_data->app_update_path, "Transaction ID: ".$payment_data->pg_transaction_id);
				$payment_model->add_history_entry("App Update: no file");
				$payment_model->store();
				return;
				}
			require_once $payment_data->app_update_path;
			if (!function_exists('payment_update'))
				{
				LAPG_trace::trace("PFE: Update callback cannot find payment_update() function in ".$payment_data->app_update_path.' - pg_transaction_id: '.$payment_data->pg_transaction_id);
			    PayageHelper::syslog(LAPG_LOG_OTHER_ERROR, "Update callback cannot find payment_update() function in ".$payment_data->app_update_path, "Transaction ID: ".$payment_data->pg_transaction_id);
				$payment_model->add_history_entry("App Update: no function");
				$payment_model->store();
				return;
				}
			LAPG_trace::trace("PFE: Calling payment_update() for tid ".$payment_data->pg_transaction_id.' in file: '.$payment_data->app_update_path);
			$payment_model->add_history_entry("App Update: ".$payment_data->app_name);
			$payment_model->store();
			payment_update($payment_data->pg_transaction_id);
			return;

		case LAPG_CALLBACK_BAD:      // the gateway detected a malformed or illegal request
			http_response_code(400);
			exit;

		default:
			return;
		}

// can't get here