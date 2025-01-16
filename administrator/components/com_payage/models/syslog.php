<?php
/********************************************************************
Product		: Payage
Date		: 20 January 2022
Copyright	: Les Arbres Design 2014-2022
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

Class PayageModelSyslog extends LAPG_model
{
var $data;
var $_pagination = null;

//-------------------------------------------------------------------------------
// initialise the data
//
function initData()
{
	$this->data = new stdClass();
	$this->data->id = 0;
	$this->data->log_type = 0;
	$this->data->client_ip = PayageHelper::getIPaddress();
	$this->data->title = '';
	$this->data->detail = '';
	return $this->data;
}
	
//-------------------------------------------------------------------------------
// get an existing row
// return false with an error if we couldn't find it
//
function getOne($id)
{
    if (!LAPG_admin::is_posint($id, false, 1))
		return false;
	$query = "SELECT * FROM `#__payage_syslog` WHERE id = $id";
	$this->data = $this->ladb_loadObject($query);
	if (empty($this->data))
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return false;
		}
	return $this->data;
}

//-------------------------------------------------------------------------------
// Create a record
// can be called from the front and back end, and the gateway addons
//
function create_new($log_type, $title = '', $detail = '', $add_request_data = false)
{
    $this->initData();
    $this->data->log_type = $log_type;
    $this->data->title = $title;
    $this->data->detail = $detail;
	if ($add_request_data)
		{
		$this->data->detail .= '<pre>';
		if (!empty($_GET))
			$this->data->detail .= "Get data: ".print_r($_GET,true);
		if (!empty($_POST))
			$this->data->detail .= "Post data: ".print_r($_POST,true);
		$this->data->detail .= "Server data: ".print_r($_SERVER,true);
		$this->data->detail .= '</pre>';
		}
    $query = $this->ladb_makeQuery($this->data, '#__payage_syslog');
	$result = $this->ladb_execute($query);
	if ($result === false)
		{
		if ($this->app->isClient('site'))
			LAPG_trace::trace($this->ladb_error_text, $query);
		else
			$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return false;
		}
	$new_id = $this->_db->insertId();	// if this was an insert, get the id of the new row
	if ($new_id > 0)
		$this->data->id = $new_id;		// save the new id in $this->data
	return true;
}

//-------------------------------------------------------------------------------
// Return a pointer to the pagination object
//
function getPagination()
{
	if ($this->_pagination == Null)
		$this->_pagination = new JPagination(0,0,0);
	return $this->_pagination;
}

//-------------------------------------------------------------------------------
// Get the list of Log entries
//
function getList()
{
	$filter_state = $this->app->getUserStateFromRequest(LAPG_COMPONENT.'.filter_state','filter_state','','word');
	$limit = $this->app->get('list_limit');
	$limitstart = $this->app->getUserStateFromRequest(LAPG_COMPONENT.'.log', 'limitstart', 0, 'int');
	$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0); // In case limit has been changed
	$search = $this->app->getUserStateFromRequest(LAPG_COMPONENT.'.log_search','log_search','','string');
	$search	 = mb_strtolower($search);

	$query_count = "Select count(*) ";
	$query_cols  = "Select * ";
	$query_from  = "From `#__payage_syslog`";
	$query_where = " Where 1";    
    if (!empty($search))
        {
        if (is_numeric(substr($search,0,4)))
			{
			if (LAPG_admin::validDate($search))					// YYYY-MM-DD
	            $query_where .=  " AND DATE(`date_time`) = ".$this->_db->Quote($search);
			if ((strlen($search) == 7) && LAPG_admin::validDate($search.'-01'))			// YYYY-MM
				{
				$search_year = substr($search,0,4);
				$search_month = substr($search,5,2);
				$query_where .= " AND YEAR(`date_time`) = $search_year AND MONTH(`date_time`) = $search_month ";
				}
			if (strlen($search) == 4)							// YYYY
				$query_where .= " AND YEAR(`date_time`) = $search ";
			}
        else
            {
            $quoted_search_string = $this->_db->Quote('%'.$search.'%');
            $query_where .= " AND (lower(`title`) Like ".$quoted_search_string." OR lower(`detail`) Like ".$quoted_search_string.")";
            }
        }

	$query_order = " Order by `id` DESC";
    
// get the total row count

	$count_query = $query_count.$query_from.$query_where;
	$total = $this->ladb_loadResult($count_query);
	if ($total === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text.'<br />'.$count_query, 'error');
		return array();
		}

	if ($limitstart > $total)
		$limitstart = 0;
	$this->_pagination = new JPagination($total, $limitstart, $limit);

// get the data	

	$main_query = $query_cols.$query_from.$query_where.$query_order;
    LAPG_trace::trace("Log getList: ".$main_query);
	$this->data = $this->ladb_loadObjectList($main_query, $limitstart, $limit);
	if ($this->data === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text.'<br />'.$main_query, 'error');
		return array();
		}
	return $this->data;
}

//-------------------------------------------------------------------------------
// delete one or more items
//
function delete()
{
	$jinput = JFactory::getApplication()->input;
	$cids = $jinput->get( 'cid', array(), 'ARRAY' );
	Joomla\Utilities\ArrayHelper::toInteger($cids);
	foreach ($cids as $cid)
		{
		$query = "delete from `#__payage_syslog` where `id` = $cid";
		$result = $this->ladb_execute($query);
		if ($result === false)
			{
			$this->app->enqueueMessage($this->ladb_error_text, 'error');
			return false;
			}
		}
	return true;
}

}