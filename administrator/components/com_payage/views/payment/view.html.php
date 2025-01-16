<?php
/********************************************************************
Product     : Payage
Date		: 5 August 2022
Copyright	: Les Arbres Design 2014-2022
Contact     : https://www.lesarbresdesign.info
Licence     : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class PayageViewPayment extends JViewLegacy
{

//-------------------------------------------------------------------------------
// Show the list of payments
//
function display($tpl = null)
{
    LAPG_admin::addSubMenu('payment');
	JToolBarHelper::title('Payage: '.JText::_('COM_PAYAGE_PAYMENTS'), 'lad.png');
	JToolBarHelper::deleteList();
	if (!empty($this->payment_list))
		JToolbarHelper::custom('export', 'download.png', 'download.png', 'COM_PAYAGE_EXPORT', false);
    if (JFactory::getUser()->authorise('core.admin', LAPG_COMPONENT))
		JToolBarHelper::preferences('com_payage');

	if ($this->payment_list === false)
		return;	// the db is broken so don't try to do anything

    LAPG_admin::viewStart();
	
// get the order states				

	$this->app = JFactory::getApplication();
	$filter_order = $this->app->getUserStateFromRequest('com_payage.payment_filter_order', 'filter_order', 'DATE_TIME');
	$filter_order_Dir = $this->app->getUserStateFromRequest('com_payage.payment_filter_order_Dir', 'filter_order_Dir', 'DESC');

// get the filters	
		
	$search            = $this->app->getUserStateFromRequest('com_payage.payment_search','search','','RAW');
    $one_month_future  = date('Y-m-d', strtotime('+1 month'));
    $one_week_ago      = date('Y-m-d', strtotime('-1 week'));
	$filter_start_date = $this->app->getUserStateFromRequest('com_payage.payment_filter_start_date','filter_start_date',$one_week_ago,'STRING');
	$filter_end_date   = $this->app->getUserStateFromRequest('com_payage.payment_filter_end_date','filter_end_date',$one_month_future,'STRING');
	$filter_app        = $this->app->getUserStateFromRequest('com_payage.payment_filter_app','filter_app','0','STRING');
	$filter_currency   = $this->app->getUserStateFromRequest('com_payage.payment_filter_currency','filter_currency','0','string');
	$filter_account    = $this->app->getUserStateFromRequest('com_payage.payment_filter_account','filter_account',0,'int');

// make the filter lists

	$start_date_html = LAPG_admin::make_date_picker('filter_start_date', $filter_start_date);
	$end_date_html   = LAPG_admin::make_date_picker('filter_end_date', $filter_end_date);

    if (count($this->app_list) > 2)         // 2 items in the list would be "All" and one application - so don't show the selector
        $app_filter_html = LAPG_admin::make_list('filter_app', $filter_app, $this->app_list, 'onchange="this.form.submit();"');
    else
        $app_filter_html = '<input type="hidden" name="filter_app" id="filter_app" value="0" />';   // so we don't break the reset javascript

    if (count($this->currency_list) > 2)
    	$currency_filter_html = LAPG_admin::make_list('filter_currency', $filter_currency, $this->currency_list, 'onchange="this.form.submit();"');
    else
        $currency_filter_html = '<input type="hidden" name="filter_currency" id="filter_currency" value="0" />';
        
    if (count($this->account_list) > 2)
    	$account_filter_html = LAPG_admin::make_list('filter_account', $filter_account, $this->account_list, 'onchange="this.form.submit();"');					
    else
        $account_filter_html = '<input type="hidden" name="filter_account" id="filter_account" value="0" />';

	$numrows = count($this->payment_list);
	JHtml::_('bootstrap.popover', 'span.hasPopover', ['trigger' => 'hover focus']);

// Show the list of payments
// the onsubmit sets the task back to blank after an export, so that the form continues to work normally after the download

    $onsubmit = ' onsubmit="if (this.task.value==\'export\') setTimeout(function (){document.getElementById(\'task\').value=\'\'}, 2000);" ';
	echo '<form method="post" name="adminForm" id="adminForm" class="lad-filterform"'.$onsubmit.'>';

	?>
	<input type="hidden" name="option" value="com_payage" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="payment" />
	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
    <?php
    
    echo '<div>&nbsp;<div class="lad-filterform-left">'; 
	$icon = '<span class="icon-search"></span>';
	echo '<span class="hasPopover" title="'.JText::_('JSEARCH_FILTER').'" '.LAPG_BS_DATA_CONTENT.'="'.JText::_('COM_PAYAGE_SEARCH_DESC').'">'.$icon.'</span>';
    echo ' <input type="text" class="form-control lad-input-inline input-medium" name="search" id="search" value="'.$search.'" /> ';
    echo JText::_('COM_PAYAGE_FROM_DATE').' '.$start_date_html.' '.JText::_('COM_PAYAGE_TO_DATE').' '.$end_date_html;
    echo ' <button type="button" class="btn btn-primary" onclick="this.form.submit();">'.JText::_('COM_PAYAGE_GO').'</button>';
	echo '</div>'; 
	echo '<div class="lad-filterform-right">';
    echo $app_filter_html.' '.$currency_filter_html.' '.$account_filter_html;
    echo ' <button type="button" class="btn btn-primary" onclick="'."
		document.adminForm.search.value='';
		document.adminForm.filter_start_date.value='".$one_week_ago."';
		document.adminForm.filter_end_date.value='".$one_month_future."';
		document.adminForm.filter_app.value='0';
		document.adminForm.filter_account.value='0';
		document.adminForm.filter_currency.value='0';            
		if (typeof(document.adminForm.limitstart) != 'undefined')
			document.adminForm.limitstart.value=0;
		document.adminForm.filter_order.value='DATE_TIME';
		document.adminForm.filter_order_Dir.value='DESC';
        this.form.submit();".'">'.JText::_('JSEARCH_RESET').'</button>';
	echo '</div></div>';

// determine the columns we need

	$column_tax = false;
	$column_customer_fee = false;
	$column_gateway_fee = false;
	$column_app_name = false;
	$app_names = array();
	foreach ($this->payment_list as $row)
		{
		if (!empty($row->tax_amount))
			$column_tax = true;
		if (!empty($row->customer_fee))
			$column_customer_fee = true;
		if (!empty($row->gateway_fee))
			$column_gateway_fee = true;
		$app_names[$row->app_name] = $row->app_name;
		}
	if (count($app_names) > 1)
		$column_app_name = true;

	echo '<div class="lad-scroll-container">';
	echo '<table class="table table-striped">';
	echo '<thead><tr>';
    echo '<th style="width:20px;text-align:center"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>';
    echo '<th>'.JHtml::_('grid.sort', 'COM_PAYAGE_DATE_TIME', 'DATE_TIME', $filter_order_Dir, $filter_order).'</th>';
	if ($column_app_name)
	    echo '<th>'.JHtml::_('grid.sort', 'COM_PAYAGE_APPLICATION', 'APP_NAME', $filter_order_Dir, $filter_order).'</th>';
    echo '<th>'.JHtml::_('grid.sort', 'COM_PAYAGE_ITEM', 'ITEM_NAME', $filter_order_Dir, $filter_order).'</th>';
    echo '<th>'.JText::_('COM_PAYAGE_CURRENCY').'</th>';
    echo '<th>'.JText::_('COM_PAYAGE_GROSS').'</th>';
	if ($column_tax)
	    echo '<th>'.JText::_('COM_PAYAGE_TAX').'</th>';
	if ($column_customer_fee)
	    echo '<th>'.JText::_('COM_PAYAGE_FEE_CUSTOMER').'</th>';
	if ($column_gateway_fee)
	    echo '<th>'.JText::_('COM_PAYAGE_FEE_GATEWAY').'</th>';
    echo '<th>'.JHtml::_('grid.sort', 'COM_PAYAGE_ACCOUNT_NAME', 'ACCOUNT_NAME', $filter_order_Dir, $filter_order).'</th>';
    echo '<th>'.JHtml::_('grid.sort', 'COM_PAYAGE_PAYER_NAME', 'PAYER_NAME', $filter_order_Dir, $filter_order).'</th>';
    echo '<th>'.JHtml::_('grid.sort', 'COM_PAYAGE_PAYER_EMAIL', 'EMAIL', $filter_order_Dir, $filter_order).'</th>';
    echo '<th>'.JText::_('COM_PAYAGE_STATUS').'</th>';
	echo '</tr></thead>';

	echo '<tbody>';
	for ($i=0; $i < $numrows; $i++) 
		{
		$row = $this->payment_list[$i];
		$link = LAPG_COMPONENT_LINK.'&task=detail&controller=payment&cid[]='.$row->id;
		switch ($row->pg_status_code)
			{
			case LAPG_STATUS_SUCCESS:
				$status = '<span class="icon-checkmark-2" style="color:green;font-size:larger;margin-right:.5em"></span>'; break;				
			case LAPG_STATUS_PENDING:
				$status = '<span class="icon-pause-circle" style="color:orange;font-size:larger;margin-right:.5em"></span>'; break;
			case LAPG_STATUS_REFUNDED:
				$status = '<span class="icon-reply" style="color:firebrick;font-size:larger;margin-right:.5em"></span>'; break;
			case LAPG_STATUS_FAILED:
				$status = '<span class="icon-cancel-2" style="color:firebrick;font-size:larger;margin-right:.5em"></span>'; break;				
			default:
				$status = '<span class="icon-warning" style="color:firebrick;font-size:larger;margin-right:.5em"></span>'; break;
			}
		$status .= ' '.PayageHelper::getPaymentDescription($row->pg_status_code);

		echo '<tr>';
		echo '<td style="text-align:center;">'.JHtml::_('grid.id', $i, $row->id).'</td>';
		echo '<td>'.JHtml::link($link, $row->date_time_initiated).'</td>';
		if ($column_app_name)
			echo '<td class="lad-break-word">'.$row->app_name.'</td>';
		echo '<td class="lad-break-word">'.$row->item_name.'</td>';
		echo '<td>'.$row->currency.'</td>';
		echo '<td>'.PayageHelper::format_amount($row->gross_amount, $row->currency_format, $row->currency_symbol).'</td>';
		if ($column_tax)
			echo '<td>'.PayageHelper::format_amount($row->tax_amount, $row->currency_format, $row->currency_symbol).'</td>';
		if ($column_customer_fee)
			echo '<td>'.PayageHelper::format_amount($row->customer_fee, $row->currency_format, $row->currency_symbol).'</td>';
		if ($column_gateway_fee)
			echo '<td>'.PayageHelper::format_amount($row->gateway_fee, $row->currency_format, $row->currency_symbol).'</td>';
		echo '<td class="lad-break-word">'.$row->account_name.'</td>';
		echo '<td class="lad-break-word">'.$row->payer_first_name.' '.$row->payer_last_name.'</td>';
		echo '<td class="lad-break-word">'.$row->payer_email.'</td>';
		echo '<td class="lad-break-word">'.$status.'</td>';
		echo "</tr>\n";
		}
	echo '</tbody>';
	echo '<tfoot><tr><td colspan="15">'.$this->pagination->getListFooter().'</td></tr></tfoot>';	
	echo '</table></div></form>';
    LAPG_admin::viewEnd();
}

//-------------------------------------------------------------------------------
// Show the list of unconfirmed payments
// These are payments where one or more buttons have been created but not yet clicked
// We don't know which button will be clicked, if any, so there is no gateway associated with these payments yet
//
function unconfirmed()
{
    LAPG_admin::addSubMenu('unconfirmed');
	JToolBarHelper::title('Payage: '.JText::_('COM_PAYAGE_UNCONFIRMED_PAYMENTS'), 'lad.png');
	JToolBarHelper::custom('unconfirmed', 'refresh.png', '', 'COM_PAYAGE_REFRESH',false);
	JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');

	if ($this->payment_list === false)
		return;	// the db is broken so don't try to do anything
	
    LAPG_admin::viewStart();
    
	$this->app = JFactory::getApplication();
	$search = $this->app->getUserStateFromRequest('com_payage.payment_search','search','','RAW');
	$numrows = count($this->payment_list);
	$params = JComponentHelper::getParams('com_payage');		// get component parameters	
	$time_to_keep_unconfirmed = $params->get('time_to_keep_unconfirmed', LAPG_DEFAULT_UNCONFIRMED);
    $msg = JText::sprintf('COM_PAYAGE_UNCONFIRMED_STATUS', $this->total_unconfirmed, $time_to_keep_unconfirmed);
	JHtml::_('bootstrap.popover', 'span.hasPopover', ['trigger' => 'hover focus']);

// Show the list of unconfirmed payments

	?>
	<form method="get" name="adminForm" id="adminForm" class="lad-filterform">
	<input type="hidden" name="option" value="com_payage" />
	<input type="hidden" name="task" value="unconfirmed" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="payment" />
	<?php

    echo '<div>&nbsp;<div class="lad-filterform-left">'; 
	$icon = '<span class="icon-search"></span>';
	echo '<span class="hasPopover" title="'.JText::_('JSEARCH_FILTER').'" '.LAPG_BS_DATA_CONTENT.'="'.$msg.'">'.$icon.'</span>';
    echo ' <input type="text" class="form-control lad-input-inline input-large" name="search" id="search" value="'.$search.'" /> ';
    echo ' <button type="button" class="btn btn-primary" onclick="this.form.submit();">'.JText::_('COM_PAYAGE_GO').'</button>';
	echo '</div>'; 
	echo '<div class="lad-filterform-right">';
    echo ' <button type="button" class="btn btn-primary" onclick="'."
		document.adminForm.search.value='';
		if (typeof(document.adminForm.limitstart) != 'undefined')
			document.adminForm.limitstart.value=0;
        this.form.submit();".'">'.JText::_('JSEARCH_RESET').'</button>';
	echo '</div></div>';

	?>
	<table class="table table-striped">
	<thead><tr>
    <th><?php echo JText::_('COM_PAYAGE_DATE_TIME'); ?></th>
    <th><?php echo JText::_('COM_PAYAGE_APPLICATION'); ?></th>
    <th><?php echo JText::_('COM_PAYAGE_ITEM'); ?></th>
    <th><?php echo JText::_('COM_PAYAGE_GROSS'); ?></th>
    <th><?php echo JText::_('COM_PAYAGE_IP_ADDRESS'); ?></th>
    <th><?php echo JText::_('COM_PAYAGE_COUNTRY'); ?></th>
    <th><?php echo JText::_('COM_PAYAGE_BROWSER'); ?></th>
	</tr></thead>

	<tfoot><tr><td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
	
	<tbody>
	<?php

	for ($i=0; $i < $numrows; $i++) 
		{
		$row = $this->payment_list[$i];
		$gross_amount = PayageHelper::format_amount($row->gross_amount);
        $browser = self::getBrowser($row->client_ua);
		$link = LAPG_COMPONENT_LINK.'&task=unconfirmed_detail&controller=payment&cid[]='.$row->id;
		$date = JHtml::link($link, $row->date_time_initiated);
        if (empty($row->account_name))
            $account_name = JText::_('JNONE');
        else
            $account_name = $row->account_name;

		echo "<tr>".
				'<td style="white-space:nowrap;">'.$date.'</td>'.
				"<td>$row->app_name</td>
				<td>$row->item_name</td>
				<td>$row->currency $gross_amount</td>
				<td>$row->client_ip</td>
				<td>$row->payer_country_code</td>
				<td>$browser</td>
				</tr>\n";
		}
	echo '</tbody></table></form>';
    LAPG_admin::viewEnd();
}

//-------------------------------------------------------------------------------
// View a single payment
//
function edit()
{
    LAPG_admin::viewStart();
	JToolBarHelper::title('Payage: '.JText::_('COM_PAYAGE_PAYMENT_DETAILS'), 'lad.png');
	
	if ( ($this->payment_data->pg_status_code != LAPG_STATUS_REFUNDED) && ($this->payment_data->pg_status_code != LAPG_STATUS_NONE) )
		JToolBarHelper::custom('status_refund', 'undo.png', 'undo_f2.png', 'COM_PAYAGE_REFUND', false);
		
	if ( ($this->payment_data->pg_status_code != LAPG_STATUS_SUCCESS) && ($this->payment_data->pg_status_code != LAPG_STATUS_NONE) )
		JToolBarHelper::custom('status_success', 'publish.png', 'publish_f2.png', 'COM_PAYAGE_SUCCESS', false);
		
	if ( ($this->payment_data->pg_status_code != LAPG_STATUS_PENDING) && ($this->payment_data->pg_status_code != LAPG_STATUS_NONE) )
		JToolBarHelper::custom('status_pending', 'pin.png', 'pin_f2.png', 'COM_PAYAGE_PENDING', false);
		
	if ( ($this->payment_data->pg_status_code != LAPG_STATUS_FAILED) && ($this->payment_data->pg_status_code != LAPG_STATUS_NONE) )
		JToolBarHelper::custom('status_failed', 'unpublish.png', 'unpublish_f2.png', 'COM_PAYAGE_FAILED', false);
		
	JToolBarHelper::custom('download', 'download.png', 'download.png', JText::_('COM_PAYAGE_DOWNLOAD'), false);

    if ($this->payment_data->pg_status_code == LAPG_STATUS_NONE)    
    	JToolBarHelper::cancel('cancel_unconfirmed','JTOOLBAR_CLOSE');
    else
    	JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');

// the gateway may have been deleted since the payment was made

	if (!empty($this->gateway_info))
		$gateway_shortName = $this->gateway_info['shortName'];
	else
		$gateway_shortName = '<span class="lad_error_msg">'.JText::_('COM_PAYAGE_GATEWAY_NOT_INSTALLED').'</span>';

	?>
	<form method="post" name="adminForm" id="adminForm" class="lad-admin-form">
	<input type="hidden" name="option" value="com_payage" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="payment" />
	<input type="hidden" name="id" value="<?php echo $this->payment_data->id; ?>" />
	<?php

// common data

	echo '<fieldset class="lad-fieldset lad-border width-auto lad-left payment-fieldset">';
	echo '<legend>'.JText::_('COM_PAYAGE_PAYMENT_DETAILS').'</legend>';

	echo "\n".'<table class="payment_details">';
		echo '<tr><td>'.JText::_('COM_PAYAGE_DATE_TIME').'</td><td>'.$this->payment_data->date_time_initiated.'</td></tr>';

		if ($this->payment_data->date_time_updated == '0000-00-00 00:00:00')
			$date_time_updated = '';
		else
			$date_time_updated = $this->payment_data->date_time_updated;
		echo '<tr><td>'.JText::_('COM_PAYAGE_UPDATED').'</td><td>'.$date_time_updated.'</td></tr>';

		echo '<tr><td>'.JText::_('COM_PAYAGE_ACCOUNT_NAME').'</td><td>'.$this->account_data->account_name.'</td></tr>';

		echo '<tr><td>'.JText::_('COM_PAYAGE_OUR_TRANSACTION_ID').'</td><td>'.$this->payment_data->pg_transaction_id.'</td></tr>';

        echo '<tr><td>'.JText::_('COM_PAYAGE_GATEWAY_TYPE').'</td><td>'.$gateway_shortName.'</td></tr>';
		
		if ($this->payment_data->pg_status_code == LAPG_STATUS_FAILED)
			$css_class = ' class="lad_error_msg"';
		else
			$css_class = '';
		echo '<tr><td>'.JText::_('COM_PAYAGE_STATUS')."</td><td $css_class>".PayageHelper::getPaymentDescription($this->payment_data->pg_status_code).'</td></tr>';
		
		if (!empty($this->payment_data->pg_status_text))
			echo '<tr><td style="vertical-align:top">'.JText::_('COM_PAYAGE_STATUS_DETAILS')."</td><td $css_class>".$this->payment_data->pg_status_text.'</td></tr>';

		if (($this->payment_data->pg_status_code == LAPG_STATUS_PENDING) && (!empty($this->payment_data->gw_pending_reason)))
			echo '<tr><td>'.JText::_('COM_PAYAGE_PENDING_REASON').'</td><td>'.$this->payment_data->gw_pending_reason.'</td></tr>';

		switch ($this->payment_data->app_name)
			{
			case 'MediaShop':
				$ms_version = PayageHelper::getComponentVersion('mediashop');
				if (version_compare($ms_version, '10.00', '>='))
					$this->payment_data->app_name = JHtml::link('index.php?option=com_mediashop&amp;task=edit_tid&amp;controller=transaction&amp;tid='.$this->payment_data->pg_transaction_id, 'MediaShop', 'target="_blank"');
				break;
			case 'RentalotPlus':
				$rp_version = PayageHelper::getComponentVersion('rentalotplus');
				if (version_compare($rp_version, '19.00', '>='))
					$this->payment_data->app_name = JHtml::link('index.php?option=com_rentalotplus&amp;task=edit_lid&amp;controller=booking&amp;lid='.$this->payment_data->app_transaction_id, 'RentalotPlus', 'target="_blank"');
				break;
			}

		echo '<tr><td>'.JText::_('COM_PAYAGE_APPLICATION').'</td><td>'.$this->payment_data->app_name.'</td></tr>';

		if (!empty($this->payment_data->item_name))
			echo '<tr><td>'.JText::_('COM_PAYAGE_ITEM').'</td><td>'.$this->payment_data->item_name.'</td></tr>';

		if (!empty($this->payment_data->app_transaction_id))
			echo '<tr><td>'.JText::_('COM_PAYAGE_APP_TRANSACTION_ID').'</td><td>'.$this->payment_data->app_transaction_id.'</td></tr>';

		if (!empty($this->payment_data->gw_transaction_id))
			echo '<tr><td>'.JText::_('COM_PAYAGE_GATEWAY_TRANSACTION_ID').'</td><td>'.$this->payment_data->gw_transaction_id.'</td></tr>';

		echo '<tr><td>'.JText::_('COM_PAYAGE_CURRENCY').'</td><td>'.$this->payment_data->currency.'</td></tr>';

		echo '<tr><td>'.JText::_('COM_PAYAGE_GROSS').'</td><td>'.PayageHelper::format_amount($this->payment_data->gross_amount, $this->account_data->currency_format, $this->account_data->currency_symbol).'</td></tr>';

		if (!empty($this->payment_data->tax_amount))
			echo '<tr><td>'.JText::_('COM_PAYAGE_TAX').'</td><td>'.PayageHelper::format_amount($this->payment_data->tax_amount, $this->account_data->currency_format, $this->account_data->currency_symbol).'</td></tr>';

		if (!empty($this->payment_data->customer_fee))
			echo '<tr><td>'.JText::_('COM_PAYAGE_FEE_CUSTOMER').'</td><td>'.PayageHelper::format_amount($this->payment_data->customer_fee, $this->account_data->currency_format, $this->account_data->currency_symbol).'</td></tr>';

		if (!empty($this->payment_data->gateway_fee))
			echo '<tr><td>'.JText::_('COM_PAYAGE_FEE_GATEWAY').'</td><td>'.PayageHelper::format_amount($this->payment_data->gateway_fee, $this->account_data->currency_format, $this->account_data->currency_symbol).'</td></tr>';
            
// external currency fields - if the external currency is populated we show all the fields so that it's clear what happened

		if (!empty($this->payment_data->external_currency_code))
            {
			echo '<tr><td>'.JText::_('COM_PAYAGE_EXTERNAL_CURRENCY').'</td><td>'.$this->payment_data->external_currency_code.'</td></tr>';
			echo '<tr><td>'.$this->payment_data->external_currency_code.' '.JText::_('COM_PAYAGE_REQUESTED').'</td>';
            echo '<td>'.$this->payment_data->external_currency_amount_requested.'</td></tr>';
			echo '<tr><td>'.$this->payment_data->external_currency_code.' '.JText::_('COM_PAYAGE_RECEIVED').'</td>';
            echo '<td>'.$this->payment_data->external_currency_amount_paid.'</td></tr>';
			echo '<tr><td>'.$this->payment_data->external_currency_code.' '.JText::_('COM_PAYAGE_RATE').'</td>';
            echo '<td>'.$this->payment_data->external_currency_exchange_rate.'</td></tr>';
            }

// payer fields                        
            
		echo '<tr><td>'.JText::_('COM_PAYAGE_PAYER_EMAIL').'</td><td>'.$this->payment_data->payer_email.'</td></tr>';
		echo '<tr><td>'.JText::_('COM_PAYAGE_FIRST_NAME').'</td><td>'.$this->payment_data->payer_first_name.'</td></tr>';
		echo '<tr><td>'.JText::_('COM_PAYAGE_LAST_NAME').'</td><td>'.$this->payment_data->payer_last_name.'</td></tr>';

		if (!empty($this->payment_data->payer_address1))
			echo '<tr><td>'.JText::_('COM_PAYAGE_PAYER_ADDRESS').'</td><td>'.$this->payment_data->payer_address1.'</td></tr>';

		if (!empty($this->payment_data->payer_address2))
			echo '<tr><td></td><td>'.$this->payment_data->payer_address2.'</td></tr>';

		if (!empty($this->payment_data->payer_city))
			echo '<tr><td></td><td>'.$this->payment_data->payer_city.'</td></tr>';

		if (!empty($this->payment_data->payer_state))
			echo '<tr><td></td><td>'.$this->payment_data->payer_state.'</td></tr>';

		if (!empty($this->payment_data->payer_zip_code))
			echo '<tr><td></td><td>'.$this->payment_data->payer_zip_code.'</td></tr>';

		require_once JPATH_ADMINISTRATOR.'/components/com_payage/helpers/country_helper.php';
	    $country_helper = new PayageCountryHelper;
		$country_detail = $country_helper->country_detail($this->payment_data->payer_country_code, $this->payment_data->payer_country);
		if (!empty($country_detail))
			echo '<tr><td>'.JText::_('COM_PAYAGE_PAYER_COUNTRY').'</td><td>'.$country_detail.'</td></tr>';

		if (!empty($this->payment_data->client_ip))
			echo '<tr><td>'.JText::_('COM_PAYAGE_IP_ADDRESS').'</td><td>'.$this->payment_data->client_ip.'</td></tr>';
            
		if (!empty($this->payment_data->client_ua))
            {
            $browser = self::getBrowser($this->payment_data->client_ua);
			echo '<tr><td>'.JText::_('COM_PAYAGE_BROWSER').'</td><td>'.$browser.'</td></tr>';
			echo '<tr><td>HTTP_USER_AGENT</td><td>'.$this->payment_data->client_ua.'</td></tr>';
            }            

	echo "</table>";
	echo '</fieldset>';

// gateway data, if not empty

	$full_details = false;
	if (!is_scalar($this->payment_data->gw_transaction_details) && ($this->payment_data->gw_transaction_details != (new stdClass())))
		{
		echo '<fieldset class="lad-fieldset lad-border width-auto lad-left payment-fieldset">';
		echo '<legend>'.JText::_('COM_PAYAGE_GATEWAY_TRANSACTION_DETAILS').'</legend>';
		echo "\n".'<table class="payment_details">';
		foreach ($this->payment_data->gw_transaction_details as $key => $value)
			if (!empty($key) && !empty($value))
					if (is_scalar($value))
						echo '<tr><td>'.$key.'</td><td>'.$value.'</td></tr>';
					else
						{
						$full_details = true;							// if there are nested structures, show the full details link
						echo '<tr><td>'.$key.'</td><td>{..}</td></tr>';
						}
			if ($full_details)
				{
				$popup_src = "index.php?option=com_payage&controller=payment&task=full_details&column=gw_transaction_details&id=".$this->payment_data->id."&tmpl=component";
				$popup = 'onclick="window.open('."'".$popup_src."', 'app_details', 'width=640,height=480,scrollbars=1,location=0,menubar=0,resizable=1'); return false;".'"';
				echo '<tr><td>'.JHtml::link('#', JText::_('COM_PAYAGE_MORE'), 'target="_blank" '.$popup).'</td><td></td></tr>';
				}
		echo "</table>";
		echo '</fieldset>';
		}

// history

	if (!empty($this->payment_data->pg_history))
		{
		echo '<fieldset class="lad-fieldset lad-border width-auto lad-left payment-fieldset">';
		echo '<legend>'.JText::_('COM_PAYAGE_HISTORY').'</legend>';
		$txt = nl2br($this->payment_data->pg_history);
		echo '<div class="lad-break-word">'.$txt.'</div>';
		echo '</fieldset>';
		}

// application data

	$full_details = false;
	if (!empty($this->payment_data->app_transaction_details))
		{
		echo '<fieldset class="lad-fieldset lad-border width-auto lad-left payment-fieldset">';
		echo '<legend>'.JText::_('COM_PAYAGE_APPLICATION_TRANSACTION_DETAILS').'</legend>';
		if (is_scalar($this->payment_data->app_transaction_details))
			echo $this->payment_data->app_transaction_details;	
		else
			{
			echo "\n".'<table class="payment_details">';
			foreach ($this->payment_data->app_transaction_details as $key => $value)
				if (!empty($key) && !empty($value))
					if (is_scalar($value))
						echo '<tr><td>'.$key.'</td><td>'.$value.'</td></tr>';
					else
						{
						$full_details = true;							// if there are nested structures, show the full details link
						echo '<tr><td>'.$key.'</td><td>{..}</td></tr>';
						}
			if ($full_details)
				{
				$popup_src = "index.php?option=com_payage&controller=payment&task=full_details&column=app_transaction_details&id=".$this->payment_data->id."&tmpl=component";
				$popup = 'onclick="window.open('."'".$popup_src."', 'app_details', 'width=640,height=480,scrollbars=1,location=0,menubar=0,resizable=1'); return false;".'"';
				echo '<tr><td>'.JHtml::link('#', JText::_('COM_PAYAGE_MORE'), 'target="_blank" '.$popup).'</td><td></td></tr>';
				}
			echo "\n</table>";
			}
		echo '</fieldset>';
		}

	echo '</form>';
    LAPG_admin::viewEnd();
}

//-------------------------------------------------------------------------------
// Get a short browser name from a UA string
//
function getBrowser($u_agent)
{
    if (empty($u_agent))
        return '';
    if (strstr($u_agent, 'Edg'))       	// must test for this first
        return 'Edge';
    if (strstr($u_agent, 'MSIE') && !strstr($u_agent, 'Opera')) 
        return 'MSIE'; 
    if (strstr($u_agent, 'Trident')) 
        return 'MSIE'; 
    if (strstr($u_agent, 'Firefox')) 
        return 'Firefox'; 
    if (strstr($u_agent, 'Chrome')) 	 // must test for Chrome before Safari
        return 'Chrome'; 
    if (strstr($u_agent, 'Safari')) 
        return 'Safari'; 
    if (strstr($u_agent, 'Opera')) 
        return 'Opera'; 
    if (strstr($u_agent, 'Netscape')) 
        return 'Netscape'; 
    if (strstr($u_agent, 'Konqueror')) 
        return 'Konqueror'; 
    return 'Unknown';
} 

}