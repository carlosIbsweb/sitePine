<?php
/********************************************************************
Product		: Payage
Date		: 21 January 2022
Copyright	: Les Arbres Design 2014-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class PayageModelReport extends LAPG_model
{
var $list_data = null;
var $chart_data = null;
var $pagination = null;
var $date_title = null;

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
// Get the filter states and construct the where clause
//
function where($min_days=0)
{
// get the filter states

    $today             = date('Y-m-d');
    $one_year_ago      = date('Y-m-d', strtotime('-1 year'));
	$filter_start_date = $this->app->getUserStateFromRequest('com_payage.filter_start_date','filter_start_date',$one_year_ago,'STRING');
	$filter_end_date   = $this->app->getUserStateFromRequest('com_payage.filter_end_date','filter_end_date',$today,'STRING');
	$filter_app        = $this->app->getUserStateFromRequest('com_payage.payment_filter_app','filter_app','0','STRING');
	$filter_currency   = $this->app->getUserStateFromRequest('com_payage.filter_currency','filter_currency','0','string');
	$filter_account    = $this->app->getUserStateFromRequest('com_payage.filter_account','filter_account',0,'int');
    
    if (!LAPG_admin::validDate($filter_start_date))
		{
		$this->app->enqueueMessage(JText::_('COM_PAYAGE_INVALID').' '.JText::_('COM_PAYAGE_FROM_DATE'), 'error');
        return false;
		}
    if (!LAPG_admin::validDate($filter_end_date))
		{
		$this->app->enqueueMessage(JText::_('COM_PAYAGE_INVALID').' '.JText::_('COM_PAYAGE_TO_DATE'), 'error');
        return false;
		}
    if ($min_days != 0)
        {
        $start = strtotime($filter_start_date);
        $end = strtotime($filter_end_date);
        $days_between = ceil(abs($end - $start) / 86400);
        if ($days_between < $min_days)
            {
    		$this->app->enqueueMessage(JText::sprintf('COM_PAYAGE_DATE_RANGE_AT_LEAST_X_DAYS',$min_days), 'error');
            return false;
            }
        }

   	$query_where = " WHERE DATE(`date_time_initiated`) >= ".$this->_db->Quote($filter_start_date)." AND DATE(`date_time_initiated`) <= ".$this->_db->Quote($filter_end_date);
	$query_where .= " AND `pg_status_code` IN (".LAPG_STATUS_SUCCESS.", ".LAPG_STATUS_PENDING.") ";

    if ($filter_currency != '0')
    	$query_where .= " AND `currency` = ".$this->_db->Quote($filter_currency);

	if ($filter_app != '0')
		$query_where .= " AND `app_name` = ".$this->_db->Quote($filter_app);

	if ($filter_account != 0)
		$query_where .= " AND `account_id` = ".$filter_account;
        
// check we have some data

    $query = "SELECT count(*) FROM `#__payage_payments` ".$query_where;
	$count = $this->ladb_loadResult($query);
    LAPG_trace::trace($query." returned: $count");
    if ($count == 0)
		{
		$this->app->enqueueMessage(JText::_('COM_PAYAGE_NO_DATA_SELECTION'), 'notice');
		return false;
		}
        
    return $query_where;
}

//-------------------------------------------------------------------------------
// Get the information for the Popular Products Pie Chart and Report
//
function popular_items()
{
// build the query for the chart

    $query_cols = "SELECT `item_name`, COUNT(*) as `number` FROM `#__payage_payments` ";
   	$query_where = $this->where();
    if ($query_where === false)
        return false;
    $query_group = " GROUP BY `item_name`";
    $query_order = " ORDER BY `number` DESC";
    $query_limit = " LIMIT 0, 10";
    $query = $query_cols.$query_where.$query_group.$query_order.$query_limit;
    LAPG_trace::trace($query);
		
// Set up the chart information

	require_once JPATH_ADMINISTRATOR.'/components/com_payage/helpers/plotalot.php';
	$chart_info = new stdclass();
	$chart_info->chart_type = CHART_TYPE_PIE_3D_V;
	$chart_info->x_size = 0;
	$chart_info->y_size = 250;
	$chart_info->num_plots = 1;
	$chart_info->x_format = FORMAT_NUM_UK_0;
	$chart_info->legend_type = LEGEND_RIGHT;
	$chart_info->extra_parms = ",chartArea:{left:0,top:5,width:'100%',height:'95%'}";
	$chart_info->plot_array = array();
	$chart_info->plot_array[0]['query'] = $query;
	$chart_info->plot_array[0]['enable'] = 1;
	$chart_info->plot_array[0]['colour'] = '7C78FF';
	$chart_info->plot_array[0]['style'] = PIE_MULTI_COLOUR;

// call Plotalot to make the chart

	$plotalot = new Plotalot;
	$this->chart_data = $plotalot->drawChart($chart_info);
    if ($this->chart_data == '')
		$this->chart_data = $plotalot->error;
    
// make the full list        

    $query_limit = " LIMIT 0, 100";
    $query = $query_cols.$query_where.$query_group.$query_order.$query_limit;
    LAPG_trace::trace($query);
    
	$this->list_data = $this->ladb_loadObjectList($query);

	if ($this->list_data === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return false;
		}

	return true;
}

//-------------------------------------------------------------------------------
// Get the information for the Country Report and Pie Chart
//
function country_sales()
{
// build the query for the chart

    $query_cols = "(SELECT IF(`payer_country_code`='','".JText::_('COM_PAYAGE_UNKNOWN')."',`payer_country_code`) AS `payer_country_code`, count(`id`) as `number`
                    FROM `#__payage_payments`";
   	$query_where = $this->where();
    if ($query_where === false)
        return false;
    $query_group = " GROUP BY `payer_country_code`";
    $query_order = " ORDER BY `number` DESC";
    $query_limit = " LIMIT 0, 10)";
    $query_union = " UNION (SELECT '".JText::_('COM_PAYAGE_ALL_OTHERS')."' AS `payer_country_code`, COALESCE(SUM(number),0) as `number` FROM 
            ( SELECT count(`id`) AS number FROM `#__payage_payments` ".$query_where."
                GROUP BY `payer_country_code` ORDER BY count(`id`) DESC LIMIT 10,18446744073709551615) AS X )";
    $query = $query_cols.$query_where.$query_group.$query_order.$query_limit.$query_union;
    LAPG_trace::trace($query);
		
// Set up the chart information

	require_once JPATH_ADMINISTRATOR.'/components/com_payage/helpers/plotalot.php';
	$chart_info = new stdclass();
	$chart_info->chart_type = CHART_TYPE_PIE_3D_V;
	$chart_info->x_size = 0;
	$chart_info->y_size = 250;
	$chart_info->num_plots = 1;
	$chart_info->x_format = FORMAT_NUM_UK_0;
	$chart_info->legend_type = LEGEND_RIGHT;
	$chart_info->extra_parms = ",chartArea:{left:0,top:5,width:'100%',height:'95%'}";
	$chart_info->plot_array = array();
	$chart_info->plot_array[0]['query'] = $query;
	$chart_info->plot_array[0]['enable'] = 1;
	$chart_info->plot_array[0]['colour'] = '7C78FF';
	$chart_info->plot_array[0]['style'] = PIE_MULTI_COLOUR;

// call Plotalot to make the chart

	$plotalot = new Plotalot;
	$this->chart_data = $plotalot->drawChart($chart_info);
    if ($this->chart_data == '')
		$this->chart_data = $plotalot->error;
        
// make the full list        

    $query_cols = "SELECT `payer_country_code`, count(`id`) as `number` FROM `#__payage_payments` ";
    $query = $query_cols.$query_where.$query_group.$query_order;
    LAPG_trace::trace($query);
    
	$this->list_data = $this->ladb_loadObjectList($query);

	if ($this->list_data === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return false;
		}

	return true;
}

//-------------------------------------------------------------------------------
// Get the information for the Sales History Line Chart
//
function sales_history()
{
// check if we can draw the chart

    $where = $this->where(30);
    if ($where === false)
        return false;
    $query = "SELECT `item_name`, COUNT(`item_name`) AS `number` FROM `#__payage_payments` ".$where."
        GROUP BY `item_name`
        ORDER BY `number` DESC
        LIMIT 8";
        
    LAPG_trace::trace("sales_history item query: ".$query);
	$this->list_data = $this->ladb_loadObjectList($query);
	if ($this->list_data === false)
		{
        LAPG_trace::trace($this->ladb_error_text);
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return false;
		}
    LAPG_trace::trace("sales_history item count = ".count($this->list_data));
        
    if (count($this->list_data) == 0)
		{
		$this->app->enqueueMessage(JText::_('COM_PLOTALOT_ERROR_NO_ROWS'), 'notice');
		return false;
		}
		
// Set up the chart information

	require_once JPATH_ADMINISTRATOR.'/components/com_payage/helpers/plotalot.php';
	$chart_info = new stdclass();
	$chart_info->chart_type = CHART_TYPE_LINE;
	$chart_info->x_size = 0;
	$chart_info->y_size = 450;
    $chart_info->show_grid = 1;
    $chart_info->x_labels = 10;
	$chart_info->x_format = FORMAT_CUSTOM_DATE;
    $chart_info->custom_x_format = 'MMM yy';
    $chart_info->y_format = FORMAT_NUM_UK_1;
    $chart_info->y_title = JText::_('COM_PAYAGE_AVERAGE_SALES_PER_DAY');
	$chart_info->legend_type = LEGEND_RIGHT;
	$chart_info->extra_parms = ",chartArea:{left:'8%',right:'20%',top:20,height:'75%'}";
    
// build the plot array - we create a plot for each of the top 8 items
// 2.08 - updated the query to work with sql_mode='ONLY_FULL_GROUP_BY' in Joomla 4
// - we use MIN() to disambiguate the date_time_initiated - ANY_VALUE() would be better but is not supported in MySql 5.6

	$chart_info->plot_array = array();
    $plot_index = 0;
    foreach ($this->list_data as $data)
        {
        if (empty($data->item_name))
            continue;
        $query_cols = "SELECT UNIX_TIMESTAMP(MIN(date_time_initiated)) AS `time`,
            ROUND((COUNT(`gross_amount`)/(IF (MONTH(MIN(`date_time_initiated`)) = MONTH(CURRENT_DATE()) AND YEAR(MIN(`date_time_initiated`)) = YEAR(CURRENT_DATE()), 
            DAY(CURRENT_DATE()), DAY(LAST_DAY(MIN(`date_time_initiated`)))))),2) as average,
            MONTH(`date_time_initiated`) AS `month` , YEAR(`date_time_initiated`) AS `year`                           
            FROM `#__payage_payments` ";
        $query_where = $where." AND `item_name` = ".$this->_db->Quote($data->item_name);
        $query_group = " GROUP BY `year`,`month`";
        $query_order = " ORDER BY `year`,`month`";
        $query = $query_cols.$query_where.$query_group.$query_order;
        LAPG_trace::trace("sales_history Plotalot query [$plot_index]: ".$query);
        $chart_info->plot_array[$plot_index]['legend'] = $data->item_name;
        $chart_info->plot_array[$plot_index]['query'] = $query;
        $chart_info->plot_array[$plot_index]['enable'] = 1;
        $chart_info->plot_array[$plot_index]['style'] = LINE_THICK_SOLID;
        $plot_index ++;
        }
	$chart_info->num_plots = $plot_index + 1;

// call Plotalot to make the chart

	$plotalot = new Plotalot;
	$this->chart_data = $plotalot->drawChart($chart_info);
    if ($this->chart_data == '')
		$this->chart_data = $plotalot->error;
    
	return true;
}

//-------------------------------------------------------------------------------
// Get the data for the Sales by Month Report
//
function sales_monthly()
{
// build the query
// 2.08 - updated the query to work with sql_mode='ONLY_FULL_GROUP_BY' in Joomla 4
// - we use MIN() to disambiguate the date_time_initiated - ANY_VALUE() would be better but is not supported in MySql 5.6

	$query_cols  = "SELECT YEAR(`date_time_initiated`) AS `year`, MONTH(`date_time_initiated`) AS `month`, 
                    IF (MONTH(MIN(`date_time_initiated`)) = MONTH(CURRENT_DATE()) AND YEAR(MIN(`date_time_initiated`)) = YEAR(CURRENT_DATE()), 
                        DAY(CURRENT_DATE()), DAY(LAST_DAY(MIN(`date_time_initiated`)))) AS `days`,
                    COUNT(`gross_amount`) AS `number`,
                    SUM(`gross_amount`) AS `gross_amount`, 
                    SUM(`gateway_fee`) AS `gateway_fee`,
                    SUM(`tax_amount`) AS `tax_amount`,
                    `currency` ";

	$query_from = "FROM `#__payage_payments`";

// where

   	$query_where = $this->where();
    if ($query_where === false)
        return false;
        
// group by

	$query_group = " GROUP BY `currency`, `year`, `month` ";

// order by

	$query_order = " ORDER BY `currency` ASC, `year` ASC, `month` ASC";

	$query = $query_cols.$query_from.$query_where.$query_group.$query_order;
    LAPG_trace::trace("sales_monthly query: ".$query);
    
	$this->list_data = $this->ladb_loadObjectList($query);

	if ($this->list_data === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return false;
		}

	return true;
}

//-------------------------------------------------------------------------------
// Get the data for the Sales by Item Report
//
function sales_item()
{
	$query_cols  = "SELECT `item_name`, `currency`,
                        COUNT(*) as `number`,
                        SUM(`gross_amount`) as `gross_amount`,                        
                        SUM(`gateway_fee`) AS `gateway_fee`,
                        SUM(`tax_amount`) AS `tax_amount`";
	$query_from = " FROM `#__payage_payments` ";

// where

   	$query_where = $this->where();
    if ($query_where === false)
        return false;

// group by

	$query_group = " GROUP BY `currency`,`item_name`";

// order by

	$query_order = " ORDER BY `currency` DESC, `item_name` ASC ";

	$query = $query_cols.$query_from.$query_where.$query_group.$query_order;
    LAPG_trace::trace("sales_item query: ".$query);
    
	$this->list_data = $this->ladb_loadObjectList($query);

	if ($this->list_data === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return false;
		}

	return true;
}

//-------------------------------------------------------------------------------
// Get the data for the Sales by Region Report
//
function sales_region()
{
    $common_where = $this->where();
    if ($common_where === false)
        return false;
            
    $query_cols  = "SELECT `currency`, COUNT(*) as `number`, SUM(`gross_amount`) as `gross_amount`, 
                    SUM(`gateway_fee`) AS `gateway_fee`, SUM(`tax_amount`) AS `tax_amount`";
    $query_from  = " FROM `#__payage_payments` ";
	$query_group = " GROUP BY `currency`";
	$query_order = " ORDER BY `currency` ";
    
// get the region array and get data for each defined region

    require_once JPATH_ADMINISTRATOR.'/components/com_payage/helpers/country_helper.php';
    $country = new PayageCountryHelper;
    $country->create_regions();
    
    foreach ($country->regions as $region)
        {
        $region_name = $region['name'];
        if (empty($region['members']))                  // empty members array means include all countries
            $query_where = $common_where;
        else
            {
            $countries = "'".implode("','",$region['members'])."'";
            $query_where = $common_where.' AND `payer_country_code` IN ('.$countries.')';
            }
        $query = $query_cols.$query_from.$query_where.$query_group.$query_order;
        LAPG_trace::trace("sales_region list query for $region_name:\n".$query);
        $region_data = $this->ladb_loadObjectList($query);
        if ($this->list_data === false)
            {
            $this->app->enqueueMessage($this->ladb_error_text, 'error');
            return false;
            }
        $this->list_data[$region_name] = $region_data;      // $this->list_data is an array of regions data
        }

// build the query for the pie chart

    $query = '';
    foreach ($country->regions as $region)
        {
        if (!$region['pie_chart'])
            continue;
        $region_name = $region['name'];
        $countries = "'".implode("','",$region['members'])."'";
        $query_where = $common_where.' AND `payer_country_code` IN ('.$countries.')';
        if ($query != '')
            $query .= "\n UNION ";
        $query .= "SELECT '$region_name', COUNT(*) as `number` FROM `#__payage_payments` ".$query_where;
        }

    LAPG_trace::trace("sales_region pie query:\n".$query);

// Set up the chart information

	require_once JPATH_ADMINISTRATOR.'/components/com_payage/helpers/plotalot.php';
	$chart_info = new stdclass();
	$chart_info->chart_type = CHART_TYPE_PIE_3D_V;
	$chart_info->x_size = 0;
	$chart_info->y_size = 300;
	$chart_info->num_plots = 1;
	$chart_info->x_format = FORMAT_NUM_UK_0;
	$chart_info->legend_type = LEGEND_RIGHT;
	$chart_info->extra_parms = ",chartArea:{left:0,top:5,width:'100%',height:'95%'}";
	$chart_info->plot_array = array();
	$chart_info->plot_array[0]['query'] = $query;
	$chart_info->plot_array[0]['enable'] = 1;
	$chart_info->plot_array[0]['colour'] = '7C78FF';
	$chart_info->plot_array[0]['style'] = PIE_MULTI_COLOUR;

// call Plotalot to make the chart

	$plotalot = new Plotalot;
	$this->chart_data = $plotalot->drawChart($chart_info);
    if ($this->chart_data == '')
		$this->chart_data = $plotalot->error;
        
	return true;
}

//-------------------------------------------------------------------------------
// Get the information for the Sales Calendar Chart
//
function sales_calendar()
{
// check if we can draw the chart

    $where = $this->where(30);
    if ($where === false)
        return false;

// get the number of years covered by the selected dates

    $query = "SELECT DISTINCT(YEAR(`date_time_initiated`)) FROM `#__payage_payments` ".$where;        
	$distinct_years = $this->ladb_loadObjectList($query);
    $number_of_years = count($distinct_years);
    if ($number_of_years > 5)
        {
		$this->app->enqueueMessage(JText::sprintf('COM_PAYAGE_REPORT_MAX_X_YEARS', 5), 'error');
        return false;
        }

// get the number of currencies in the selected payments

    $query = "SELECT DISTINCT(`currency`) FROM `#__payage_payments` ".$where;        
	$distinct_currencies = $this->ladb_loadObjectList($query);
    $number_of_currencies = count($distinct_currencies);

// build the query for the chart

    if ($number_of_currencies == 1)
        $query_cols = "SELECT UNIX_TIMESTAMP(`date_time_initiated`), ROUND(SUM(`gross_amount`),2) AS `total` FROM `#__payage_payments` ";
    else
        $query_cols = "SELECT UNIX_TIMESTAMP(`date_time_initiated`), COUNT(*) AS `total` FROM `#__payage_payments` ";
    $query_group = " GROUP BY DATE(`date_time_initiated`)";
    $query_order = " ORDER BY `date_time_initiated`";
    $query = $query_cols.$where.$query_group.$query_order;
    LAPG_trace::trace($query);
		
// Set up the chart information

	require_once JPATH_ADMINISTRATOR.'/components/com_payage/helpers/plotalot.php';
	$chart_info = new stdclass();
	$chart_info->chart_type = CHART_TYPE_CALENDAR;
    if ($number_of_currencies > 1)
		$chart_info->chart_title = JText::_('COM_PAYAGE_REPORT_NUMBER_SALES_DAY');
    else
		$chart_info->chart_title = JText::sprintf('COM_PAYAGE_REPORT_SALES_PER_DAY_X',$distinct_currencies[0]->currency);
	$chart_info->x_size = 950;
	$chart_info->y_size = ($number_of_years * 145) + 45;
	$chart_info->num_plots = 1;
	$chart_info->plot_array = array();
	$chart_info->plot_array[0]['query'] = $query;
	$chart_info->plot_array[0]['enable'] = 1;

// call Plotalot to make the chart

	$plotalot = new Plotalot;
	$this->chart_data = $plotalot->drawChart($chart_info);
    if ($this->chart_data == '')
		$this->chart_data = $plotalot->error;
    
	return true;
}

}