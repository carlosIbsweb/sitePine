<?php
/********************************************************************
Product		: Payage
Date		: 22 January 2022
Copyright	: Les Arbres Design 2014-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');
require_once JPATH_ADMINISTRATOR.'/components/com_payage/helpers/country_helper.php';

class PayageViewReports extends JViewLegacy
{

//-------------------------------------------------------------------------------
// Show the report menu
//
function report_menu()
{
	LAPG_admin::addSubMenu('report');
    LAPG_admin::viewStart();
	JToolBarHelper::title('Payage: '.JText::_('COM_PAYAGE_REPORTS'), 'lad.png');
	JToolBarHelper::cancel('cancellist','JTOOLBAR_CLOSE');

// Set up the report links

	$report_table = array(
		array(
			'link' => LAPG_COMPONENT_LINK.'&amp;controller=report&amp;function=popular_items',
			'name' => 'COM_PAYAGE_REPORT_POPULAR_ITEMS',
			'desc' => 'COM_PAYAGE_REPORT_POPULAR_ITEMS_DESC',
			'icon' => 'i_hybrid.png'),
		array(
			'link' => LAPG_COMPONENT_LINK.'&amp;controller=report&amp;function=country_sales',
			'name' => 'COM_PAYAGE_REPORT_SALES_COUNTRY',
			'desc' => 'COM_PAYAGE_REPORT_SALES_COUNTRY_DESC',
			'icon' => 'i_hybrid.png'),
		array(
			'link' => LAPG_COMPONENT_LINK.'&amp;controller=report&amp;function=sales_history',
			'name' => 'COM_PAYAGE_REPORT_SALES_HISTORY',
			'desc' => 'COM_PAYAGE_REPORT_SALES_HISTORY_DESC',
			'icon' => 'i_line.png'),
		array(
			'link' => LAPG_COMPONENT_LINK.'&amp;controller=report&amp;function=sales_monthly',
			'name' => 'COM_PAYAGE_REPORT_SALES_MONTHLY',
			'desc' => 'COM_PAYAGE_REPORT_SALES_MONTHLY_DESC',
			'icon' => 'i_report.png'),
		array(
			'link' => LAPG_COMPONENT_LINK.'&amp;controller=report&amp;function=sales_item',
			'name' => 'COM_PAYAGE_REPORT_SALES_ITEM',
			'desc' => 'COM_PAYAGE_REPORT_SALES_ITEM_DESC',
			'icon' => 'i_report.png'),
		array(
			'link' => LAPG_COMPONENT_LINK.'&amp;controller=report&amp;function=sales_region',
			'name' => 'COM_PAYAGE_REPORT_SALES_REGION',
			'desc' => 'COM_PAYAGE_REPORT_SALES_REGION_DESC',
			'icon' => 'i_hybrid.png'),
        array(
			'link' => LAPG_COMPONENT_LINK.'&amp;controller=report&amp;function=sales_calendar',
			'name' => 'COM_PAYAGE_REPORT_SALES_CALENDAR',
			'desc' => 'COM_PAYAGE_REPORT_SALES_CALENDAR_DESC',
			'icon' => 'c_calendar.png'));

// show the report list

	?>
	<form method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_payage" />
	<input type="hidden" name="task" value="report_menu" />
	<input type="hidden" name="controller" value="report" />
	<table class="table table-striped">
	<thead><tr><th></th>
    <th style="width:15%; white-space:nowrap;"><?php echo  JText::_('COM_PAYAGE_REPORT_NAME'); ?></th>
    <th style="width:85%; white-space:nowrap;"><?php echo  JText::_('COM_PAYAGE_REPORT_DESC'); ?></th>
    </tr></thead><tbody>

	<?php
	foreach ($report_table as $report)
		{
		echo "<tr><td>".'<img src="'.LAPG_ADMIN_ASSETS_URL.$report['icon'].'" alt="" />'."</td>
                <td>".JHTML::link($report['link'], JText::_($report['name']))."</td>
                <td>".JText::_($report['desc'])."</td></tr>";
		}
	echo '</tbody></table></form><p></p>';
	LAPG_admin::viewEnd();
}

//-------------------------------------------------------------------------------
// Show the Popular Items Report and Chart
//
function popular_items()
{
	LAPG_admin::addSubMenu('report');
	LAPG_admin::viewStart();
	JToolBarHelper::title('Payage: '.JText::_('COM_PAYAGE_REPORT_POPULAR_ITEMS'), 'lad.png');
	JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');
    
    self::make_filters('popular_items');

    if ($this->model_result === false)
        {
    	LAPG_admin::viewEnd();
        return;
        }
    
// Show the list data

    echo '<div style="display:inline-block; margin-right:25px; vertical-align:top;">';
	echo '<table class="table table-striped table-bordered table-condensed width-auto">';
	echo '<thead><tr>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_ITEM').'</th>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_NUMBER').'</th>';
	echo '</tr></thead>';
    
    echo '<tbody>';
    foreach ($this->list_data as $row)
        echo '<tr><td>'.$row->item_name.'</td><td>'.$row->number.'</td></tr>';

	echo '</tbody></table></div>';

// show the chart

	if (strpos($this->chart_data,'script type'))						// is it a script?
		{
		$document = JFactory::getDocument();
		$document->addScript("https://www.gstatic.com/charts/loader.js");	// load the Google Javascript
		$document->addCustomTag($this->chart_data);				            // load the Plotalot Javascript
		echo '<div id="chart_1" style="display:inline-block; margin-right:25px; vertical-align:top;"></div>';
		self::resize();
		}
	else
		echo $this->chart_data;
	
    echo self::plotalot_logo();
	LAPG_admin::viewEnd();
}

//-------------------------------------------------------------------------------
// Show the Sales by Country Report and Chart
//
function country_sales()
{
	LAPG_admin::addSubMenu('report');
	LAPG_admin::viewStart();
	JToolBarHelper::title('Payage: '.JText::_('COM_PAYAGE_REPORT_SALES_COUNTRY'), 'lad.png');
	JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');

    self::make_filters('country_sales');

    if ($this->model_result === false)
        {
    	LAPG_admin::viewEnd();
        return;
        }
        
// Show the list data

    $country = new PayageCountryHelper;

    echo '<div style="display:inline-block; margin-right:25px; vertical-align:top;">';
	echo '<table class="table table-striped table-bordered table-condensed width-auto">';
	echo '<thead><tr>';
	echo '<th style="white-space:nowrap;" colspan="2">'.JText::_('COM_PAYAGE_PAYER_COUNTRY').'</th>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_NUMBER').'</th>';
	echo '</tr></thead>';
    
    echo '<tbody>';
    $country_count = 0;
    $sales_count = 0;
    foreach ($this->list_data as $row)
        {
        if ($row->payer_country_code != '')
            $country_count ++;
        $sales_count += $row->number;
        $country_name = $country->country_name($row->payer_country_code);
        echo '<tr><td>'.$row->payer_country_code.'</td><td>'.$country_name.'</td><td>'.$row->number.'</td></tr>';
        }

	echo '</tbody>';
    echo '<tfoot><tr class="report_totals"><td>'.$country_count.'</td><td>'.JText::_('COM_PAYAGE_TOTALS').'</td><td>'.$sales_count.'</td></tr></tfoot>';
    echo '</table></div>';
    
// show the chart

	if (strpos($this->chart_data,'script type'))					// is it a script?
		{
		$document = JFactory::getDocument();
		$document->addScript("https://www.gstatic.com/charts/loader.js");	// load the Google Javascript
		$document->addCustomTag($this->chart_data);					// load the chart script
		echo '<div id="chart_1" style="display:inline-block; margin-right:25px; vertical-align:top;"></div>';
		self::resize();
		}
	else
		echo $this->chart_data;
        
    echo self::plotalot_logo();
	LAPG_admin::viewEnd();
}

//-------------------------------------------------------------------------------
// Show the Sales History Line Chart
//
function sales_history()
{
	LAPG_admin::addSubMenu('report');
	LAPG_admin::viewStart();
	JToolBarHelper::title('Payage: '.JText::_('COM_PAYAGE_REPORT_SALES_HISTORY'), 'lad.png');
	JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');
    
    self::make_filters('sales_history');

    if ($this->model_result === false)
        {
    	LAPG_admin::viewEnd();
        return;
        }

// show the chart

	if (strpos($this->chart_data,'script type'))				// is it a script?
		{
		$document = JFactory::getDocument();
		$document->addScript("https://www.gstatic.com/charts/loader.js");	// load the Google Javascript
		$document->addCustomTag($this->chart_data);				// load the chart script
		echo '<div id="chart_1"></div>';
		self::resize();
		}
	else
		echo $this->chart_data;
	
    echo self::plotalot_logo();
	LAPG_admin::viewEnd();
}

//-------------------------------------------------------------------------
// Show the Sales by Month Report
//
function sales_monthly()
{
	LAPG_admin::addSubMenu('report');
	LAPG_admin::viewStart();
	JToolBarHelper::title('Payage: '.JText::_('COM_PAYAGE_REPORT_SALES_MONTHLY'), 'lad.png');
	JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');
	
    self::make_filters('sales_monthly');

    if ($this->model_result === false)
        {
    	LAPG_admin::viewEnd();
        return;
        }
        
	echo '<div class="lad-scroll-container">';
	echo '<table class="table table-striped lad-report-table">';
	echo '<thead><tr>';
    echo '<th style="white-space:nowrap;">';
    echo JText::_('COM_PAYAGE_MONTH');
    echo '</th>';
    echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_DAYS').'</th>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_NUMBER').'</th>';
    echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_PER_DAY').'</th>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_GROSS').'</th>';
    echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_TAX').'</th>';
    echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_FEES').'</th>';
    echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_NET').'</th>';
    echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_NET_PER_DAY').'</th>';
    echo '</tr></thead><tbody>';

    $current_currency = '';
    $total_number = 0;
    foreach ($this->list_data as $row)
		{
        if ($row->currency != $current_currency)
            {
            if ($current_currency != '')
                {
                $gross = PayageHelper::format_amount($total_gross);
                $tax = PayageHelper::format_amount($total_tax);
                $fee = PayageHelper::format_amount($total_fees);
                $net_amt = $total_gross - $total_tax - $total_fees;
                $net = PayageHelper::format_amount($net_amt);
                if ($total_days == 0)
                    {
                    $num_day = 0;
                    $net_day = 0;
                    }
                else
                    {
                    $num_day_raw = $total_number / $total_days;
                    $num_day = number_format($num_day_raw, 1);
                    $net_day_raw = $net_amt / $total_days;
                    $net_day = PayageHelper::format_amount($net_day_raw);
                    }
                echo '<tr class="report_totals"><td>'.$current_currency.' '.JText::_('COM_PAYAGE_TOTALS').'</td>';
                echo "<td>$total_days</td><td>$total_number</td><td>$num_day</td><td>$gross</td><td>$tax</td><td>$fee</td><td>$net</td><td>$net_day</td></tr>\n";
                }
            $current_currency = $row->currency;
            echo '<tr><td colspan="9" style="font-weight:bold">'.JText::sprintf('COM_PAYAGE_AMOUNTS_IN_X',$current_currency).'</td></tr>';
        	$total_days = 0;
            $total_number = 0;
            $total_gross = 0;
            $total_tax = 0;
            $total_fees = 0;
            }
		$total_days += $row->days;
        $total_number += $row->number;
        $total_gross += $row->gross_amount;
        $total_tax += $row->tax_amount;
        $total_fees += $row->gateway_fee;
        $month = self::get_month_name($row->month).' '.$row->year;
		$gross = PayageHelper::format_amount($row->gross_amount);
        $tax = PayageHelper::format_amount($row->tax_amount);
        $fee = PayageHelper::format_amount($row->gateway_fee);
        $net_amt = $row->gross_amount - $row->tax_amount - $row->gateway_fee;
        $net = PayageHelper::format_amount($net_amt);
		if ($row->days == 0)
            {
            $num_day = 0;
            $net_day = 0;
            }
		else
            {
			$num_day_raw = $row->number / $row->days;
    		$num_day = number_format($num_day_raw, 1);
			$net_day_raw = $net_amt / $row->days;
    		$net_day = PayageHelper::format_amount($net_day_raw);
            }
			
		echo "<tr><td>$month</td><td>$row->days</td><td>$row->number</td><td>$num_day</td>";
        echo "<td>$gross</td><td>$tax</td><td>$fee</td><td>$net</td><td>$net_day</td></tr>\n";
		}
    if ($total_number > 0)
        {
        $gross = PayageHelper::format_amount($total_gross);
        $tax = PayageHelper::format_amount($total_tax);
        $fee = PayageHelper::format_amount($total_fees);
        $net_amt = $total_gross - $total_tax - $total_fees;
        $net = PayageHelper::format_amount($net_amt);
        if ($total_days == 0)
            {
            $num_day = 0;
            $net_day = 0;
            }
        else
            {
            $num_day_raw = $total_number / $total_days;
            $num_day = number_format($num_day_raw, 1);
            $net_day_raw = $net_amt / $total_days;
            $net_day = PayageHelper::format_amount($net_day_raw);
            }
        echo '<tr class="report_totals"><td>'.$current_currency.' '.JText::_('COM_PAYAGE_TOTALS').'</td>';
        echo "<td>$total_days</td><td>$total_number</td><td>$num_day</td><td>$gross</td><td>$tax</td><td>$fee</td><td>$net</td><td>$net_day</td></tr>\n";
        }
        
	echo '</tbody></table></div></form>';
	LAPG_admin::viewEnd();
}

//-------------------------------------------------------------------------
// Show the Sales by Item Report
//
function sales_item()
{
	LAPG_admin::addSubMenu('report');
	LAPG_admin::viewStart();
	JToolBarHelper::title('Payage: '.JText::_('COM_PAYAGE_REPORT_SALES_ITEM'), 'lad.png');
	JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');
    
    self::make_filters('sales_item');

    if ($this->model_result === false)
        {
    	LAPG_admin::viewEnd();
        return;
        }
        
	echo '<div class="lad-scroll-container">';
	echo '<table class="table table-striped lad-report-table">';
	echo '<thead><tr>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_ITEM').'</th>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_NUMBER').'</th>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_GROSS').'</th>';
    echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_TAX').'</th>';
    echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_FEES').'</th>';
    echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_NET').'</th>';
	echo '</tr></thead>';

    echo '<tbody>';
    
    $current_currency = '';
    $total_number = 0;
    foreach ($this->list_data as $row)
		{
        if ($row->currency != $current_currency)
            {
            if ($current_currency != '')
                {
                $gross = PayageHelper::format_amount($total_gross);
                $tax = PayageHelper::format_amount($total_tax);
                $fee = PayageHelper::format_amount($total_fees);
                $net_amt = $total_gross - $total_tax - $total_fees;
                $net = PayageHelper::format_amount($net_amt);
                echo '<tr class="report_totals"><td>'.$current_currency.' '.JText::_('COM_PAYAGE_TOTALS').'</td>';
                echo "<td>$total_number</td><td>$gross</td><td>$tax</td><td>$fee</td><td>$net</td></tr>\n";
                }
            $current_currency = $row->currency;
            echo '<tr><td colspan="6" style="font-weight:bold">'.JText::sprintf('COM_PAYAGE_AMOUNTS_IN_X',$current_currency).'</td></tr>';
            $total_number = 0;
            $total_gross = 0;
            $total_tax = 0;
            $total_fees = 0;
            }
        $total_number += $row->number;
        $total_gross += $row->gross_amount;
        $total_tax += $row->tax_amount;
        $total_fees += $row->gateway_fee;
		$gross = PayageHelper::format_amount($row->gross_amount);
        $tax = PayageHelper::format_amount($row->tax_amount);
        $fee = PayageHelper::format_amount($row->gateway_fee);
        $net_amt = $row->gross_amount - $row->tax_amount - $row->gateway_fee;
        $net = PayageHelper::format_amount($net_amt);
		echo "<tr><td>$row->item_name</td><td>$row->number</td><td>$gross</td><td>$tax</td><td>$fee</td><td>$net</td></tr>\n";
		}
        
    if ($total_number > 0)
        {
        $gross = PayageHelper::format_amount($total_gross);
        $tax = PayageHelper::format_amount($total_tax);
        $fee = PayageHelper::format_amount($total_fees);
        $net_amt = $total_gross - $total_tax - $total_fees;
        $net = PayageHelper::format_amount($net_amt);
        echo '<tr class="report_totals"><td>'.$current_currency.' '.JText::_('COM_PAYAGE_TOTALS').'</td>';
        echo "<td>$total_number</td><td>$gross</td><td>$tax</td><td>$fee</td><td>$net</td></tr>\n";
        }
        
	echo '</tbody></table></div>';
	LAPG_admin::viewEnd();
}

//-------------------------------------------------------------------------
// Show the Sales by Region Report
//
function sales_region()
{
	LAPG_admin::addSubMenu('report');
	LAPG_admin::viewStart();
	JToolBarHelper::title('Payage: '.JText::_('COM_PAYAGE_REPORT_SALES_REGION'), 'lad.png');
	JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');
    
    self::make_filters('sales_region');

    if ($this->model_result === false)
        {
    	LAPG_admin::viewEnd();
        return;
        }

	echo '<div class="lad-scroll-container">';
	echo '<table class="table table-striped lad-report-table">';
	echo '<thead><tr>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_REGION').'/'.JText::_('COM_PAYAGE_CURRENCY').'</th>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_NUMBER').'</th>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_GROSS').'</th>';
    echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_TAX').'</th>';
    echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_FEES').'</th>';
    echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_NET').'</th>';
	echo '</tr></thead>';
    echo '<tbody>';
    
    $current_currency = '';
    $total_number = 0;
    
// $this->list_data is an array of regions
// - each region has an array for each currency

    $report_region_name = '';
    foreach ($this->list_data as $region_name => $region_data)
        foreach ($region_data as $currency_data)
            {
            if ($region_name != $report_region_name)      // only show the region name when it changes
                $report_region_name = $region_name;
            else
                $report_region_name = ' ...';
            $currency = $currency_data->currency;
            $number = $currency_data->number;
            $gross = PayageHelper::format_amount($currency_data->gross_amount);
            $tax = PayageHelper::format_amount($currency_data->tax_amount);
            $fee = PayageHelper::format_amount($currency_data->gateway_fee);
            $net_amt = $currency_data->gross_amount - $currency_data->tax_amount - $currency_data->gateway_fee;
            $net = PayageHelper::format_amount($net_amt);
            echo "\n".'<tr><td>'.$report_region_name.' (<span style="font-size:smaller;">'.$currency.'</span>'.")</td><td>$number</td><td>$gross</td><td>$tax</td><td>$fee</td><td>$net</td></tr>";
            }
                
	echo '</tbody></table></div>';

// show the chart

	if (strpos($this->chart_data,'script type'))						// is it a script?
		{
		$document = JFactory::getDocument();
		$document->addScript("https://www.gstatic.com/charts/loader.js");	// load the Google Javascript
		$document->addCustomTag($this->chart_data);				            // load the Plotalot Javascript
		echo '<div id="chart_1""></div>';
		self::resize();
		}
	else
		echo $this->chart_data;
	
    echo self::plotalot_logo();

	LAPG_admin::viewEnd();
}

function sales_calendar()
{
	LAPG_admin::addSubMenu('report');
	LAPG_admin::viewStart();
	JToolBarHelper::title('Payage: '.JText::_('COM_PAYAGE_REPORT_SALES_CALENDAR'), 'lad.png');
	JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');
    
    self::make_filters('sales_calendar');

    if ($this->model_result === false)
        {
    	LAPG_admin::viewEnd();
        return;
        }

// show the chart - the calendar chart cannot resize so we load it in a scrolling div instead

	if (strpos($this->chart_data,'script type'))				// is it a script?
		{
		$document = JFactory::getDocument();
		if (substr(JVERSION,0,1) == '4')						// Joomla 4 atum template fix
			$document->addCustomTag("<style>*, ::before, ::after {transition-duration:0s !important}</style>");
		$document->addScript("https://www.gstatic.com/charts/loader.js");	// load the Google Javascript
		$document->addCustomTag($this->chart_data);				// load the chart script
        echo '<div style="overflow-x:scroll;overflow-y:hidden">';
		echo '<div id="chart_1"></div>';
        echo '</div>';
		}
	else
		echo $this->chart_data;
	
    echo self::plotalot_logo();
	LAPG_admin::viewEnd();
}

//-------------------------------------------------------------------------
// get the current filters and construct the html filter elements and input form
// not all filters are used by all reports but we make them all anyway
//
function make_filters($function_name, $filters='')
{
	$this->app = JFactory::getApplication();
    $today = date('Y-m-d');
    $one_year_ago = date('Y-m-d', strtotime('-1 year'));
	$filter_start_date = $this->app->getUserStateFromRequest('com_payage.filter_start_date','filter_start_date',$one_year_ago,'STRING');
	$filter_end_date = $this->app->getUserStateFromRequest('com_payage.filter_end_date','filter_end_date',$today,'STRING');
   	$filter_app = $this->app->getUserStateFromRequest('com_payage.payment_filter_app','filter_app','0','STRING');
   	$filter_currency = $this->app->getUserStateFromRequest('com_payage.filter_currency','filter_currency','0','STRING');
   	$filter_account = $this->app->getUserStateFromRequest('com_payage.filter_account','filter_account',0,'int');
        
	$start_date_html = LAPG_admin::make_date_picker('filter_start_date', $filter_start_date);
	$end_date_html   = LAPG_admin::make_date_picker('filter_end_date', $filter_end_date);

    if (count($this->app_list) > 2)         // 2 items in the list would be "All" and one application - so don't show the selector
        $app_filter_html = LAPG_admin::make_list('filter_app', $filter_app, $this->app_list, 'onchange="this.form.submit();"');
    else
        $app_filter_html = '<input type="hidden" name="filter_app" value="0" />';
    
    if (count($this->currency_list) > 2)
    	$currency_filter_html = LAPG_admin::make_list('filter_currency', $filter_currency, $this->currency_list, 'onchange="this.form.submit();"');
    else
        $currency_filter_html = '<input type="hidden" name="filter_currency" value="0" />';

    if (count($this->account_list) > 2)
    	$account_filter_html = LAPG_admin::make_list('filter_account', $filter_account, $this->account_list, 'onchange="this.form.submit();"');					
    else
        $account_filter_html = '<input type="hidden" name="filter_account" value="0" />';

	?>
	<form method="post" name="adminForm" id="adminForm" class="lad-filterform">
	<input type="hidden" name="option" value="com_payage" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="report" />
	<input type="hidden" name="function" value="<?php echo $function_name;?>" />
    <?php
    
	echo '<div>&nbsp;<div class="lad-filterform-left">';
    echo JText::_('COM_PAYAGE_FROM_DATE').' '.$start_date_html.' '.JText::_('COM_PAYAGE_TO_DATE').' '.$end_date_html;
    echo ' <button type="button" class="btn btn-primary" onclick="this.form.submit();">'.JText::_('COM_PAYAGE_GO').'</button>';
	echo '</div>'; 
	echo '<div class="lad-filterform-right">';
    echo $app_filter_html.' '.$currency_filter_html.' '.$account_filter_html;
    echo ' <button type="button" class="btn btn-primary" onclick="'."
        this.form.filter_start_date.value='".$one_year_ago."';
        this.form.filter_end_date.value='".$today."';
        this.form.filter_app.value='0';
        this.form.filter_currency.value='0';
        this.form.filter_account.value='0';
        this.form.submit();".'">'.JText::_('JSEARCH_RESET').'</button>';
	echo '</div></div></form>';
    echo '<div style="clear:both;margin-bottom:5px;"></div>';
}

//-------------------------------------------------------------------------
// Show the Plotalot logo
//
static function plotalot_logo()
{
    $plotalot_logo = '<img src="'.LAPG_ADMIN_ASSETS_URL.'plotalot.png" alt="" >';
	return '<div style="text-align:right">'.JHtml::link(LAPG_PLOTALOT_LINK, $plotalot_logo, 'target="_blank"').'</div>';
}

//-------------------------------------------------------------------------
// Add a script to resize the chart if the window resizes
//
static function resize()
{
	$script = "\n".'<script type="text/javascript">';
	$script .= "\n"."window.addEventListener('resize', plotalot_resize);";
	$script .= "\n".'function plotalot_resize() {';
	$script .= "\n"."if (typeof window.plotalot_resize_function != 'undefined')";
	$script .= "\n".'{';
	$script .= "\n".'window.plotalot_resize_function = null;';
	$script .= "\n".'clearTimeout(window.plotalot_resize_function);';
	$script .= "\n".'}';
	$script .= "\n".'window.plotalot_resize_function = setTimeout(plotalot_resize_chart,250);';
	$script .= "\n".'}';
	$script .= "\n".'function plotalot_resize_chart() {';
	$script .= "\n"."if (typeof window.plotalot_chart_1_options != 'undefined') {";
	$script .= "\n"."  window.plotalot_chart_1_options.width = document.getElementById('chart_1').clientWidth;";
	$script .= "\n"."  window.plotalot_chart_1_options.height = document.getElementById('chart_1').clientHeight;";
	$script .= "\n"."  window.plotalot_chart_1.draw(window.plotalot_chart_1_data, window.plotalot_chart_1_options); }";
	$script .= "\n".'}';
	$script .= "\n</script>";
	$document = JFactory::getDocument();
	$document->addCustomTag($script);		
}

//---------------------------------------------------------------------------------------------
// Get a month name in the current language
//
function get_month_name($month)
{
	switch ($month)
		{
		case 1: return JText::_('JANUARY');
		case 2: return JText::_('FEBRUARY');
		case 3: return JText::_('MARCH');
		case 4: return JText::_('APRIL');
		case 5: return JText::_('MAY');
		case 6: return JText::_('JUNE');
		case 7: return JText::_('JULY');
		case 8: return JText::_('AUGUST');
		case 9: return JText::_('SEPTEMBER');
		case 10: return JText::_('OCTOBER');
		case 11: return JText::_('NOVEMBER');
		case 12: return JText::_('DECEMBER');
		}
}

}