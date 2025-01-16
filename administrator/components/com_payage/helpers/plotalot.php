<?php
/********************************************************************
Product    : Plotalot
Date       : 15 January 2022
Copyright  : Les Arbres Design 2009-2022
Contact    : https://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

if (!defined('PLOTALOT_VERSION'))
	{
	define("PLOTALOT_VERSION","P6.20");
	define("PLOTALOT_COMPONENT", "com_plotalot");

	define("CHART_MAX_PLOTS", 20);
	define("PLOTALOT_TRACE_FILE", JPATH_ROOT.'/components/com_plotalot/trace.txt');

	define("CHART_TYPE_ANY", 0);
	define("CHART_TYPE_PL_TABLE", 1);		// HTML Table generated internally by Plotalot
	define("CHART_TYPE_PL_TABLE_CSS", 2);	// CSS Table generated internally by Plotalot
	define("CHART_TYPE_SINGLE_ITEM",10);
	define("CHART_TYPE_GV_TABLE", 20);		// Google Visualization table
	define("CHART_TYPE_LINE", 100);
	define("CHART_TYPE_AREA", 110);
	define("CHART_TYPE_SCATTER", 200);
	define("CHART_TYPE_BAR_H_STACK", 300);
	define("CHART_TYPE_BAR_H_GROUP", 310);
	define("CHART_TYPE_BAR_V_STACK", 320);
	define("CHART_TYPE_BAR_V_GROUP", 330);
	define("CHART_TYPE_PIE_2D", 400);
	define("CHART_TYPE_PIE_3D", 410);
	define("CHART_TYPE_PIE_2D_V", 420);
	define("CHART_TYPE_PIE_3D_V", 430);
	define("CHART_TYPE_GAUGE", 500);
	define("CHART_TYPE_TIMELINE", 520);
	define("CHART_TYPE_BUBBLE", 530);
	define("CHART_TYPE_COMBO_STACK", 540);
	define("CHART_TYPE_COMBO_GROUP", 550);
	define("CHART_TYPE_CANDLESTICK", 560);
	define("CHART_TYPE_ORG", 600);
	define("CHART_TYPE_TREEMAP", 610);
	define("CHART_TYPE_GEO", 620);
	define("CHART_TYPE_GANTT", 630);
	define("CHART_TYPE_CALENDAR", 640);
	define("CHART_TYPE_ANNOTATION", 650);

	define("CHART_CATEGORY_TABLE", 1);
	define("CHART_CATEGORY_BAR", 2);
	define("CHART_CATEGORY_PIE", 3);
	define("CHART_CATEGORY_COMBO", 4);
	define("CHART_CATEGORY_SAMPLE",20);

	define("FORMAT_NONE",0);
	define("FORMAT_NUM_UK_0", 10);
	define("FORMAT_NUM_UK_1", 20);
	define("FORMAT_NUM_UK_2", 30);
	define("FORMAT_NUM_FR_0", 40);
	define("FORMAT_NUM_FR_1", 50);
	define("FORMAT_NUM_FR_2", 60);
	define("FORMAT_DATE_TIME_MIN",100);     // not a real format, used for validation
	define("FORMAT_DATE_DMY",100);
	define("FORMAT_DATE_YMD_NOTIME",110);
	define("FORMAT_DATE_MDY",120);
	define("FORMAT_DATE_DMONY",125);
	define("FORMAT_DATE_DM",130);
	define("FORMAT_DATE_DMON",135);
	define("FORMAT_DATE_MD",140);
	define("FORMAT_DATE_MY",144);
	define("FORMAT_DATE_MONY",145);
	define("FORMAT_DATE_Y",150);
	define("FORMAT_DATE_M",160);
	define("FORMAT_DATE_MON",161);
	define("FORMAT_DATE_MONTH",162);
	define("FORMAT_DATE_D",170);
	define("FORMAT_DATE_DAY",180);
	define("FORMAT_TIME_HHMM",190);
	define("FORMAT_TIME_HHMMSS",195);
	define("FORMAT_TIME_HH",200);
	define("FORMAT_TIME_MM",210);
	define("FORMAT_CUSTOM_DATE", 299);
	define("FORMAT_DATE_TIME_MAX",299);     // not a real format, used for validation
	define("FORMAT_PERCENT_0", 300);
	define("FORMAT_PERCENT_1", 310);
	define("FORMAT_PERCENT_2", 320);
    
	define("PLOT_STYLE_NORMAL", 0);
	define("LINE_THIN_SOLID", 20);
	define("LINE_THICK_SOLID", 40);
	define("PIE_LIGHT_GRADIENT", 60);
	define("PIE_DARK_GRADIENT", 70);
	define("PIE_MULTI_COLOUR", 80);

	define("LEGEND_NONE", 0);
	define("LEGEND_IN", 10);	// previously this value was called "Left or in", but evaluated as "in"
	define("LEGEND_RIGHT", 20);
	define("LEGEND_TOP", 30);
	define("LEGEND_BOTTOM", 40);
	define("LEGEND_LEFT", 50);
	define("LEGEND_LABELLED", 60);

	define("PIE_TEXT_NONE",    0);
	define("PIE_TEXT_PERCENT", 1);
	define("PIE_TEXT_VALUE",   2);
	define("PIE_TEXT_LABEL",   3);
	
	define("COMBO_PLOT_TYPE_LINE_NORMAL",  0);
	define("COMBO_PLOT_TYPE_LINE_THIN",    20);
	define("COMBO_PLOT_TYPE_LINE_THICK",   40);
	define("COMBO_PLOT_TYPE_AREA",         50);
	define("COMBO_PLOT_TYPE_BARS",         60);
	define("COMBO_PLOT_TYPE_CANDLESTICKS", 70);
	define("COMBO_PLOT_TYPE_STEPPEDAREA",  80);

	define("GEO_MODE_REGION", 0);
	define("GEO_MODE_MARKER_ADDRESS", 1);
	define("GEO_MODE_MARKER_LATLONG", 2);
	define("GEO_MODE_TEXT", 3);
	
	define("GV_DATA_TYPE_NUMBER",   0);
	define("GV_DATA_TYPE_STRING",   1);
	define("GV_DATA_TYPE_FORMAT_X", 2);
	define("GV_DATA_TYPE_EXTRA",    3);
	define("GV_DATA_TYPE_DATE",     4);
	define("GV_DATA_TYPE_DATETIME", 5);

	define("DESIGN_CLASSIC", 0);
	define("DESIGN_MATERIAL", 1);
	}

if (class_exists("Plotalot"))
	return;

class Plotalot
{
var $error = '';				// error message to be returned
var $warning = '';				// warning to be returned
var $chart_script = '';			// the chart script to be returned
var $chart_data = null;			// the chart data definition structure
var $datasets = array();		// the dataset arrays
var $total_rows = 0;			// total number of rows from all queries
var $active_plots = 0;			// number of plots that have rows
var $chart_title = '';			// the resolved chart title
var $x_title = '';				// the resolved X axis title
var $y_title = '';				// the resolved Y axis title
var $chart_x_min;				// overall minimum X value for all datasets
var $chart_x_max;				// overall maximum X value for all datasets
var $chart_y_min;				// overall minimum Y value for all datasets
var $chart_y_max;				// overall maximum Y value for all datasets

//-------------------------------------------------------------------------------
// Constructor
//
function __construct()
{
	$this->joomla_app = JFactory::getApplication();
	$this->joomla_dbname = $this->joomla_app->get('db');
	$this->joomla_dbprefix = $this->joomla_app->get('dbprefix');
	$this->joomla_dbhost = $this->joomla_app->get('host');
	$this->joomla_dbuser = $this->joomla_app->get('user');
	$this->joomla_dbpassword = $this->joomla_app->get('password');
	$this->plotalot_version = PLOTALOT_VERSION;
}

//-------------------------------------------------------------------------------
// log data to the trace file
function _trace($data)
{
	if ($this->trace != 0)
		@file_put_contents(PLOTALOT_TRACE_FILE, "$data \n",FILE_APPEND);
}

//-------------------------------------------------------------------------------
// set an error and log it
function _error($data)
{
	$this->error = $data;
	if ($this->trace != 0)
		@file_put_contents(PLOTALOT_TRACE_FILE, "ERROR: $data \n",FILE_APPEND);
}

//-------------------------------------------------------------------------------
//set a warning and log it
function _warning($data)
{
	if ($this->warning != '')
		$this->warning .= '; ';
	$this->warning .= $data;
	if ($this->trace != 0)
		@file_put_contents(PLOTALOT_TRACE_FILE, "WARNING: $data \n",FILE_APPEND);
}

//-------------------------------------------------------------------------------
// Draw a single item
// returns the first column of the first row, or an error message
//
function _drawSingleItem()
{
	$num_rows = $this->datasets[0]['num_rows'];
	$this->chart_script = '';
	if ($num_rows != 0)
		$this->chart_script = $this->datasets[0]['data'][0][0];		// first column of first row of first dataset
	$this->_trace("Single Item returning: ".$this->chart_script);
	return $this->chart_script; 									// the plugin takes the return value from chart_script
}

//-------------------------------------------------------------------------------
// Draw a (Plotalot) table or a single item
// returns the table or item, or an error message
//
function _drawHtmlTable()
{
// table data always uses plot zero
			
	$num_rows = $this->datasets[0]['num_rows'];
	$num_columns = $this->datasets[0]['num_columns'];

// make the classes

	if (!empty($this->chart_data->style_array['pl_table']))
		$table_style = ' class="'.$this->chart_data->style_array['pl_table'].'"';
	else
		$table_style = '';

	if (!empty($this->chart_data->style_array['pl_title']))
		$title_style = ' class="'.$this->chart_data->style_array['pl_title'].'"';
	else
		$title_style = '';

	if (!empty($this->chart_data->style_array['pl_head']))
		$heading_style = ' class="'.$this->chart_data->style_array['pl_head'].'"';
	else
		$heading_style = '';

	if (!empty($this->chart_data->style_array['pl_odd']))
		$odd_style = ' class="'.$this->chart_data->style_array['pl_odd'].'"';
	else
		$odd_style = '';

	if (!empty($this->chart_data->style_array['pl_even']))
		$even_style = ' class="'.$this->chart_data->style_array['pl_even'].'"';
	else
		$even_style = '';

// table style is from the chart record

    $this->chart_script = "\n<table ".$this->chart_data->chart_css_style.' '.$table_style.'>';

// if title is non-blank, draw a heading row    

	$this->chart_title = $this->_resolveQuery($this->chart_data->chart_title);

	$headrows = '';
    if ($this->chart_title != '')
    	$headrows .= "\n<tr".$title_style.'><th colspan="'.$num_columns.'">'.$this->chart_title.'</th></tr>';

// if legend is set, show column headings
// the column names are stored escaped, but here we need them unescaped

	if ($this->chart_data->legend_type > LEGEND_NONE)
		{
		$headrows .= "\n<tr".$heading_style.'>';
		for ($i=0; $i < $num_columns; $i++)
            {
            $escaped_column_name = $this->datasets[0]['column_names'][$i];
            $raw_column_name = json_decode('"'.$escaped_column_name.'"');
			$headrows .= '<th>'.$raw_column_name.'</th>';
            }
		$headrows .= "</tr>";
		}
		
	if ($headrows != '')
		$this->chart_script .= "\n<thead>".$headrows."\n</thead>";
		
// draw all the rows

	$this->chart_script .= "\n<tbody>";

	$odd = true;
    for ($r=0; $r < $num_rows; $r++)
    	{
    	if ($odd)
    		$style = $odd_style;
    	else
    		$style = $even_style;
	    $this->chart_script .=   "\n<tr$style>";
	    $row = $this->datasets[0]['data'][$r];
		for ($c=0; $c < $num_columns; $c++)
			{
			$value = $row[$c];
			$this->chart_script .= '<td>'.$value.'</td>';
			}
	    $this->chart_script .= "</tr>";
	    $odd = !$odd;			// next row is opposite
	    }
	$this->chart_script .= "\n</tbody>";

// totals	

	if (!empty($this->chart_data->column_totals))
		{
		$totals = $this->calc_table_totals();
		$this->chart_script .= "\n<tfoot><tr>";
		for ($i=0; $i < $num_columns; $i++)
			$this->chart_script .= '<td>'.$totals[$i].'</td>';
		$this->chart_script .= "</tr></tfoot>";
		}

// done

    $this->chart_script .= "\n</table>\n";
	$length = strlen($this->chart_script);
	$this->_trace("Table length: ".$length);
	$this->_trace("Table: ".$this->chart_script);
	if ($this->trace != 0)
		$this->_trace("Returning Table: ".$this->chart_script);
    return $this->chart_script;
}

//-------------------------------------------------------------------------------
// Draw a (Plotalot) CSS table or a single item
// returns the table or item, or an error message
//
function _drawCssTable()
{
// table data always uses plot zero
			
	$num_rows = $this->datasets[0]['num_rows'];
	$num_columns = $this->datasets[0]['num_columns'];

// get the raw column names

	$raw_column_names = array();
	for ($i=0; $i < $num_columns; $i++)
		{
		$escaped_column_name = $this->datasets[0]['column_names'][$i];
		$raw_column_names[$i] = json_decode('"'.$escaped_column_name.'"');
		}

	if (!empty($this->chart_data->style_array['plr_outer']))
		$outer_class = ' '.$this->chart_data->style_array['plr_outer'];
	else
		$outer_class = '';

// create the outer div

	$outer_style = '';
	if (!empty($this->chart_data->x_size) || !empty($this->chart_data->y_size))
		{
		$outer_style = ' style="overflow:scroll;';
		if (!empty($this->chart_data->x_size))
			$outer_style .= 'width:'.$this->chart_data->x_size.'px;';
		if (!empty($this->chart_data->y_size))
			$outer_style .= 'height:'.$this->chart_data->y_size.'px';
		$outer_style .= '"';
		}
    $this->chart_script = "\n".'<div class="pct-outer'.$outer_class.'"'.$outer_style.'>';

// if title is non-blank, draw a heading row    

	$this->chart_title = $this->_resolveQuery($this->chart_data->chart_title);

    if ($this->chart_title != '')
    	$this->chart_script .= '<div class="pct-title">'.$this->chart_title.'</div>';

    $this->chart_script .= '<div class="pct-table">';

// if legend is set, show column headings
// the column names are stored escaped, but here we need them unescaped

	$headrows = '';
	if ($this->chart_data->legend_type > LEGEND_NONE)
		{
		$headrows .= '<div class="pct-head-row">';
		for ($i=0; $i < $num_columns; $i++)
			$headrows .= '<div class="pct-head-cell">'.$raw_column_names[$i].'</div>';
		$headrows .= '</div>';
		}
		
	if ($headrows != '')
		$this->chart_script .= '<div class="pct-head">'.$headrows.'</div>';
		
// draw all the rows

	$this->chart_script .= '<div class="pct-body">';

	$odd = true;
    for ($r=0; $r < $num_rows; $r++)
    	{
    	if ($odd)
    		$style = 'pct-odd';
    	else
    		$style = 'pct-even';
	    $this->chart_script .=   '<div class="pct-body-row '.$style.'">';
	    $row = $this->datasets[0]['data'][$r];
		for ($c=0; $c < $num_columns; $c++)
			{
			$value = $row[$c];
			$this->chart_script .= '<div class="pct-body-cell"><span class="pct-cell-name">'.$raw_column_names[$c].'</span><span class="pct-cell-value">'.$value.'</span></div>';
			}
	    $this->chart_script .= "</div>";
	    $odd = !$odd;			// next row is opposite
	    }
	$this->chart_script .= "</div>";			// div class="pct-body"

// totals	

	if (!empty($this->chart_data->column_totals))
		{
		$totals = $this->calc_table_totals();
		$this->chart_script .= '<div class="pct-foot">';
	    $this->chart_script .=   '<div class="pct-body-row">';
		for ($i=0; $i < $num_columns; $i++)
			$this->chart_script .= '<div class="pct-body-cell"><span class="pct-cell-name">'.$raw_column_names[$i].'</span><span class="pct-cell-value">'.$totals[$i].'</span></div>';
		$this->chart_script .= "</div>";			// div class="pct-body-row"
		$this->chart_script .= "</div>";			// div class="pct-foot"
		}

// done

    $this->chart_script .= "</div>";			// div class="pct-table"
    $this->chart_script .= "</div>";			// div class="pct-outer"
	$length = strlen($this->chart_script);
	$this->_trace("Table length: ".$length);
	$this->_trace("Table: ".$this->chart_script);
	if ($this->trace != 0)
		$this->_trace("Returning Table: ".$this->chart_script);
    return $this->chart_script;
}

//-------------------------------------------------------------------------------
// Unformat a number so that we can add it to a column total
//
function unformat_number($number_string, $decimal_separator, $thousands_separator)
{
	if (is_numeric($number_string))
		return $number_string;                           		// it's already a number
	$number = str_replace(' ','',$number_string);        		// remove any spaces
	$number = str_replace($thousands_separator,'',$number);     // remove any thousands separators
	if ($decimal_separator != '.')
		$number = str_replace($decimal_separator,'.',$number);  // change decimal separator to .
	if (is_numeric($number))                             		// if it's now a number
		return $number;                                 		// .. return the converted number
	else
		return $number_string;                           		// failed to unformat so return original string
}

//-------------------------------------------------------------------------------
// Calculate totals for tables
// column_totals are specified as: column_list {;decimals {;decimal_separator {;thousands_separator}}}
//   e.g. "1,2,3" or "1,2,3;2" or "1,2,3;2;,;."
//
function calc_table_totals()
{
	$num_rows = $this->datasets[0]['num_rows'];
	$num_columns = $this->datasets[0]['num_columns'];
	$column_totals_parts = explode(';', $this->chart_data->column_totals);		// separate the column numbers from the format specifiers
	$column_totals = explode(',', $column_totals_parts[0]);						// the column numbers, e.g. 1,2,3
	$decimals = intval(isset($column_totals_parts[1])?$column_totals_parts[1]:0);
	$decimal_separator = (isset($column_totals_parts[2])?$column_totals_parts[2]:'.');    // default to .
	$thousands_separator = (isset($column_totals_parts[3])?$column_totals_parts[3]:'');   // default to none
	$totals = array();
	for ($c=0; $c < $num_columns; $c++)
		if (in_array(($c + 1), $column_totals))
			$totals[$c] = 0;
		else
			$totals[$c] = '';
    for ($r=0; $r < $num_rows; $r++)
		{
	    $row = $this->datasets[0]['data'][$r];
		for ($c=0; $c < $num_columns; $c++)
			if (in_array(($c+1), $column_totals) && ($totals[$c] != '#') )
				{
				$value = $this->unformat_number($row[$c], $decimal_separator, $thousands_separator);
				if (is_numeric($value)) 
					$totals[$c] += $value;
				else
					$totals[$c] = '#';		// any non-numeric value stops any further totalling for the column
				}
		}
	for ($c=0; $c < $num_columns; $c++)
		{
		if (empty($totals[$c]))
			continue;
		if ($totals[$c] == '#')
			$totals[$c] = $num_rows;
		else
			$totals[$c] = number_format($totals[$c], $decimals, $decimal_separator, $thousands_separator);
		}
	return $totals;	
}

//-------------------------------------------------------------------------------
// Format a value into a string format
// - this is only used for pie charts
//
function _formatValue($value,$format)
{
	switch ($format)
		{
		case FORMAT_NONE       : return $value;
		case FORMAT_NUM_UK_0   : return number_format($value);
		case FORMAT_NUM_UK_1   : return number_format($value,1);
		case FORMAT_NUM_UK_2   : return number_format($value,2);
		case FORMAT_NUM_FR_0   : return number_format($value,0,',',' ');
		case FORMAT_NUM_FR_1   : return number_format($value,1,',',' ');
		case FORMAT_NUM_FR_2   : return number_format($value,2,',',' ');
		case FORMAT_DATE_DMY   : return strftime("%d/%m/%y",$value);
		case FORMAT_DATE_MDY   : return strftime("%m/%d/%y",$value);
		case FORMAT_DATE_DMONY : return strftime("%d/%b/%y",$value);
		case FORMAT_DATE_DM    : return strftime("%d/%m",$value);
		case FORMAT_DATE_DMON  : return strftime("%d %b",$value);
		case FORMAT_DATE_MD    : return strftime("%m/%d",$value);
		case FORMAT_DATE_MY    : return strftime("%m/%y",$value);
		case FORMAT_DATE_MONY  : return strftime("%b/%y",$value);
		case FORMAT_DATE_Y     : return strftime("%y",$value);
		case FORMAT_DATE_M     : return strftime("%m",$value);
		case FORMAT_DATE_MON   : return strftime("%b",$value);
		case FORMAT_DATE_MONTH : return strftime("%B",$value);
		case FORMAT_DATE_D     : return strftime("%d",$value);
		case FORMAT_DATE_DAY   : return strftime("%a",$value);
		case FORMAT_TIME_HHMM  : return strftime("%H:%M",$value);
		case FORMAT_TIME_HHMMSS: return strftime("%H:%M:%S",$value);
		case FORMAT_TIME_HH    : return strftime("%H",$value);
		case FORMAT_TIME_MM    : return strftime("%M",$value);
		case FORMAT_PERCENT_0  : return number_format($value).'%';
		case FORMAT_PERCENT_1  : return number_format($value,1).'%';
		case FORMAT_PERCENT_2  : return number_format($value,2).'%';
        case FORMAT_CUSTOM_DATE: return strftime("%y/%m/%d",$value);    // custom is not really supported here but just in case
		default: return "Invalid format $format";
		}
}

//-------------------------------------------------------------------------------
// Format a number for use in a Google charts data object
//
function _gcFormatNumber($value,$format)
{
	if (!is_numeric($value))	// we can get here with a string for chart types that allow string or numeric X axes
		return $value;			// the only valid option for strings is FORMAT_NONE
	switch ($format)
		{
		case FORMAT_NONE       : return $value;
		case FORMAT_NUM_UK_0   : return number_format($value,0,'.','');
		case FORMAT_NUM_UK_1   : return number_format($value,1,'.','');
		case FORMAT_NUM_UK_2   : return number_format($value,2,'.','');
		case FORMAT_NUM_FR_0   : return number_format($value,0,'.','');
		case FORMAT_NUM_FR_1   : return number_format($value,1,'.','');
		case FORMAT_NUM_FR_2   : return number_format($value,2,'.','');
		case FORMAT_PERCENT_0  : 
		case FORMAT_PERCENT_1  : 
		case FORMAT_PERCENT_2  : 
			return $value;
		case FORMAT_DATE_DMY   : 
		case FORMAT_DATE_MDY   : 
		case FORMAT_DATE_DMONY : 
		case FORMAT_DATE_DM    : 
		case FORMAT_DATE_DMON  : 
		case FORMAT_DATE_MD    : 
		case FORMAT_DATE_MY    : 
		case FORMAT_DATE_MONY  : 
		case FORMAT_DATE_Y     : 
		case FORMAT_DATE_M     : 
		case FORMAT_DATE_MON   : 
		case FORMAT_DATE_MONTH : 
		case FORMAT_DATE_D     : 
		case FORMAT_DATE_DAY   : 
		case FORMAT_TIME_HHMM  : 
		case FORMAT_TIME_HHMMSS: 
		case FORMAT_TIME_HH    : 
		case FORMAT_TIME_MM    :
        case FORMAT_CUSTOM_DATE:
			if (empty($value))
				return 'null';
			else
				return "new Date(".gmstrftime("%Y",$value).', '.(gmstrftime("%m",$value) - 1).', '.gmstrftime("%d, %H, %M, %S",$value).")";
		case FORMAT_DATE_YMD_NOTIME:
			if (empty($value))
				return 'null';
			else
				return "new Date(".gmstrftime("%Y",$value).', '.(gmstrftime("%m",$value) - 1).', '.gmstrftime("%d",$value).")";
		default: return "Invalid format $format";
		}
}

//-------------------------------------------------------------------------------
// Return a Google charts format string
//
function _gcFormatString($format)
{
	switch ($format)
		{
		case FORMAT_NONE       : return "";
		case FORMAT_NUM_UK_0   : return "#,##0";
		case FORMAT_NUM_UK_1   : return "#,##0.0";
		case FORMAT_NUM_UK_2   : return "#,##0.00";
		case FORMAT_NUM_FR_0   : return "#,##0";
		case FORMAT_NUM_FR_1   : return "#,##0.0";
		case FORMAT_NUM_FR_2   : return "#,##0.00";
		case FORMAT_DATE_DMY   : return "dd/MM/yy";
		case FORMAT_DATE_MDY   : return "MM/dd/yy";
		case FORMAT_DATE_DMONY : return "dd/MMM/yy";
		case FORMAT_DATE_DM    : return "dd/MM";
		case FORMAT_DATE_DMON  : return "dd MMM";
		case FORMAT_DATE_MD    : return "MM/dd";
		case FORMAT_DATE_MY    : return "MM/yy";
		case FORMAT_DATE_MONY  : return "MMM/yy";
		case FORMAT_DATE_Y     : return "yy";
		case FORMAT_DATE_M     : return "MM";
		case FORMAT_DATE_MON   : return "MMM";
		case FORMAT_DATE_MONTH : return "MMMM";
		case FORMAT_DATE_D     : return "dd";;
		case FORMAT_DATE_DAY   : return "EEE";
		case FORMAT_TIME_HHMM  : return "kk:mm";
		case FORMAT_TIME_HHMMSS: return "kk:mm:ss";
		case FORMAT_TIME_HH    : return "kk";
		case FORMAT_TIME_MM    : return "mm";
		case FORMAT_PERCENT_0  : return "#,##0'%'";
		case FORMAT_PERCENT_1  : return "#,##0.0'%'";
		case FORMAT_PERCENT_2  : return "#,##0.00'%'";
        case FORMAT_CUSTOM_DATE:
            if (!empty($this->chart_data->custom_x_format))
                return str_replace(array("'",'"','<','>'),"",$this->chart_data->custom_x_format);
            else
                {
                return "yy/MM/dd";
                }
		default                : return "";
		}
}

//-------------------------------------------------------------------------------
// Return the format type for use in a Google charts data object
//
function _gcFormatType($format)
{
	switch ($format)
		{
		case FORMAT_NONE       : 
		case FORMAT_NUM_UK_0   : 
		case FORMAT_NUM_UK_1   : 
		case FORMAT_NUM_UK_2   : 
		case FORMAT_NUM_FR_0   : 
		case FORMAT_NUM_FR_1   : 
		case FORMAT_NUM_FR_2   : 
		case FORMAT_PERCENT_0  : 
		case FORMAT_PERCENT_1  : 
		case FORMAT_PERCENT_2  : 
			return 'number';
		case FORMAT_DATE_DMY   : 
		case FORMAT_DATE_MDY   : 
		case FORMAT_DATE_DMONY : 
		case FORMAT_DATE_DM    : 
		case FORMAT_DATE_DMON    : 
		case FORMAT_DATE_MD    : 
		case FORMAT_DATE_MY    : 
		case FORMAT_DATE_MONY  : 
		case FORMAT_DATE_Y     : 
		case FORMAT_DATE_M     : 
		case FORMAT_DATE_MON   : 
		case FORMAT_DATE_MONTH : 
		case FORMAT_DATE_D     : 
		case FORMAT_DATE_DAY   :
        case FORMAT_CUSTOM_DATE:
			return 'datetime';		// 9.00 (previously 'date')
		case FORMAT_TIME_HHMM  : 
		case FORMAT_TIME_HHMMSS: 
		case FORMAT_TIME_HH    : 
		case FORMAT_TIME_MM    : 
			return 'datetime';
		default: return 'number';
		}
}

//-------------------------------------------------------------------------------
// Return the marker type for use in a combo chart
//
function _gcMarkerType($type)
{
	switch ($type)
		{
		case COMBO_PLOT_TYPE_LINE_NORMAL:  return 'line';
		case COMBO_PLOT_TYPE_LINE_THIN:    return 'line';
		case COMBO_PLOT_TYPE_LINE_THICK:   return 'line';
		case COMBO_PLOT_TYPE_AREA:         return 'area';
		case COMBO_PLOT_TYPE_BARS:         return 'bars';
		case COMBO_PLOT_TYPE_CANDLESTICKS: return 'candlesticks';
		case COMBO_PLOT_TYPE_STEPPEDAREA:  return 'steppedArea';
		default:                           return 'line';
		}
}

// -------------------------------------------------------------------------------
// Special characters in titles and strings need to be converted to Javascript Unicode Escape Sequences
//
function _escape_string($str)
{
	$str = json_encode($str);               // json_encode() does a good job but encloses the result in double quotes
    $str = substr($str,1,-1);               // remove the quotes
    return str_replace("'",'\u0027',$str);  // encode any single quotes
}

//-------------------------------------------------------------------------------
// return a string surrounded by single quotes
// with any single quotes in the string escaped and CRLF's removed
//
function _quote(&$str)
{
	$newstr = str_replace("'","\\'",$str);	                // replace ' with \'
	$newstr = str_replace("\r","",$newstr);	                // remove any CR's
	$newstr = str_replace("\n","",$newstr);	                // remove any LF's
	$newstr = str_replace("<script","<...",$newstr);	    // remove any <script tags
	$newstr = str_replace("</script>","</...>",$newstr);	// remove any </script> tags
	return "'".$newstr."'";
}

//-------------------------------------------------------------------------------
// Make sure a plot style matches the current chart type
// returns either the current plot_style or a more suitable one
//
function checkPlotStyle($chart_type, $plot_style)
{
	switch ($chart_type)
		{
		case CHART_TYPE_LINE:
		case CHART_TYPE_AREA:
			if (($plot_style >= PLOT_STYLE_NORMAL) && ($plot_style <= LINE_THICK_SOLID))
				return $plot_style;
			return PLOT_STYLE_NORMAL;
			break;
			
		case CHART_TYPE_PIE_2D:
		case CHART_TYPE_PIE_3D:
		case CHART_TYPE_PIE_2D_V:
		case CHART_TYPE_PIE_3D_V:
			if ($plot_style == PLOT_STYLE_NORMAL)
				return $plot_style;
			if (($plot_style >= PIE_LIGHT_GRADIENT) && ($plot_style <= PIE_MULTI_COLOUR))
				return $plot_style;
			return PLOT_STYLE_NORMAL;
			break;
			
		case CHART_TYPE_COMBO_STACK:
		case CHART_TYPE_COMBO_GROUP:
			if (($plot_style >= COMBO_PLOT_TYPE_LINE_NORMAL) && ($plot_style <= COMBO_PLOT_TYPE_STEPPEDAREA))
				return $plot_style;
			return COMBO_PLOT_TYPE_LINE_NORMAL;
			break;
		}
	return 0;
}

//-------------------------------------------------------------------------------
// Replace all occurrences of special variable with their values
//
function _resolveSpecialVariable(&$query)
{
	$this->_trace("Resolving $query");

// replace __# with the required db prefix

	if (!empty($this->chart_data->db_name) && !empty($this->chart_data->db_prefix))
        $new_query = str_replace('#__', $this->chart_data->db_prefix, $query);
    else    
    	$new_query = str_replace("#__",$this->joomla_dbprefix, $query);

// try to get the Joomla user object

	$user = JFactory::getUser();
	if ($user == null)
		$this->_trace("  Joomla user object not available");
	else
		{
		$new_query = str_replace("%%J_USER_ID%%",$user->id,$new_query);
		$new_query = str_replace("%%J_USER_NAME%%",$user->name,$new_query);
		$new_query = str_replace("%%J_USER_USERNAME%%",$user->username,$new_query);
		$new_query = str_replace("%%J_USER_EMAIL%%",$user->email,$new_query);
		$new_query = str_replace("%%J_USER_USERTYPE%%",'',$new_query);      // was $user->usertype before Joomla 3
		$new_query = str_replace("%%J_ROOT_PATH%%",JPATH_ROOT, $new_query);
		$new_query = str_replace("%%J_ROOT_URI%%",JURI::root(), $new_query);
        $new_query = str_replace("%%J_CURRENT_DATE%%",JHtml::date('now', 'Y-m-d'),$new_query);
		}

// find and replace all plot_params of the form %%Pn%% or %%Pn=default%%

	$regex = "#%%P[0-9]+=?[^%]*%%#";
	if (preg_match_all($regex, $new_query, $matches, PREG_SET_ORDER) != 0)
		{
		foreach ($matches as $match)
			{
			$param_str = str_replace('%', '', $match[0]);		// remove the % signs
			$param = explode('=',$param_str);					// separate name=default
			$param_num = substr($param[0],1);					// get parameter number
			if (!is_numeric($param_num))						// if not a number, ignore it
				continue;
			$param_value = '';
			if (isset($this->chart_data->json_plot_params[$param_num]))			// plugin > 6.05 passes the parameters here
				{
				$this->_trace("  P$param_num json: ".$this->chart_data->json_plot_params[$param_num]);
				$raw_value = json_decode($this->chart_data->json_plot_params[$param_num]);
                if (is_array($raw_value))
					{
					foreach ($raw_value as $key => $value) 
						$param_value .= $this->db->quote($value).',';
					$param_value = rtrim($param_value,',');
					}
				else
	  				$param_value = $this->db->escape($raw_value);
				}
			if (isset($this->chart_data->plot_params[$param_num]))				// plugin <= 6.05 passes the parameters here
				{
				$this->_trace("  P$param_num value: ".$this->chart_data->plot_params[$param_num]);
				$param_value = $this->chart_data->plot_params[$param_num];
				}
			if (empty($param_value) && isset($param[1]))					// .. if we have a default, use that
				$param_value = $param[1];
			$this->_trace("  P$param_num evaluated as: ".$param_value);
			$new_query = str_replace($match[0],$param_value,$new_query);
			}
		}
		
	if ($query != $new_query)
		{
		$this->_trace("  Resolved query: $new_query");
		$query = $new_query;
		}
}

//-------------------------------------------------------------------------------
// Connect to the chart database
// - returns true for success, false for failure with error detail in $this->error
//
function connectChartDatabase()
{
	if (empty($this->chart_data->db_name))
		{
		$this->_trace("Using site database");
		return true;
		}
		
// try to connect to the external database

	$db_driver = trim($this->chart_data->db_driver);
	$db_name = trim($this->chart_data->db_name);
	$db_host = trim($this->chart_data->db_host);
			
	$options = array();
	$options['driver']   = $db_driver;
	$options['database'] = $db_name;
	$options['host']     = $db_host;
	$options['user']     = trim($this->chart_data->db_user);
	$options['password'] = trim($this->chart_data->db_pass);
	$options['prefix']   = $this->chart_data->db_prefix;
	$this->_trace("Connecting to database $db_name at $db_host with $db_driver driver");
	
	if ($db_driver == 'pdo')			// Gives a PHP fatal error 'Cannot instantiate abstract class JDatabaseDriverPdo'
		{
		$this->_trace(" - cannot use driver pdo");
		$this->error = JText::sprintf('JLIB_DATABASE_ERROR_CONNECT_DATABASE',$db_name).' (cannot instantiate abstract class pdo)';
		return false;
		}

	if ($db_driver == '')				// Gives a PHP fatal error 'Cannot instantiate abstract class JDatabaseDriver'
		{
		$this->_trace(" - cannot use blank driver");
		$this->error = JText::sprintf('JLIB_DATABASE_ERROR_CONNECT_DATABASE',$db_name).' (driver not specified)';
		return false;
		}

// try to create a new instance of JDatabaseDriver using the supplied options

	try
		{
		$this->db = JDatabaseDriver::getInstance($options);	
		}
	catch (RuntimeException $e)
		{
		$this->_trace(" - exception: ".$e->getMessage());
		$this->error = $e->getMessage().' (runtime exception)';
		return false;
		}
	
	if ($this->db === NULL)
		{
		$this->_trace(" - null pointer returned");
		$this->error = JText::sprintf('JLIB_DATABASE_ERROR_CONNECT_DATABASE',$db_name).' (null pointer returned)';
		return false;
		}

// we got an object back, check if it works by querying the version information, which we need for the trace anyway

    $this->_trace("Connection instantiated with ".$this->db->name." driver");

	$result = $this->_db_info($this->chart_data->db_driver);
	
	if ($result === false)
		{
		$this->error = $this->ladb_error_text;
		return false;
		}

	$this->_trace("Connected to $db_name ok - target database version  ".$this->chart_db_version);
    
    $collation = @$this->db->getCollation();
    $connection_collation = @$this->db->getConnectionCollation();
	$this->_trace("Collation = $collation, Connection Collation = $connection_collation");
    
    if (!strstr($collation,'utf8') || !strstr($connection_collation,'utf8'))
        {
        $this->_trace("Attempting to set connection to UTF8");
        $this->db->setUtf();            // Calls mysqli::set_charset, or the db-specific equivalent
        $collation = @$this->db->getCollation();
        $connection_collation = @$this->db->getConnectionCollation();
        $this->_trace(" -> Collation = $collation, Connection Collation = $connection_collation");
        }
    
	return true;
}

//-------------------------------------------------------------------------------
// Execute a SQL query and return true if it worked, false if it failed
//
function _execute($query)
{
	$this->_trace($query);
	try
		{
		$this->db->setQuery($query);
		$this->db->execute();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
		$this->_trace('execute error: '.$this->ladb_error_text);
		return false;
		}
	$this->rows_affected = $this->db->getAffectedRows();
	$this->_trace(" - ".$this->rows_affected." affected");
	return true;
}

//-------------------------------------------------------------------------------
// Get a single value from the database as an object and return it, or false if it failed
//
function _loadResult($query)
{
	$this->_trace($query);
	try
		{
		$this->db->setQuery($query);
		$result = $this->db->loadResult();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
		$this->_trace('loadResult error: '.$this->ladb_error_text);
		return false;
		}
	return $result;
}

//-------------------------------------------------------------------------------
// Get an array of rows from the database and return it, or false if it failed
//
function _loadAssocList($query, $limitstart = 0, $limit = 0)
{
	$this->_trace($query);
	try
		{
		$this->db->setQuery($query, $limitstart, $limit);
		$result = $this->db->loadAssocList();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
		$this->_trace('loadAssocList error: '.$this->ladb_error_text);
		return false;
		}
	return $result;
}

//-------------------------------------------------------------------------------
// Get the database version
//
function _db_info($db_driver)
{
	switch ($db_driver)
		{
		case 'sqlite':
			$this->chart_db_version = $this->_loadResult("select sqlite_version();");
			break;
		case 'sqlsrv':
		case 'sqlazure':
			$this->chart_db_version = $this->_loadResult("select  @@Version;");
			break;
		case 'oracle':
			$this->chart_db_version = $this->_loadResult('select * from v$version;');
			break;
		case 'mysql':
		case 'mysqli':
			$this->chart_db_version = $this->_loadResult("select version()");
			break;
		case 'pdomysql':
		case 'postgresql':
		default:
			$this->chart_db_version = $this->_loadResult("select version()");
		}

	return $this->chart_db_version;     // this will be false if we couldn't connect to the database
}

//-------------------------------------------------------------------------------
// Resolve a string that can be a database select
// e.g: SELECT concat("Yesterday ", DATE_FORMAT((CURDATE()-INTERVAL 1 DAY),"%W %D %M %Y"))
// or:  SELECT UNIX_TIMESTAMP(CURDATE() + INTERVAL 1 DAY - interval 1 second) as max
// $query is the potential query
//
function _resolveQuery($query)
{
	$this->_resolveSpecialVariable($query);
	if (strncasecmp($query,"select",6) != 0)
		return $query;

	$result = $this->_loadResult($query);
    if ($result === false)
    	{
		$this->_warning (JText::_('COM_PLOTALOT_WARNING_NO_RESOLVE')." ".$query." - ".$this->ladb_error_text);
		return $query;
		}

	return $result;
}

//-------------------------------------------------------------------------------
// Get a chart dataset for one plot
// returns true or false
// builds:
// $this->datasets[plot_number]['num_rows']
// $this->datasets[plot_number]['num_columns']
// $this->datasets[plot_number]['legend']
// $this->datasets[plot_number]['style']
// $this->datasets[plot_number]['column_names'][]           - these are stored escaped with json_encode() for Google charts and tables
// $this->datasets[plot_number]['column_types'][]
// $this->datasets[plot_number]['numeric'][]
// $this->datasets[plot_number]['data'][]
//
// we do not build a datasets[] array for plots that are disabled or have no query or return zero rows
//
function _getDataSet($plot_number)
{	
	$query = $this->chart_data->plot_array[$plot_number]['query'];
   	$this->datasets[$plot_number]['num_rows'] = 0;
   	$this->datasets[$plot_number]['num_columns'] = 0;
   	
   	if (!isset($this->chart_data->plot_array[$plot_number]['legend']))
   		$this->chart_data->plot_array[$plot_number]['legend'] = LEGEND_NONE;
   		
   	$this->datasets[$plot_number]['legend'] = $this->_resolveQuery($this->chart_data->plot_array[$plot_number]['legend']);
    if (!isset($this->chart_data->plot_array[$plot_number]['style']))
        $this->chart_data->plot_array[$plot_number]['style'] = 0;
    $this->datasets[$plot_number]['style'] = $this->chart_data->plot_array[$plot_number]['style'];
   	
	if ($query == '')
		return true;

	$this->_resolveSpecialVariable($query);

// if we are allowed to run multiple queries, see if that's what we have here
// if this is a multiple query, execute all but the last query,
// then set the last query to be the one that retrieves the data

    if ($this->multiquery)
        {
        $queries = $this->db->splitSql($query);
        $num_queries = count($queries);
        $this->_trace("Multiple query is enabled, plot query has $num_queries queries: ".print_r($queries, true) );
        if ($num_queries > 1)
            {
            for ($i=0; $i < $num_queries; $i++)
                {
                $result = $this->_execute($queries[$i]);
                if ($result !== true)
                    $this->_warning(JText::_('COM_PLOTALOT_PLOT').' '.($plot_number + 1).' '.JText::_('COM_PLOTALOT_QUERY').' '.($i + 1).': '.$this->ladb_error_text);
                }
            $query = $queries[$num_queries - 1];        // set the last query to be the one that retrieves the data
            }
        }

// only allow queries beginning with "select" or "(select" unless component parameter "selectonly" is false

	if ( ((strncasecmp($query,"select",6) != 0) && (strncasecmp($query,"(select",7) != 0)) )
		{										// it's not a select query
		if ($this->select_only)					// .. is it allowed?
			{
			$this->_error(JText::sprintf('COM_PLOTALOT_ERROR_QUERY_CHECK', ($plot_number + 1)));
			return false;
			}
        $this->_trace("Non-select query is enabled");
	    $result = $this->_execute($query);      // execute a non-select query
	    if ($result === true)                   // and store the number of rows affected
	    	{
	    	$this->datasets[$plot_number]['num_rows'] = 1;
	    	$this->datasets[$plot_number]['num_columns'] = 1;
	    	$this->datasets[$plot_number]['column_names'][0] = 'rows_affected';
	    	$this->datasets[$plot_number]['data'][0][0] = $this->rows_affected;
		    }
	    else
	    	$this->_warning(JText::_('COM_PLOTALOT_PLOT').' '.($plot_number + 1).': '.$this->ladb_error_text);
	    return true;
		}
        
// it's a select query so run it
			
    $result_set = $this->_loadAssocList($query);
    if ($result_set === false)
    	{
    	$this->_error(JText::sprintf('COM_PLOTALOT_ERROR_QUERY_FAIL', ($plot_number + 1)).": ".$this->ladb_error_text);
    	return false;
    	}
    	
	$num_rows_raw = count($result_set);
	if ($num_rows_raw == 0)
		$num_columns = 0;
	else
		$num_columns = count($result_set[0]);	

    if ($num_rows_raw == 0)
    	{
    	$this->_warning(JText::_('COM_PLOTALOT_PLOT').' '.($plot_number + 1).': '.JText::_('COM_PLOTALOT_ERROR_NO_ROWS'));
    	$this->_trace("\nPlot $plot_number returned no rows");
    	return true;
    	}

	$this->datasets[$plot_number]['num_columns'] = $num_columns;

// get the column names and whether they are string or numeric

	$this->_getColumnInfo($plot_number, $result_set);

// calculate the skip factor, if any
// for line and area charts there is no point keeping more than one row for every two pixels ($this->chart_data->x_size)
// for tables, the user can specify the maximum number of rows ($this->chart_data->y_labels)

	$skip_factor = 1;					// don't skip any
	if (in_array($this->chart_data->chart_type, array(CHART_TYPE_LINE, CHART_TYPE_AREA)))
		{
		if ($this->chart_data->x_size <= 0)			// size zero means the chart is responsive and could be any size
			$x_size = 1200;							// so assume an arbitrary screen size
		else
			$x_size = $this->chart_data->x_size;
		$skip_factor = intval(floor($num_rows_raw / ($x_size / 2)));	// changed from ceil to floor at v6.15
		if ($skip_factor <= 0)
			$skip_factor = 1;			// don't skip any
		$this->_trace("Plot $plot_number returned $num_rows_raw rows, assuming chart width $x_size pixels, skip factor $skip_factor");
		}

	if (in_array($this->chart_data->chart_type, array(CHART_TYPE_PL_TABLE, CHART_TYPE_PL_TABLE_CSS, CHART_TYPE_GV_TABLE)))
		{
		if ($this->chart_data->y_labels == -1)	// max rows of -1 means don't skip any
			$skip_factor = 1;					// don't skip any
		else
			{
			if ($this->chart_data->y_labels <= 0)	// protect against divide by zero
				$this->chart_data->y_labels = 1;
			$skip_factor = intval(floor($num_rows_raw / $this->chart_data->y_labels));		// changed from ceil to floor at v6.15
			if ($skip_factor <= 0)
				$skip_factor = 1;			// don't skip any
			}
		$this->_trace("Query returned $num_rows_raw rows, max-rows ".$this->chart_data->y_labels.", skip factor $skip_factor");
		}

// save the data rows to the datasets array based on the skip factor

    for ($i=0; $i < $num_rows_raw; $i += $skip_factor)
    	$this->datasets[$plot_number]['data'][] = array_values((array) $result_set[$i]);

	$last_raw_row = $num_rows_raw - 1;
	if (($i - $skip_factor) != $last_raw_row)			// make sure we included the last row
    	$this->datasets[$plot_number]['data'][] = array_values((array) $result_set[$last_raw_row]);

    $num_rows_filtered = count($this->datasets[$plot_number]['data']);
    $this->datasets[$plot_number]['num_rows'] = $num_rows_filtered;
	
// fix nulls if required

	if ($this->fixnulls)
		{
		for ($r=0; $r < $num_rows_filtered; $r++)
			for ($c=0; $c < $num_columns; $c++)
				if (is_null($this->datasets[$plot_number]['data'][$r][$c]))
					{
					if ($this->datasets[$plot_number]['numeric'][$c])
						$this->datasets[$plot_number]['data'][$r][$c] = 0;
					else
						$this->datasets[$plot_number]['data'][$r][$c] = '';
					if (!$this->nulls_defaulted)
						{
						$this->_warning(JText::_('COM_PLOTALOT_WARNING_NULLS_DEFAULTED'));
						$this->nulls_defaulted = true;
						}
					}
		}

// if trace is on, save all the data to the trace file

	if ($this->trace != 0)
		{
		if ($skip_factor > 1)
			$this->_trace("Plot $plot_number returned $num_rows_raw rows, which was reduced to $num_rows_filtered rows by the skip factor of $skip_factor");
		else
			$this->_trace("Plot $plot_number processing $num_rows_filtered rows");
		$str = "There are $num_columns columns: ";
		for ($c=0; $c < $num_columns; $c++)
			$str .= $this->datasets[$plot_number]['column_names'][$c]." [".$this->datasets[$plot_number]['column_types'][$c]."], " ;
		$str = trim($str," ,");
		$this->_trace($str);
		if ($num_rows_filtered == 0)
			return true;
		$trace_columns = $num_columns;
		if ($trace_columns > 8)
			{
			$trace_columns = 8;
			$this->_trace("We only show 8 here in the trace");
			}
		$str = '';
		for ($i=0; $i < $trace_columns; $i++)
			$str .= $this->datasets[$plot_number]['column_names'][$i]."\t";
		$str = trim($str,"\t");
		$this->_trace($str);
	    for ($r=0; $r < $num_rows_filtered; $r++)
	    	{
			$str = '';
			for ($col=0; $col < $trace_columns; $col++)
				$str .= $this->datasets[$plot_number]['data'][$r][$col]."\t";
			$str = trim($str,"\t");
			$this->_trace($str);
			}
		$this->_trace("");
		}
	return true;
}

//-------------------------------------------------------------------------------
// Get a plot legend, defaulting if empty
//
function _getLegend($plot_number)
{
	if (!empty($this->datasets[$plot_number]['legend']))
		return $this->datasets[$plot_number]['legend'];
	else
		return 'Plot '.($plot_number + 1);
}

//-------------------------------------------------------------------------------
// Get the column names and types for a result set
// builds:
// $this->datasets[$plot_number]['column_names'][]
// $this->datasets[plot_number]['column_types'][]
// $this->datasets[plot_number]['numeric'][]
//
function _getColumnInfo($plot_number, $result_set)
{	
// get the name of each column and initially make it numeric

	foreach ($result_set[0] as $column_name => $column_value)
		{
		$this->datasets[$plot_number]['column_names'][] = $this->_escape_string($column_name);
		$this->datasets[$plot_number]['column_types'][] = 'numeric';
		$this->datasets[$plot_number]['numeric'][] = true; 
		}

// now check every value of every row

	foreach ($result_set as $row)
		{
		$column_index = 0;
		foreach ($row as $column_name => $column_value)		// for each column ..										
			{
			if (empty($column_value))						// (6.13) empty or null does not make a column non-numeric
				{
				$column_index ++;							// (6.14)
				continue;
				}
			if (!is_numeric($column_value))										// .. check it again
				{
				$this->datasets[$plot_number]['column_types'][$column_index] = 'string';
				$this->datasets[$plot_number]['numeric'][$column_index] = false;
				}
			$column_index ++;
			}
		}
}

//-------------------------------------------------------------------------------
// Get the min and max values for a dataset
// builds:
// $this->datasets[plot_number]['min'][column_number]
// $this->datasets[plot_number]['max'][column_number]
//
function _getMinMax($plot_number, $column_number)
{	
	if ($this->datasets[$plot_number]['num_columns'] <= $column_number)
		{
		$this->datasets[$plot_number]['min'][$column_number] = 0;
		$this->datasets[$plot_number]['max'][$column_number] = 0;
		$this->_trace("Plot $plot_number, Column $column_number does not exist, setting min/max to 0");
		return;
		}
		
	$num_rows = $this->datasets[$plot_number]['num_rows'];
		
	$min = $this->datasets[$plot_number]['data'][0][$column_number];
    if ( ($min == null) || ($min == 'min') )        // 5.14 (can happen if fix nulls is off!)
        $min = 0;
	$max = $min;
	
    for ($i=0; $i < $num_rows; $i++)
    	{
        $num = $this->datasets[$plot_number]['data'][$i][$column_number];
            if ( ($num == null) || ($num == 'min') )        // 5.14 (can happen if fix nulls is off!)
                $num = 0;
    	if ($num < $min)
    		$min = $num;
    	if ($num > $max)
    		$max = $num;
		}
	
    $this->datasets[$plot_number]['min'][$column_number] = $min;
    $this->datasets[$plot_number]['max'][$column_number] = $max;
	$this->_trace("Plot $plot_number, Column $column_number: min: $min, max: $max");
}

//-------------------------------------------------------------------------------
// Get all the data we will need,
// resolve titles and calculate all the data ranges
//
function _getAllData()
{
	if (!$this->connectChartDatabase())
		{
		$this->_error($this->error);
		return false;
		}
        
    $this->first_dataset = 0;                               // the key of the first $this->datasets[] array

// for tables and single items, just get the data for plot 1 and exit

	if (($this->chart_data->chart_type == CHART_TYPE_SINGLE_ITEM) 
	|| ($this->chart_data->chart_type == CHART_TYPE_PL_TABLE)
	|| ($this->chart_data->chart_type == CHART_TYPE_PL_TABLE_CSS)
	|| ($this->chart_data->chart_type == CHART_TYPE_GV_TABLE))
		{
		if (!$this->_getDataSet(0))
			return false;
		if (empty($this->datasets[0]['num_rows']))			// we can't allow no rows because we don't get the column names
			{
			$this->_error(JText::_('COM_PLOTALOT_ERROR_NO_ROWS'));
			return false;
			}
		return true;
		}
		
// attempt to retrieve a dataset for all defined plots

	$enabled_plots = 0;
	
	for ($p = 0; $p < $this->chart_data->num_plots; $p++)
		{
		if (empty($this->chart_data->plot_array[$p]['enable']))		// plot undefined
			continue;

		if (!$this->chart_data->plot_array[$p]['enable'])			// plot is disabled by user
			continue;
			
		$enabled_plots ++;
		if (!$this->_getDataSet($p))								// if db access fails
			return false;											// give up
			
		if (!empty($this->datasets[$p]['num_rows']))				// do we have rows from this plot?
			{
			$this->active_plots ++;
			$this->total_rows += $this->datasets[$p]['num_rows'];
			}
		else
			{
			unset($this->datasets[$p]);								// commented out at version 5.05 to fix incorrect "No plots enabled" error
			$enabled_plots --;                                      // reinstated at 5.08 because a plot with no data caused a "This chart requires x columns" error
			}                                                       // empty plots must be removed from $this->datasets
		}
        
// determine the key of the first $this->datasets[] array      

    reset($this->datasets);
    $this->first_dataset = key($this->datasets);

	if ($this->total_rows == 0)										// if no rows at all..
		{
		$this->_error(JText::_('COM_PLOTALOT_ERROR_NO_ROWS'));
		return false;
		}

	foreach ($this->datasets as $dataset)
		if ($dataset['num_columns'] < 2)								// we always need at least 2 columns
			return true;											// the chart specific functions will require at least 2 columns
	
	$this->_trace("Active plots: ".$this->active_plots.", Total Rows: ".$this->total_rows."\n");
		
// get data ranges for all the datasets
// we always use column 0 as the X axis, and column 1 as the Y axis
// for scatter graphs only, we use column 3 of plot 0 as the z axis

	foreach ($this->datasets as $p => $dataset)
		{
		$this->_getMinMax($p, $this->x_column);		// get min/max for x axis
		$this->_getMinMax($p, $this->y_column);		// get min/max for y axis
		if ($this->chart_data->chart_type == CHART_TYPE_SCATTER)
			$this->_getMinMax($p, 2);	// get min/max for column 2 (scatter graph z axis)
		}

// get the overall data ranges for the entire chart
// initialise the min/maxes to the first value in a non-empty dataset

	foreach ($this->datasets as $p => $dataset)
		{
		if ($dataset['num_rows'] == 0)
			continue;
		$this->chart_x_min = $dataset['min'][$this->x_column];
		$this->chart_x_max = $dataset['max'][$this->x_column];
		$this->chart_y_min = $dataset['min'][$this->y_column];
		$this->chart_y_max = $dataset['max'][$this->y_column];
		}

	foreach ($this->datasets as $p => $dataset)
		{
		if ($dataset['num_rows'] == 0)
			continue;
		if ($dataset['min'][$this->x_column] < $this->chart_x_min)
			$this->chart_x_min = $dataset['min'][$this->x_column];
		if ($dataset['max'][$this->x_column] > $this->chart_x_max)
			$this->chart_x_max = $dataset['max'][$this->x_column];
		if ($dataset['min'][$this->y_column] < $this->chart_y_min)
			$this->chart_y_min = $dataset['min'][$this->y_column];
		if ($dataset['max'][$this->y_column] > $this->chart_y_max)
			$this->chart_y_max = $dataset['max'][$this->y_column];
		}
			
// For stacked charts, the Y range should be the total of all the Y ranges
// so we must find the highest TOTAL Y value

	if (($this->chart_data->chart_type == CHART_TYPE_BAR_H_STACK)
	||  ($this->chart_data->chart_type == CHART_TYPE_BAR_V_STACK)
	||  ($this->chart_data->chart_type == CHART_TYPE_COMBO_STACK))
		{
		$x_value = array();
		foreach ($this->datasets as $p => $dataset)
			{
			if ($dataset['num_rows'] == 0)
				continue;
			foreach ($dataset['data'] as $row => $row_data)
				{
				$x = $row_data[0];			// the X value is a string, the name of the bar
				$y = (double) $row_data[1];	// the Y value is a number, the height of the bar
				if (!isset($x_value[$x]))	// we build an array of all the bars
					$x_value[$x] = 0;		// .. initialise a new bar
				$x_value[$x] += $y;			// .. add the new Y value for this bar
				}
			}
		foreach ($x_value as $k => $v)
			$this->_trace(" Total x_value[$k] = $v");
		$this->chart_y_max = max($x_value);
		}

	$this->_trace("Actual data ranges: X: [".$this->chart_x_min."] - [".$this->chart_x_max."], Y: [".$this->chart_y_min."] - [".$this->chart_y_max."]\n");

// resolve the chart and axis titles

	$this->chart_title = $this->_resolveQuery($this->chart_data->chart_title);
	$this->chart_title = $this->_escape_string($this->chart_title);
	$this->_trace("  resolved title: ".$this->chart_title);
	$this->x_title = $this->_resolveQuery($this->chart_data->x_title);
	$this->x_title = $this->_escape_string($this->x_title);
	$this->_trace("  resolved x_title: ".$this->x_title);
	$this->y_title = $this->_resolveQuery($this->chart_data->y_title);
	$this->y_title = $this->_escape_string($this->y_title);
	$this->_trace("  resolved y_title: ".$this->y_title);
	
// resolve the specified axis start and end values
// if they resolve to valid numbers outside the calculated overall chart ranges,
// they will be used instead of the calculated ranges

	if (isset($this->chart_data->x_start) && ($this->chart_data->x_start !== ''))
		{
		$x_start = $this->_resolveQuery($this->chart_data->x_start);
		if (!is_numeric($x_start))
			$this->_warning (JText::sprintf('COM_PLOTALOT_WARNING_X_START_NOT_NUMERIC',$x_start));
		else
			if ($x_start > $this->chart_x_min) 
				$this->_warning (JText::sprintf('COM_PLOTALOT_WARNING_X_START_GREATER',$x_start)." [".$this->chart_x_min."]");
			else
				$this->chart_x_min = $x_start;	// ok, we can use it
		}

	if (isset($this->chart_data->x_end) && ($this->chart_data->x_end !== ''))
		{
		$x_end = $this->_resolveQuery($this->chart_data->x_end);
		if (!is_numeric($x_end))
			$this->_warning (JText::sprintf('COM_PLOTALOT_WARNING_X_END_NOT_NUMERIC',$x_end));
		else
			if ($x_end < $this->chart_x_max) 
				$this->_warning (JText::sprintf('COM_PLOTALOT_WARNING_X_END_LESS',$x_end)." [".$this->chart_x_max."]");
			else
				$this->chart_x_max = $x_end;	// ok, we can use it
		}
		
	if (isset($this->chart_data->y_start) && ($this->chart_data->y_start !== ''))
		{
		$y_start = $this->_resolveQuery($this->chart_data->y_start);
		if (!is_numeric($y_start))
			$this->_warning (JText::sprintf('COM_PLOTALOT_WARNING_Y_START_NOT_NUMERIC',$y_start));
		else
			if ($y_start > $this->chart_y_min) 
				$this->_warning (JText::sprintf('COM_PLOTALOT_WARNING_Y_START_GREATER',$y_start)." [".$this->chart_y_min."]");
			else
				$this->chart_y_min = $y_start;	// ok, we can use it
		}

	if (isset($this->chart_data->y_end) && ($this->chart_data->y_end !== ''))
		{
		$y_end = $this->_resolveQuery($this->chart_data->y_end);
		if (!is_numeric($y_end))
			$this->_warning (JText::sprintf('COM_PLOTALOT_WARNING_Y_END_NOT_NUMERIC',$y_end));
		else
			if ($y_end < $this->chart_y_max) 
				$this->_warning (JText::sprintf('COM_PLOTALOT_WARNING_Y_END_LESS',$y_end)." [".$this->chart_y_max."]");
			else
				$this->chart_y_max = $y_end;	// ok, we can use it
		}

	$this->_trace("\nUsing data ranges: X: [".$this->chart_x_min."] - [".$this->chart_x_max."], Y: [".$this->chart_y_min."] - [".$this->chart_y_max."]\n");
		
// Resolve extra_parms

	$this->chart_data->extra_parms = $this->_resolveQuery($this->chart_data->extra_parms);

	return true;
}

//-------------------------------------------------------------------------------
// Rotate colours for pie charts
//
function _nextColour($colour, $style)
{
	switch ($style)
		{
		case PIE_LIGHT_GRADIENT:
			$dec = hexdec($colour);
			$dec += 0x1616;
			$str = dechex($dec);
			return str_pad($str, 6, '0');
			
		case PIE_DARK_GRADIENT:
			$dec = hexdec($colour);
			$dec -= 0x1C0101;
			$str = str_pad(dechex($dec), 6, '0');
			$str = substr($str, -6);
			return $str;
			
		case PIE_MULTI_COLOUR:
			$r = substr($colour, 0, 2);
			$g = substr($colour, 2, 2);
			$b = substr($colour, 4, 2);
			$r = (hexdec($r) + 0x06)%0xFF; // 6F's work well
			$g = (hexdec($g) + 0x3F)%0xFF;
			$b = (hexdec($b) + 0x8F)%0xFF;;
			return sprintf("%02X%02X%02X",$r,$g,$b) ;
		}
}

//-------------------------------------------------------------------------------
// construct a nice message about the datatype of a column
//
function _datatype_message($plot_number, $column_number)
{
	$column_type = $this->datasets[$plot_number]['column_types'][$column_number];
	$column_name = $this->datasets[$plot_number]['column_names'][$column_number];
	if ($this->datasets[$plot_number]['numeric'][$column_number])
		return JText::sprintf('COM_PLOTALOT_COLUMN_X_X_IS_NUMERIC',
			($column_number + 1), $column_name);
	else
		return JText::sprintf('COM_PLOTALOT_COLUMN_X_X_IS_NON_NUMERIC',
			($column_number + 1), $column_name);
}

// -------------------------------------------------------------------------------
// return the datatype for an extra column, 
// return an empty string for an unsupported column type
//
static function extraColumnDataType($column_name)
{
	switch ($column_name)
		{
		case 'annotation'    : return 'string';
		case 'annotationText': return 'string';
		case 'certainty'     : return 'boolean';
		case 'emphasis'      : return 'boolean';
		case 'interval'      : return 'number';
		case 'scope'         : return 'boolean';
		case 'tooltip'       : return 'string';
		case 'style'         : return 'string';
		default              : return '';
		}
}

//-------------------------------------------------------------------------------
// Build a Google Visualization data table - the info_array describes the columns
//
function _make_gvDataTable($data, $column_info_array)
{
// 	$this->_trace("_make_gvDataTable() info_array: ".print_r($column_info_array,true));
// 	$this->_trace("_make_gvDataTable() data: ".print_r($data,true));

// start the DataTable object

	$this->gvDataTable = "\nwindow.plotalot_chart_".$this->chart_data->id."_data = new google.visualization.DataTable();";

// add the column definitions

	foreach ($column_info_array as &$info)
		{
		$column_name = $info['name'];
		switch ($info['type'])
			{
			case GV_DATA_TYPE_FORMAT_X:
				$this->gvDataTable .= "\nwindow.plotalot_chart_".$this->chart_data->id."_data.addColumn('".$this->_gcFormatType($this->chart_data->x_format)."', '$column_name');";
				break;
			case GV_DATA_TYPE_STRING:
				$this->gvDataTable .= "\nwindow.plotalot_chart_".$this->chart_data->id."_data.addColumn('string', '$column_name');";
				break;
			case GV_DATA_TYPE_NUMBER:
				$this->gvDataTable .= "\nwindow.plotalot_chart_".$this->chart_data->id."_data.addColumn('number', '$column_name');";
				break;
			case GV_DATA_TYPE_DATE:
			case GV_DATA_TYPE_DATETIME:
				$this->gvDataTable .= "\nwindow.plotalot_chart_".$this->chart_data->id."_data.addColumn('date', '$column_name');";
				break;
			case GV_DATA_TYPE_EXTRA:
				$info['extra_data_type'] = self::extraColumnDataType($column_name);
				if ($column_name == 'tooltip')		// always enable html tooltips
					$this->gvDataTable .= "\nwindow.plotalot_chart_".$this->chart_data->id."_data.addColumn({'type': 'string', 'role': 'tooltip', 'p': {'html': true}});";
				else
					$this->gvDataTable .= "\nwindow.plotalot_chart_".$this->chart_data->id.
						"_data.addColumn({type:'".$info['extra_data_type']."', role:'".$column_name."'});";
				break;
			}
		}

// add the data rows
// we keep the data separate so that the ajax responder can pick it out easily

	$num_columns = count($column_info_array);
	$this->gvDataTableData = "[";
	$comma1 = '';
	foreach ($data as $row)
		{
		$this->gvDataTableData .= $comma1."[";
		$comma2 = '';
		for ($col=0; $col < $num_columns; $col++)
			{
			if (!isset($row[$col]))
				{
				$this->gvDataTableData .= $comma2.'null';
				$comma2 = ',';
				continue;
				}
			switch ($column_info_array[$col]['type'])
				{
				case GV_DATA_TYPE_STRING:
					if (substr($row[$col],0,3) == '{v:')		// if it's a Cell Object don't quote it
						$column_value = $row[$col];
					else
						$column_value = $this->_quote($row[$col]);
					break;

				case GV_DATA_TYPE_NUMBER:
					$column_value = $row[$col];
					break;

				case GV_DATA_TYPE_DATE:
					$column_value = $this->_gcFormatNumber($row[$col],FORMAT_DATE_YMD_NOTIME);
					break;

				case GV_DATA_TYPE_DATETIME:
					$column_value = $this->_gcFormatNumber($row[$col],FORMAT_CUSTOM_DATE);
					break;

				case GV_DATA_TYPE_FORMAT_X:
					$column_value = $this->_gcFormatNumber($row[$col],$this->chart_data->x_format);
					break;

				case GV_DATA_TYPE_EXTRA:
					switch ($column_info_array[$col]['extra_data_type'])
						{
						case 'string':  $column_value = $this->_quote($row[$col]); break;
						case 'number':  $column_value = $row[$col]; break;
						case 'boolean': $column_value = ($row[$col] == 0) ? 'false' : 'true'; break;
						}
					break;
				}
			$this->gvDataTableData .= $comma2.$column_value;
			$comma2 = ',';
			}
		$this->gvDataTableData .= "]";
		$comma1 = ',';
		}
	$this->gvDataTableData .= "]";

	$this->gvDataTable .= "\nwindow.plotalot_chart_".$this->chart_data->id."_data.addRows(\n".$this->gvDataTableData.");";

}

//-------------------------------------------------------------------------------
// Validate the dataset requirements for a chart
//   $multiple is true if multiple data sets are allowed
//   $min_cols is the minimum number of columns
//   $col_types is an array of data types required, e.g. ('string','string','number','number')
// There can be more than $min_cols columns in $col_types because some columns may be optional  
//
function _check_dataset($multiple, $min_cols, $col_types)
{
	if (($this->active_plots > 1) && (!$multiple))
		{
		$this->_error(JText::_('COM_PLOTALOT_ERROR_ONLY_ONE_PLOT'));
		return false;
		}

// add the extra column types to the supplied array of column types

	foreach ($this->extra_column_array as $extra_column)
		{
		$extra_data_type = self::extraColumnDataType($extra_column);
		if ($extra_data_type == 'boolean')		// boolean requires a numeric column
			$extra_data_type = 'number';
		$col_types[] = $extra_data_type;		// add a new array element
		}

// check the column type requirements for each dataset

	foreach ($this->datasets as $p => $dataset)
		{
		if ($dataset['num_columns'] < $min_cols)
			{
			$this->_error(JText::sprintf('COM_PLOTALOT_ERROR_REQUIRES_X_COLUMNS',$min_cols));
			return false;
			}
		
		foreach ($col_types as $column => $col_type)
			{
			if ($column >= $dataset['num_columns'])	// there can be more columns in $col_types than actual columns
				break;
			switch ($col_type)
				{
				case 'either':
					break;
				case 'number':
					if (!$dataset['numeric'][$column])
						{
						$this->_error(JText::sprintf('COM_PLOTALOT_ERROR_COLUMN_X_NUMERIC',$column+1).'<br />'.
						JText::_('COM_PLOTALOT_PLOT').' '.($p + 1).' '.$this->_datatype_message($p, $column));
						return false;
						}
					break;
				case 'string':		// as of version 5.0 we no longer enforce this because a number can always be made into a string
					break;
				}
			}
		}

	return true;
}

//-------------------------------------------------------------------------------
// merge the datasets to a single array and sort if required
// for charts with multiple plots of X, Y we end up with a single dataset of X, Y1, Y2, Y3, etc
// for charts with multiple plots and multiple values (or extra columns) we end up with X, Y1, E1, E2, Y2, E1, E2, Y3, E1, E2, etc.
//
function _mergeDatasets($num_values, $sort = false)
{
	$merged_data = array();
	$y_start = 0;
	foreach ($this->datasets as $d => $dataset)
		{
		$num_rows = $dataset['num_rows'];
		if ($num_rows == 0)						// ignore empty datasets
			continue;
		for ($row = 0; $row < $num_rows; $row++)
			{
			$x_value = $dataset['data'][$row][0];
			if (is_string($x_value))
				$x_string = $x_value;
			else
				$x_string = number_format(($x_value * 100),0,'.','');
			$merged_data[$x_string][0] = $dataset['data'][$row][0];
			$ds_num_values = $num_values;
            if ($dataset['style'] == COMBO_PLOT_TYPE_CANDLESTICKS)
				$ds_num_values = $num_values + 3;
			for ($i=1; $i < $ds_num_values; $i++)
				if (isset($dataset['data'][$row][$i]))
					$merged_data[$x_string][$i + $y_start] = $dataset['data'][$row][$i];
				else
					$merged_data[$x_string][$i + $y_start] = '';
			}
		$y_start += ($ds_num_values - 1);
		}

	if ($sort)
		ksort($merged_data);
	$this->_trace("Merged dataset [$num_values values]: ".print_r($merged_data,true));
	return $merged_data;
}

//-------------------------------------------------------------------------------
// Create the legend option
//
function _legend_options()
{
	switch ($this->chart_data->legend_type)
		{
		case LEGEND_LEFT:   $this->gvOptions .= ",legend:{position:'left'}"; break;
		case LEGEND_RIGHT:  $this->gvOptions .= ",legend:{position:'right'}"; break;
		case LEGEND_TOP:    $this->gvOptions .= ",legend:{position:'top'}"; break;
		case LEGEND_BOTTOM: $this->gvOptions .= ",legend:{position:'bottom'}"; break;
		case LEGEND_NONE:   $this->gvOptions .= ",legend:{position:'none'}"; break;
		case LEGEND_IN:     $this->gvOptions .= ",legend:{position:'in'}"; break;
		case LEGEND_LABELLED: $this->gvOptions .= ",legend:{position:'labeled'}"; break;
		}
}

//-------------------------------------------------------------------------------
// Create the Axis titles, grid lines, formats, and min/max values
// - the grid parameters can suppress the horizontal and/or vertical grid lines
//
function _axis_options($x_grid=true, $y_grid=true)
{
	$x_grid_colour = "color:'transparent',";		// hide the grid but we still want to specify the count
	$y_grid_colour = "color:'transparent',";		// .. because it determines the number of labels

	if (($this->chart_data->show_grid) && ($x_grid))
		$x_grid_colour = "";
		
	if (($this->chart_data->show_grid) && ($y_grid))
		$y_grid_colour = "";
		
	if (($this->chart_data->x_labels == -1) || ($this->chart_data->x_labels == ''))			// auto, which means 5
		$x_grid_count = "count:5";
	else
		$x_grid_count = "count:".$this->chart_data->x_labels;
	
	if (($this->chart_data->y_labels == -1) || ($this->chart_data->y_labels == ''))			// auto, which means 5
		$y_grid_count = "count:5";
	else
		$y_grid_count = "count:".$this->chart_data->y_labels;
	
	$x_gridlines = ",gridlines:{".$x_grid_colour.$x_grid_count."}";
	$y_gridlines = ",gridlines:{".$y_grid_colour.$y_grid_count."}";

// axis label formats

	if ($this->chart_data->x_format == FORMAT_NONE)
		$x_format = '';
	else
		$x_format = ',format:"'.$this->_gcFormatString($this->chart_data->x_format).'"';
		
	if ($this->chart_data->y_format == FORMAT_NONE)
		$y_format = '';
	else
		$y_format = ',format:"'.$this->_gcFormatString($this->chart_data->y_format).'"';
		
// X axis ranges

	if (($this->chart_data->x_start == '') && ($this->chart_data->x_end == ''))
		$x_range = '';
	else
		{
		$x_min = $this->_gcFormatNumber($this->chart_x_min,$this->chart_data->x_format);
		$x_max = $this->_gcFormatNumber($this->chart_x_max,$this->chart_data->x_format);
		$x_range = ",viewWindowMode:'explicit',viewWindow:{min:$x_min,max:$x_max}";
		}

// Y axis ranges

	if (($this->chart_data->y_start == '') && ($this->chart_data->y_end == ''))
		$y_range = '';
	else
		$y_range = ",viewWindowMode:'explicit',viewWindow:{min:".$this->chart_y_min.",max:".$this->chart_y_max."}";

// finally we are ready to specify the axis objects

	$this->gvOptions .= ",hAxis:{title:'".$this->x_title."'".$x_gridlines.$x_format.$x_range."}";
	$this->gvOptions .= ",vAxis:{title:'".$this->y_title."'".$y_gridlines.$y_format.$y_range."}";
}

//-------------------------------------------------------------------------------
// Line or Area chart
// returns true or false with an error set
//
function _lineOrAreaGraph()
{
	if ($this->chart_data->chart_type == CHART_TYPE_LINE)
		{
		$this->gvClass = 'visualization.LineChart';
		if ($this->chart_data->design_pattern == DESIGN_MATERIAL)
			{										// Material line chart
			$this->gvPackages = 'line';
			$this->gvClass = 'charts.Line';
			$this->gvConvertOptions = 'charts.Line.convertOptions';
			}
		}
	else
		$this->gvClass = 'visualization.AreaChart';

// validate the data types
// multiple data sets, all with first two columns numeric

	if (!$this->_check_dataset(true, 2, array('number','number')))
		return false;

// build the options

	$this->gvOptions .= ",series:[";
	$comma1 = '';
	foreach ($this->datasets as $p => $dataset)
		{
		$this->gvOptions .= $comma1;
		$comma2 = '';
		$this->gvOptions .= "{";
		if (!empty($this->chart_data->plot_array[$p]['colour']))
			{
			$this->gvOptions .= "color:'#".$this->chart_data->plot_array[$p]['colour']."'";
			$comma2 = ',';
			}
		if (!isset($this->chart_data->plot_array[$p]['style']))
			$this->chart_data->plot_array[$p]['style'] = PLOT_STYLE_NORMAL;
		switch ($this->chart_data->plot_array[$p]['style'])
			{
			case LINE_THIN_SOLID:
				$this->gvOptions .= $comma2."lineWidth:'1'";
				break;
			case LINE_THICK_SOLID: 
				$this->gvOptions .= $comma2."lineWidth:'3'";
				break;
			}
		$this->gvOptions .= "}";
		$comma1 = ',';
		}
	$this->gvOptions .= "]";
	$this->gvOptions .= ",interpolateNulls:true";		// always interpolate missing data points
	$this->_legend_options();
	$this->_axis_options();

// Build the column_info_array that controls building of the Data Table Object
// - the X axis is either number, date, or datetime - it is always continuous
// - we don't support discreet X axis because gridlines don't work for discreet axes

	$column_info_array = array();
	$column_info_array[0]['type'] = GV_DATA_TYPE_FORMAT_X;
	$column_info_array[0]['name'] = 'X';
	$index = 1;		
	foreach ($this->datasets as $d => $dataset)
		{
		$legend = $this->_getLegend($d);
		$column_info_array[$index]['type'] = GV_DATA_TYPE_NUMBER;
		$column_info_array[$index++]['name'] = $legend;
		foreach ($this->extra_column_array as $extra_column)
			{
			$column_info_array[$index]['type'] = GV_DATA_TYPE_EXTRA;
			$column_info_array[$index++]['name'] = $extra_column;
			}
		}

// merge and sort the datasets to a single array, and create the Google data table

	$num_values = 2 + count($this->extra_column_array);
	$merged_data = $this->_mergeDatasets($num_values, true);	
	$this->_make_gvDataTable($merged_data, $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// Scatter graph specific
// returns true or false with an error set
//
function _scatterGraph()
{
	$this->gvClass = 'visualization.ScatterChart';

// multiple data sets, all with two numeric columns

	if (!$this->_check_dataset(true, 2, array('number','number')))
		return false;
		
// build the options

	$this->gvOptions .= ",series:[";
	$comma1 = '';
	foreach ($this->datasets as $p => $dataset)
		{
		$this->gvOptions .= $comma1;
		$this->gvOptions .= "{";
		if (!empty($this->chart_data->plot_array[$p]['colour']))
			$this->gvOptions .= "color:'#".$this->chart_data->plot_array[$p]['colour']."'";
		$this->gvOptions .= "}";
		$comma1 = ',';
		}
	$this->gvOptions .= "]";
	$this->gvOptions .= ",interpolateNulls:true";		// always interpolate missing data points
	$this->_legend_options();
	$this->_axis_options();

// Build the column_info_array that controls building of the Data Table Object
// scatter graphs have multiple Y values for the same X value
// the X axis is either number, date, or datetime - it is always continuous
// we don't support the discreet X axis because gridlines don't work for discreet axes

	$column_info_array = array();
	$column_info_array[0]['type'] = GV_DATA_TYPE_FORMAT_X;
	$column_info_array[0]['name'] = 'X';
	$index = 1;
	foreach ($this->datasets as $d => $dataset)
		{
		$legend = $this->_getLegend($d);
		$column_info_array[$index]['type'] = GV_DATA_TYPE_NUMBER;
		$column_info_array[$index++]['name'] = $legend;
		foreach ($this->extra_column_array as $extra_column)
			{
			$column_info_array[$index]['type'] = GV_DATA_TYPE_EXTRA;
			$column_info_array[$index++]['name'] = $extra_column;
			}
		}

// merge and sort the datasets to a single array, and create the Google data table

	$num_values = 2 + count($this->extra_column_array);
	$merged_data = $this->_mergeDatasets($num_values);
	$this->_make_gvDataTable($merged_data, $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// Pie chart specific
// returns true or false with an error set
//
function _pieChart()
{
	$this->gvClass = 'visualization.PieChart';

// one dataset with at least two columns, first column non-numeric

	if (!$this->_check_dataset(false, 2, array('string','number')))
		return false;

// set options depending on chart type

	switch ($this->chart_data->chart_type)
		{
		case CHART_TYPE_PIE_2D:
			$this->gvOptions .= ",is3D:false";
			$add_values_to_labels = false;
			break;

		case CHART_TYPE_PIE_3D:
			$this->gvOptions .= ",is3D:true";
			$add_values_to_labels = false;
			break;

		case CHART_TYPE_PIE_2D_V:
			$this->gvOptions .= ",is3D:false";
			$add_values_to_labels = true;
			$this->gvOptions .= ",tooltip:{text:'percentage'}";
			break;

		case CHART_TYPE_PIE_3D_V:
			$this->gvOptions .= ",is3D:true";
			$add_values_to_labels = true;
			$this->gvOptions .= ",tooltip:{text:'percentage'}";
			break;
		}

	switch ($this->chart_data->chart_option)
		{
		case PIE_TEXT_NONE:		$this->gvOptions .= ",pieSliceText:'none'"; break;
		case PIE_TEXT_PERCENT:	$this->gvOptions .= ",pieSliceText:'percentage'"; break;
		case PIE_TEXT_VALUE:	$this->gvOptions .= ",pieSliceText:'value'"; break;
		case PIE_TEXT_LABEL:	$this->gvOptions .= ",pieSliceText:'label'"; break;
		}

	$this->_legend_options();

	if (!isset($this->chart_data->plot_array[0]['style']))
		$style = PLOT_STYLE_NORMAL;
	else
		$style = $this->checkPlotStyle($this->chart_data->chart_type, $this->chart_data->plot_array[0]['style']);
		
	if ($style != PLOT_STYLE_NORMAL)
		{
		$this->gvOptions .= ",'colors':[";
		$num_rows = $this->datasets[0]['num_rows'];
		$comma = '';
		$colour = $this->chart_data->plot_array[0]['colour'];
		if (empty($colour))
			$colour = '000000';
		for ($r = 0; $r < $num_rows; $r++)
			{
			$this->gvOptions .= $comma."'#".$colour."'";
			$colour = $this->_nextColour($colour, $style);
			$comma = ',';
			}
		$this->gvOptions .= "]";
		}

// Build the column_info_array that controls building of the Data Table Object
// Pie charts only have one dataset
// X are the labels
// Y are the values

	$column_info_array = array();
	$column_info_array[0]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[0]['name'] = 'Labels';
	$column_info_array[1]['type'] = GV_DATA_TYPE_NUMBER;
	$column_info_array[1]['name'] = 'Values';
	$index = 2;
	foreach ($this->extra_column_array as $extra_column)
		{
		$column_info_array[$index]['type'] = GV_DATA_TYPE_EXTRA;
		$column_info_array[$index++]['name'] = $extra_column;
		}

// we need to modify the X values before passing the data to _make_gvDataTable()

	$data = array();
	$row_number = 0;
	foreach ($this->datasets[0]['data'] as $row)
		{
		$x_value = $row[$this->x_column];		// labels
		$y_value = $row[$this->y_column];		// values
		if ($add_values_to_labels)
			$x_value .= ' ('.$this->_formatValue($y_value,$this->chart_data->y_format).')';		// the label can include our formatted Y value
		$data[$row_number][0] = $x_value;
		$data[$row_number][1] = $y_value;
		$extra_col = 2;
		foreach ($this->extra_column_array as $extra_column)
			{
			if (!isset($row[$extra_col]))
				break;		// extra column defined but dataset does not have it
			$extra_value = $row[$extra_col];
			$data[$row_number][$extra_col] = $extra_value;
			$extra_col ++;
			}
		$row_number ++;
		}

	$this->_make_gvDataTable($data, $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// Bar chart specific
// returns true or false with an error set
//
function _barChart()
{
	switch ($this->chart_data->chart_type)
		{
		case CHART_TYPE_BAR_H_STACK:
			$this->gvClass = "visualization.BarChart";
			break;
		case CHART_TYPE_BAR_H_GROUP:
			$this->gvClass = "visualization.BarChart";
			break;
		case CHART_TYPE_BAR_V_STACK:
			$this->gvClass = "visualization.ColumnChart";
			break;
		case CHART_TYPE_BAR_V_GROUP:
			$this->gvClass = "visualization.ColumnChart";
			break;
		}

	if ($this->chart_data->design_pattern == DESIGN_MATERIAL)
		{
		$this->gvPackages = 'bar';
		$this->gvClass = 'charts.Bar';		// Material bar charts use the same class, with or without the option "bars:'horizontal'"
		$this->gvConvertOptions = 'charts.Bar.convertOptions';
		}

// multiple datasets, all with first column non-numeric, second column numeric

	if (!$this->_check_dataset(true, 2, array('string','number')))
		return false;

// Chart type options
// bars:'horizontal' is required for Material bar charts and ignored for Classic bar charts

	switch ($this->chart_data->chart_type)
		{
		case CHART_TYPE_BAR_H_STACK:
			$this->gvOptions .= ",bars:'horizontal',isStacked:true";
			$horizontal = true;
			break;
		case CHART_TYPE_BAR_H_GROUP:
			$this->gvOptions .= ",bars:'horizontal'";
			$horizontal = true;
			break;
		case CHART_TYPE_BAR_V_STACK:
			$this->gvOptions .= ",isStacked:true";
			$horizontal = false;
			break;
		case CHART_TYPE_BAR_V_GROUP:
			$horizontal = false;
			break;
		}

// Bar colours

	$this->gvOptions .= ",series:[";
	$comma = '';
	foreach ($this->datasets as $p => $dataset)
		{
		$this->gvOptions .= $comma;
		$this->gvOptions .= "{";
		if (!empty($this->chart_data->plot_array[$p]['colour']))
			$this->gvOptions .= "color:'#".$this->chart_data->plot_array[$p]['colour']."'";
		$this->gvOptions .= "}";
		$comma = ',';
		}
	$this->gvOptions .= "]";
	
// Legend type

	$this->_legend_options();

// Axis titles and grid lines
// we only use gridlines on the Y axis

	if ($this->chart_data->show_grid)
		$y_grid_colour = "";
	else
		$y_grid_colour = "color:'transparent',";		// .. because it determines the number of labels
		
	if (($this->chart_data->y_labels == -1) || ($this->chart_data->y_labels == ''))			// auto, which means 5
		$y_grid_count = "count:5";
	else
		$y_grid_count = "count:".$this->chart_data->y_labels;
	
	$y_gridlines = ",gridlines:{".$y_grid_colour.$y_grid_count."}";
	$x_gridlines = ",gridlines:{color:'transparent'}";

// axis label formats

	if ($this->chart_data->y_format == FORMAT_NONE)
		$y_format = '';
	else
		$y_format = ",format:'".$this->_gcFormatString($this->chart_data->y_format)."'";
		
// X axis ranges

	if (($this->chart_data->x_start == '') && ($this->chart_data->x_end == ''))
		$x_range = '';
	else
		{
		$x_min = $this->_gcFormatNumber($this->chart_x_min,$this->chart_data->x_format);
		$x_max = $this->_gcFormatNumber($this->chart_x_max,$this->chart_data->x_format);
		$x_range = ",viewWindowMode:'explicit',viewWindow:{min:$x_min,max:$x_max}";
		}

// Y axis ranges

	if (($this->chart_data->y_start == '') && ($this->chart_data->y_end == ''))
		$y_range = '';
	else
		$y_range = ",viewWindowMode:'explicit',viewWindow:{min:".$this->chart_y_min.",max:".$this->chart_y_max."}";

// finally we are ready to specify the axis objects

	if ($horizontal)
		{
		$this->gvOptions .= ",hAxis:{title:'".$this->y_title."'".$y_gridlines.$y_format.$y_range."}";
		$this->gvOptions .= ",vAxis:{title:'".$this->x_title."'".$x_gridlines."}";
		}
	else
		{
		$this->gvOptions .= ",hAxis:{title:'".$this->x_title."'".$x_gridlines."}";
		$this->gvOptions .= ",vAxis:{title:'".$this->y_title."'".$y_gridlines.$y_format.$y_range."}";
		}

// Build the column_info_array that controls building of the Data Table Object
// - the X axis is always a string

	$column_info_array = array();
	$column_info_array[0]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[0]['name'] = 'X';
	$index = 1;		
	foreach ($this->datasets as $d => $dataset)
		{
		$legend = $this->_getLegend($d);
		$column_info_array[$index]['type'] = GV_DATA_TYPE_NUMBER;
		$column_info_array[$index++]['name'] = $legend;
		foreach ($this->extra_column_array as $extra_column)
			{
			$column_info_array[$index]['type'] = GV_DATA_TYPE_EXTRA;
			$column_info_array[$index++]['name'] = $extra_column;
			}
		}

// merge and sort the datasets to a single array, and create the Google data table

	$num_values = 2 + count($this->extra_column_array);
	$merged_data = $this->_mergeDatasets($num_values, $this->chart_data->chart_option);
	$this->_make_gvDataTable($merged_data, $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// Gauge specific
// returns true or false with an error set
//
function _gaugeChart()
{
	$this->gvClass = 'visualization.Gauge';
	$this->gvPackages = 'gauge';

// one dataset with two columns

	if (!$this->_check_dataset(false, 2, array('string','number')))
		return false;
		
// Options		

	if ($this->chart_data->y_start == '')
		$this->chart_data->y_start = 0;
	$this->gvOptions .= ",min:".$this->_resolveQuery($this->chart_data->y_start);
	if ($this->chart_data->y_end == '')
		$this->chart_data->y_end = 100;
	$this->gvOptions .= ",max:".$this->_resolveQuery($this->chart_data->y_end);

// Build the column_info_array that controls building of the Data Table Object
// Gauge charts only have one dataset

	$column_info_array = array();
	$column_info_array[0]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[0]['name'] = 'Labels';
	$column_info_array[1]['type'] = GV_DATA_TYPE_NUMBER;
	$column_info_array[1]['name'] = 'Values';

	$this->_make_gvDataTable($this->datasets[0]['data'], $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// Timeline specific
// returns true or false with an error set
//
function _timelineChart()
{
	$this->gvClass = 'visualization.Timeline';
	$this->gvPackages = 'timeline';

// one dataset with four columns: string, string, date, date
// can only have tooltip as an extra column

	if ( ($this->extra_column_count > 1) 
	|| ( ($this->extra_column_count == 1) && ($this->extra_column_array[0] != 'tooltip') ) )
		{
		$this->_error(JText::sprintf('COM_PLOTALOT_ONLY_X_EXTRA_COLUMNS','tooltip'));
		return false;
		}

	if (!$this->_check_dataset(false, 4, array('string','string','number','number')))
		return false;

	$this->chart_data->x_format = FORMAT_DATE_DMY;	// dates must be formatted this way

// Build the column_info_array that controls building of the Data Table Object
// Timeline charts only have one dataset
// if we have a tooltip column it must be the third column in the gvDataTable

	$column_info_array = array();
	$index = 0;
	$column_info_array[$index]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[$index]['name'] = 'Row_labels';
	$column_info_array[++$index]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[$index]['name'] = 'Bar_labels';
	if ($this->extra_column_count > 0)
		{
		$column_info_array[++$index]['type'] = GV_DATA_TYPE_EXTRA;
		$column_info_array[$index]['name'] = 'tooltip';
		}
	$column_info_array[++$index]['type'] = GV_DATA_TYPE_FORMAT_X;	// will format as FORMAT_DATE_DMY
	$column_info_array[$index]['name'] = 'Start';
	$column_info_array[++$index]['type'] = GV_DATA_TYPE_FORMAT_X;	// will format as FORMAT_DATE_DMY
	$column_info_array[$index]['name'] = 'End';

// if we have a tooltip column it must be the third column in the gvDataTable

	$data = array();
	$row_number = 0;
	foreach ($this->datasets[0]['data'] as $row)
		{
		if ($row[2] > $row[3])
			{
			$this->_error(JText::sprintf('COM_PLOTALOT_ERROR_START_END',$row[2],$row[3],$row_number));
			return false;
			}
		$index = 0;
		$data[$row_number][$index] = $row[0];			// Row labels
		$data[$row_number][++$index] = $row[1];			// Bar labels
		if ($this->extra_column_count > 0)
			$data[$row_number][++$index] = $row[4];		// Tooltip, if we have one
		$data[$row_number][++$index] = $row[2];			// Start date
		$data[$row_number][++$index] = $row[3];			// End date
		$row_number ++;
		}

	$this->_make_gvDataTable($data, $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// Bubble specific
// returns true or false with an error set
//
function _bubbleChart()
{
	$this->gvClass = 'visualization.BubbleChart';

// one dataset with five columns: string, number, number, string or number, number

	if (!$this->_check_dataset(false, 5, array('string','number','number','either','number')))
		return false;

// if column 3 is a string, this is a "series" bubble chart
// if column 3 is numeric, this is a "gradient-colour" bubble chart

	if ($this->datasets[0]['numeric'][3])
		{
		$series = false;
		$this->_trace("Gradient-colour BubbleChart");
		}
	else
		{
		$series = true;
		$this->_trace("Series BubbleChart");
		}

// Build the options
// if this is a "series" bubble chart, set the normal series legend
// if this is a "gradient-colour" bubble chart, set the colour axis legend

	if ($series)
		$this->_legend_options();
	else
		{
		switch ($this->chart_data->legend_type)
			{
			case LEGEND_IN:     $this->gvOptions .= ",colorAxis:{legend:{position:'in'}}"; break;
			case LEGEND_TOP:    $this->gvOptions .= ",colorAxis:{legend:{position:'top'}}"; break;
			case LEGEND_BOTTOM: $this->gvOptions .= ",colorAxis:{legend:{position:'bottom'}}"; break;
			case LEGEND_NONE:   $this->gvOptions .= ",colorAxis:{legend:{position:'none'}}"; break;
			}
		}
	$this->_axis_options();

// Build the column_info_array that controls building of the Data Table Object
// Bubble charts only have one dataset

	$column_info_array = array();
	$index = 0;
	$column_info_array[$index]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[$index]['name'] = 'ID';											// ID
	$column_info_array[++$index]['type'] = GV_DATA_TYPE_NUMBER;
	$column_info_array[$index]['name'] = $this->datasets[0]['column_names'][1];			// X
	$column_info_array[++$index]['type'] = GV_DATA_TYPE_NUMBER;
	$column_info_array[$index]['name'] = $this->datasets[0]['column_names'][2];			// Y
	if ($series)
		{
		$column_info_array[++$index]['type'] = GV_DATA_TYPE_STRING;
		$column_info_array[$index]['name'] = $this->datasets[0]['column_names'][3];		// Series
		}
	else
		{
		$column_info_array[++$index]['type'] = GV_DATA_TYPE_NUMBER;
		$column_info_array[$index]['name'] = $this->datasets[0]['column_names'][3];		// Colour
		}
	$column_info_array[++$index]['type'] = GV_DATA_TYPE_NUMBER;
	$column_info_array[$index]['name'] = $this->datasets[0]['column_names'][4];			// Size

	$this->_make_gvDataTable($this->datasets[0]['data'], $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// Combo chart specific
// returns true or false with an error set
//
function _comboChart()
{
	$this->gvClass = 'visualization.ComboChart';

// multiple datasets, all with first column numeric or non-numeric, second column must be numeric
// the first column of each dataset must be consistently numeric or non-numeric

	if (!$this->_check_dataset(true, 2, array('either','number')))
		return false;
	$numeric_X = $this->datasets[$this->first_dataset]['numeric'][0];
	foreach ($this->datasets as $p => $dataset)
        {
		if ($numeric_X != $dataset['numeric'][0])
			{
			$this->_error(JText::_('COM_PLOTALOT_ERROR_CONSISTENT'));
			return false;
			}
        if ($this->chart_data->plot_array[$p]['style'] == COMBO_PLOT_TYPE_CANDLESTICKS)
            {
            $num_columns_required = $this->extra_column_count + 5;
            if ($dataset['num_columns'] < $num_columns_required)
                {
                $plot = $p + 1;
                $this->_error(JText::sprintf('COM_PLOTALOT_PLOT_X_REQUIRES_X_COLUMNS',$plot,$num_columns_required));
                return false;
                }
            }
        }

// options

	if ($this->chart_data->chart_type == CHART_TYPE_COMBO_STACK)
		$this->gvOptions .= ",isStacked:true";
	$this->gvOptions .= ",interpolateNulls:true";		// always interpolate missing data points
	$this->_legend_options();
	if (!$numeric_X)									// for a string X axis
		{
		$this->chart_data->x_start = '';				// .. we need to ignore X axis overrides
		$this->chart_data->x_end = '';
		}
	$this->_axis_options(false,true);

	$this->gvOptions .= ",series:[";
	$comma = '';
	foreach ($this->datasets as $p => $dataset)
		{
		$this->gvOptions .= $comma;
		$this->gvOptions .= "{";
		if (!empty($this->chart_data->plot_array[$p]['colour']))
			$this->gvOptions .= "color:'#".$this->chart_data->plot_array[$p]['colour']."',";
		$this->gvOptions .= "type:'".$this->_gcMarkerType($this->chart_data->plot_array[$p]['style'])."'";
		switch ($this->chart_data->plot_array[$p]['style'])
			{
			case COMBO_PLOT_TYPE_LINE_THIN:
				$this->gvOptions .= ",lineWidth:'1'";
				break;
			case COMBO_PLOT_TYPE_LINE_THICK: 
				$this->gvOptions .= ",lineWidth:'3'";
				break;
			}
        if (substr($dataset['legend'],-1) == '~')           // if last character of name is '~', hide in chart legend 
            $this->gvOptions .= ",visibleInLegend:false";
		$this->gvOptions .= "}";
		$comma = ',';
		}
	$this->gvOptions .= "]";

// Build the column_info_array that controls building of the Data Table Object
// - the X axis is either number, date, or datetime - it is always continuous
// - we don't support discreet X axis because gridlines don't work for discreet axes

	$column_info_array = array();
	$index = 0;
	if ($numeric_X)
		$column_info_array[0]['type'] = GV_DATA_TYPE_FORMAT_X;
	else
		$column_info_array[0]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[0]['name'] = 'X';
		
	foreach ($this->datasets as $d => $dataset)
		{
		$legend = $this->_getLegend($d);
		$column_info_array[++$index]['type'] = GV_DATA_TYPE_NUMBER;
		$column_info_array[$index]['name'] = $legend;
        if ($this->chart_data->plot_array[$d]['style'] == COMBO_PLOT_TYPE_CANDLESTICKS)
            {                       // candlestick plots need 4 columns
    		$column_info_array[++$index]['type'] = GV_DATA_TYPE_NUMBER;
    		$column_info_array[$index]['name'] = $legend.'_2';
    		$column_info_array[++$index]['type'] = GV_DATA_TYPE_NUMBER;
    		$column_info_array[$index]['name'] = $legend.'_3';
    		$column_info_array[++$index]['type'] = GV_DATA_TYPE_NUMBER;
    		$column_info_array[$index]['name'] = $legend.'_4';
            }
		foreach ($this->extra_column_array as $extra_column)
			{
			$column_info_array[++$index]['type'] = GV_DATA_TYPE_EXTRA;
			$column_info_array[$index]['name'] = $extra_column;
			}
		}

// merge and sort the datasets to a single array, and create the Google data table

	$num_values = 2 + count($this->extra_column_array);
	$merged_data = $this->_mergeDatasets($num_values, $this->chart_data->chart_option);
	$this->_make_gvDataTable($merged_data, $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// Candlestick specific
// returns true or false with an error set
//
function _candlestickChart()
{
	$this->gvClass = 'visualization.CandlestickChart';

// multiple datasets with five or more columns
// first column numeric or non-numeric, next four must be numeric
// the first column of each dataset must be consistently numeric or non-numeric
// can only have tooltip as an extra column

	if ( ($this->extra_column_count > 1) 
	|| ( ($this->extra_column_count == 1) && ($this->extra_column_array[0] != 'tooltip') ) )
		{
		$this->_error(JText::sprintf('COM_PLOTALOT_ONLY_X_EXTRA_COLUMNS','tooltip'));
		return false;
		}

	if (!$this->_check_dataset(true, 5, array('either','number','number','number','number')))
		return false;

	$numeric_X = $this->datasets[$this->first_dataset]['numeric'][0];
	foreach ($this->datasets as $p => $dataset)
		if ($numeric_X != $dataset['numeric'][0])
			{
			$this->_error(JText::_('COM_PLOTALOT_ERROR_CONSISTENT'));
			return false;
			}

// options

	$this->gvOptions .= ",interpolateNulls:true";		// always interpolate missing data points
	$this->_legend_options();
	if (!$numeric_X)									// for a string X axis
		{
		$this->chart_data->x_start = '';				// .. we need to ignore X axis overrides
		$this->chart_data->x_end = '';
		}
	$this->_axis_options(false,true);
	$this->gvOptions .= ",series:[";
	$comma = '';
	foreach ($this->datasets as $p => $dataset)
		{
		$this->gvOptions .= $comma;
		$this->gvOptions .= "{";
		if (!empty($this->chart_data->plot_array[$p]['colour']))
			$this->gvOptions .= "color:'#".$this->chart_data->plot_array[$p]['colour']."',";
		$this->gvOptions .= "}";
		$comma = ',';
		}
	$this->gvOptions .= "]";

// Build the column_info_array that controls building of the Data Table Object
// if we have a tooltip column it must follow the maximum value column in the Data Table

	$column_info_array = array();
	$index = 0;
	if ($numeric_X)
		$column_info_array[0]['type'] = GV_DATA_TYPE_FORMAT_X;
	else
		$column_info_array[0]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[0]['name'] = 'X';
		
	foreach ($this->datasets as $d => $dataset)
		{
		$legend = $this->_getLegend($d);
		$column_info_array[++$index]['type'] = GV_DATA_TYPE_NUMBER;
		$column_info_array[$index]['name'] = $legend;				// used as the chart legend
		$column_info_array[++$index]['type'] = GV_DATA_TYPE_NUMBER;
		$column_info_array[$index]['name'] = $legend.'_open';
		$column_info_array[++$index]['type'] = GV_DATA_TYPE_NUMBER;
		$column_info_array[$index]['name'] = $legend.'_close';
		$column_info_array[++$index]['type'] = GV_DATA_TYPE_NUMBER;
		$column_info_array[$index]['name'] = $legend.'_max';
		if ($this->extra_column_count == 1)
			{
			$column_info_array[++$index]['type'] = GV_DATA_TYPE_EXTRA;
			$column_info_array[$index]['name'] = 'tooltip';
			}
		}

// merge and sort the datasets to a single array, and create the Google data table

	$num_values = 5 + count($this->extra_column_array);
	$merged_data = $this->_mergeDatasets($num_values, $this->chart_data->chart_option);
	$this->_make_gvDataTable($merged_data, $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// Organization Chart specific
// returns true or false with an error set
//
function _orgChart()
{
	$this->gvClass = 'visualization.OrgChart';
	$this->gvPackages = 'orgchart';

// one dataset with three string columns

	if (!$this->_check_dataset(false, 2, array('string','string','string')))
		return false;
		
// Build the column_info_array that controls building of the Data Table Object
// Org charts only have one dataset

	$column_info_array = array();
	$column_info_array[0]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[0]['name'] = 'Name';
	$column_info_array[1]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[1]['name'] = 'Manager';

	$num_columns = $this->datasets[0]['num_columns'];
	if ($num_columns > 2)
		{
		$column_info_array[2]['type'] = GV_DATA_TYPE_STRING;
		$column_info_array[2]['name'] = 'ToolTip';
		}

	$this->_make_gvDataTable($this->datasets[0]['data'], $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// Treemap Chart specific
// returns true or false with an error set
//
function _treeMap()
{
	$this->gvClass = 'visualization.TreeMap';
	$this->gvPackages = 'treemap';

// one dataset with three or four columns

	if (!$this->_check_dataset(false, 3, array('string','string','number','number')))
		return false;
		
// Build the column_info_array that controls building of the Data Table Object
// Tree maps only have one dataset

	$column_info_array = array();
	$column_info_array[0]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[0]['name'] = 'Name';
	$column_info_array[1]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[1]['name'] = 'Parent';
	$column_info_array[2]['type'] = GV_DATA_TYPE_NUMBER;
	$column_info_array[2]['name'] = 'Size';
	$num_columns = $this->datasets[0]['num_columns'];
	if ($num_columns > 3)
		{
		$column_info_array[3]['type'] = GV_DATA_TYPE_NUMBER;
		$column_info_array[3]['name'] = 'Colour';
		}

	$this->_make_gvDataTable($this->datasets[0]['data'], $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// GeoChart specific
// returns true or false with an error set
//
function _geoChart()
{
	$this->gvClass = 'visualization.GeoChart';
	$this->gvPackages = 'geochart';
	$this->gvExtra .= ", 'mapsApiKey': '".$this->chart_data->api_key."'";

// Data columns depend on the mode

	switch ($this->chart_data->chart_option)
		{
		case GEO_MODE_REGION:
			if (!$this->_check_dataset(false, 1, array('string','number')))
				return false;
			$this->gvOptions .= ",displayMode: 'regions'"; 
			$required_columns = 2;
			break;

		case GEO_MODE_MARKER_ADDRESS: 
			if (!$this->_check_dataset(false, 1, array('string','number','number')))
				return false;
			$this->gvOptions .= ",displayMode: 'markers'";
			$required_columns = 3;
			break;

		case GEO_MODE_MARKER_LATLONG: 
			if (!$this->_check_dataset(false, 2, array('number','number','number','number')))
				return false;
			$this->gvOptions .= ",displayMode: 'markers'"; 
			$required_columns = 4;
			break;

		case GEO_MODE_TEXT:	          
			if (!$this->_check_dataset(false, 1, array('string','number')))
				return false;
			$this->gvOptions .= ",displayMode: 'text'"; 
			$required_columns = 2;
			break;
		}

// Build the column_info_array that controls building of the Data Table Object
// Geo charts only have one dataset

	$column_info_array = array();
	$index = 0;
	$num_columns = $this->datasets[0]['num_columns'];
	for ($col=0; $col < $required_columns; $col++)
		{
		if ($this->datasets[0]['numeric'][$col])
			$column_info_array[$index]['type'] = GV_DATA_TYPE_NUMBER;
		else
			$column_info_array[$index]['type'] = GV_DATA_TYPE_STRING;
		$column_info_array[$index++]['name'] = $this->datasets[0]['column_names'][$col];
		}

	foreach ($this->extra_column_array as $extra_column)
		{
		$column_info_array[$index]['type'] = GV_DATA_TYPE_EXTRA;
		$column_info_array[$index++]['name'] = $extra_column;
		}

	$this->_make_gvDataTable($this->datasets[0]['data'], $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// Gantt Chart specific
// returns true or false with an error set
//
function _ganttChart()
{
	$this->gvClass = 'visualization.Gantt';
	$this->gvPackages = 'gantt';

// one dataset with 8 columns: string, string, string, date, date, number, number, string

	if (!$this->_check_dataset(false, 8, array('string','string','string','number','number','number','number','string')))
		return false;

// Build the column_info_array that controls building of the Data Table Object
// Gantt charts only have one dataset

	$column_info_array = array();
	$index = 0;
	$column_info_array[$index]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[$index]['name'] = 'Task ID';
	$column_info_array[++$index]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[$index]['name'] = 'Task Name';
	$column_info_array[++$index]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[$index]['name'] = 'Resource';
	$column_info_array[++$index]['type'] = GV_DATA_TYPE_DATE;
	$column_info_array[$index]['name'] = 'Start';
	$column_info_array[++$index]['type'] = GV_DATA_TYPE_DATE;
	$column_info_array[$index]['name'] = 'End';
	$column_info_array[++$index]['type'] = GV_DATA_TYPE_NUMBER;
	$column_info_array[$index]['name'] = 'Duration';
	$column_info_array[++$index]['type'] = GV_DATA_TYPE_NUMBER;
	$column_info_array[$index]['name'] = 'Percent';
	$column_info_array[++$index]['type'] = GV_DATA_TYPE_STRING;
	$column_info_array[$index]['name'] = 'Dependencies';

	$this->_make_gvDataTable($this->datasets[0]['data'], $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// Calendar Chart specific
// returns true or false with an error set
//
function _calendarChart()
{
	$this->gvClass = 'visualization.Calendar';
	$this->gvPackages = 'calendar';

// one dataset with 2 columns: date, number
// An optional third column for customized styling is coming in a future Google Charts release

	if (!$this->_check_dataset(false, 2, array('number','number')))
		return false;

// Build the column_info_array that controls building of the Data Table Object
// Calendar charts only have one dataset

	$column_info_array = array();
	$index = 0;
	$column_info_array[$index]['type'] = GV_DATA_TYPE_DATE;
	$column_info_array[$index]['name'] = 'Date';
	$column_info_array[++$index]['type'] = GV_DATA_TYPE_NUMBER;
	$column_info_array[$index]['name'] = 'Value';

	$data = array();
	$row_number = 0;
	foreach ($this->datasets[0]['data'] as $row)
		{
		$index = 0;
		if (empty($row[0]))								// don't allow null or zero dates - skip the row
			continue;
		$data[$row_number][$index] = $row[0];			// Start date
		$data[$row_number][++$index] = $row[1];			// Value
		$row_number ++;
		}

	$this->_make_gvDataTable($data, $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// Annotation Chart specific
// returns true or false with an error set
//
function _annotationChart()
{
	$this->gvClass = 'visualization.AnnotationChart';
	$this->gvPackages = 'annotationchart';

// multiple datasets each with 4 columns: date, numeric value, title string, text string

	if (!$this->_check_dataset(true, 4, array('number','number','string','string')))
		return false;

// options - Annotation chart has some undocumented options https://groups.google.com/g/google-visualization-api/c/GF2ZTDQESqU
// ,chart:{backgroundColor:'white',chartArea:{backgroundColor:'lightgrey'}} 

	$this->gvOptions .= ',interpolateNulls:true,displayAnnotations:true';
	if (($this->chart_data->back_colour != '') && ($this->chart_data->back_colour != 'FFFFFF'))
		$this->gvOptions .= ",chart:{backgroundColor:'".$this->chart_data->back_colour."',chartArea:{backgroundColor:'".$this->chart_data->back_colour."'}} ";
	if ($this->chart_data->y_start != '')
		$this->gvOptions .= ',min:'.$this->chart_data->y_start;
	if ($this->chart_data->y_end != '')
		$this->gvOptions .= ',max:'.$this->chart_data->y_end;
	$colours = array();
	foreach ($this->datasets as $p => $dataset)
		if (!empty($this->chart_data->plot_array[$p]['colour']))
			$colours[] = "'#".$this->chart_data->plot_array[$p]['colour']."'";
	if (!empty($colours))
		$this->gvOptions .= ",colors:[".implode(',',$colours).']';

// Build the column_info_array that controls building of the Data Table Object

	$column_info_array = array();
	$column_info_array[0]['type'] = GV_DATA_TYPE_DATETIME;
	$column_info_array[0]['name'] = 'Date';
	$index = 1;
	foreach ($this->datasets as $d => $dataset)
		{
		$legend = $this->_getLegend($d);
		$column_info_array[$index]['type'] = GV_DATA_TYPE_NUMBER;
		$column_info_array[$index++]['name'] = $legend;           // used as the chart legend
		$column_info_array[$index]['type'] = GV_DATA_TYPE_STRING;
		$column_info_array[$index++]['name'] = $legend.'_title';
		$column_info_array[$index]['type'] = GV_DATA_TYPE_STRING;
		$column_info_array[$index++]['name'] = $legend.'_text';
		}

// merge and sort the datasets to a single array, and create the Google data table

	$merged_data = $this->_mergeDatasets(4, true);
	$this->_make_gvDataTable($merged_data, $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// Table Chart specific
// returns true or false with an error set
//
function _tableChart()
{
	$this->gvClass = 'visualization.Table';
	$this->gvPackages = 'table';

// Data types can be anything at all
// - there can only be one dataset but we don't need to validate that because the UI can't do anything else

	$this->gvOptions .= ",allowHtml:true";
	if ($this->chart_data->chart_option == 1)
		$this->gvOptions .= ",showRowNumber:true";

// build the cssClassNames

	$css_vars = '';
	$comma = '';
	foreach($this->chart_data->style_array as $key => $value)
		{
		$var_name = '';
		switch ($key)
			{
			case 'gv_head':     $var_name = 'headerRow'; break;
			case 'gv_odd':      $var_name = 'oddTableRow'; break;
			case 'gv_row':      $var_name = 'tableRow'; break;
			case 'gv_selected': $var_name = 'selectedTableRow'; break;
			case 'gv_hover':    $var_name = 'hoverTableRow'; break;
			case 'gv_hcell':    $var_name = 'headerCell'; break;
			case 'gv_tcell':    $var_name = 'tableCell'; break;
			case 'gv_numcell':  $var_name = 'rowNumberCell'; break;
			}
		if (($var_name != '') && ($value != ''))
			{													// if it's not empty
			$css_vars .= $comma.$var_name.":'".$value."'";		// specify it
			$comma = ',';
			}
		}
	if ($css_vars != '')										// if any classes were specified...
		$this->gvOptions .= ",cssClassNames:{".$css_vars.'}';	// add the option
	
// Build the column_info_array that controls building of the Data Table Object
// Table charts only have one dataset

	$column_info_array = array();
	$index = 0;
	$num_columns = $this->datasets[0]['num_columns'];
	for ($col=0; $col < $num_columns; $col++)
		{
		if ($this->datasets[0]['numeric'][$col])
			$column_info_array[$index]['type'] = GV_DATA_TYPE_NUMBER;
		else
			$column_info_array[$index]['type'] = GV_DATA_TYPE_STRING;
		$column_info_array[$index++]['name'] = $this->datasets[0]['column_names'][$col];
		}

	$this->_make_gvDataTable($this->datasets[0]['data'], $column_info_array);
	return true;
}

//-------------------------------------------------------------------------------
// Draw a chart - this is the main public function
// It constructs any type of chart and returns the html
// If the chart cannot be drawn, an empty string is returned, and an error message is in $this->error
// Even if the chart is drawn, there may be warnings in $this->warning
// $chart_data is the data from a chart record
// $no_trace can be used to prevent tracing - used by the chart editor to prevent traces for the raw data tables
// $extra_trace_data is added to the trace file
//
function drawChart(&$chart_data, $no_trace=false, $extra_trace_data='')
{													
// initialise all our global properties so that we can be called multiple times

	$this->chart_data = $chart_data;
	$this->start_time = microtime(true);	// time we started to draw the plot
	$this->db = JFactory::getDBO();		// default to the site database
	$this->error = '';					// error message to be returned
	$this->warning = '';				// warning to be returned
	$this->nulls_defaulted = false;		// flag so we only report this warning once
	$this->chart_script = '';			// the chart url (http://chart.apis.google.com/chart?...)
	$this->datasets = array();			// the dataset arrays
	$this->total_rows = 0;				// total number of rows from all queries
	$this->active_plots = 0;			// number of plots that have rows
	$this->chart_title = '';			// the resolved chart title
	$this->x_title = '';				// the resolved X axis title
	$this->y_title = '';				// the resolved Y axis title
	$this->chart_x_min;					// overall minimum X value for all datasets
	$this->chart_x_max;					// overall maximum X value for all datasets
	$this->chart_y_min;					// overall minimum Y value for all datasets
	$this->chart_y_max;					// overall maximum Y value for all datasets
	
// try to get the component parameters
// if Plotalot is not installed this will just get the defaults
//   (which can only happen if the Plotalot class has been used in a non-Plotalot component)

	$params = JComponentHelper::getParams(PLOTALOT_COMPONENT);  	// get component parameters
	$this->select_only = $params->get('selectonly',true);
	$this->multiquery = $params->get('multiquery',false);
	$this->fixnulls = $params->get('fixnulls',true);
	$this->experimental = $params->get('experimental',false);
	$this->api_version = $params->get('api_version',0);
	$this->chart_locale = $params->get('chart_locale','');

// if the trace file exists, create a full trace

	$this->trace = false;
	if (@file_exists(PLOTALOT_TRACE_FILE) && !$no_trace)
		{
		$site_db_type = $this->joomla_app->get('dbtype');
		$db_info = $this->_db_info($site_db_type);
		$this->trace = true;
		@unlink(PLOTALOT_TRACE_FILE);	// start again for each call
		$this->_trace("Plotalot chart trace started at ".date("d/m/Y H:i"));
		$this->_trace("Plotalot version: ".PLOTALOT_VERSION);
		$this->_trace("Server:           ".PHP_OS);
		$this->_trace("PHP version:      ".phpversion());
		$locale = setlocale(LC_ALL,0);
		$this->_trace("PHP Locale:       ".print_r($locale, true));
		$this->_trace("Site db type:     ".$site_db_type);
		$this->_trace("Site db version:  ".$db_info);
		$this->_trace("Joomla Version:   ".JVERSION);
		$langObj = JFactory::getLanguage();
		$language = $langObj->get('tag');
		$this->_trace("Joomla Language:  ".$language);
        $this->_trace('-------------------------------');
        $this->_trace($extra_trace_data);
		$this->_trace("Chart Type:       ".$this->chart_data->chart_type);
        if (defined('JPATH_COMPONENT'))
            $this->_trace("JPATH_COMPONENT:  ".JPATH_COMPONENT);
        else
            $this->_trace("JPATH_COMPONENT:  Not defined");
		$this->_trace("Chart Data:".print_r($chart_data,true));
        $this->_trace(str_repeat("-",50));
		}
		
// default the non-mandatory properties to avoid PHP notices

	if (!isset($this->chart_data->id))
		$this->chart_data->id = 1;
	$chart_id = $this->chart_data->id;

	if (!isset($this->chart_data->chart_option))
		$this->chart_data->chart_option = 0;
	if (!isset($this->chart_data->chart_title))
		$this->chart_data->chart_title = '';
	if (!isset($this->chart_data->chart_title_colour))
		$this->chart_data->chart_title_colour = '';
	if (!isset($this->chart_data->back_colour))
		$this->chart_data->back_colour = '';
	if (!isset($this->chart_data->chart_css_style))		// valid but only accessible by API
		$this->chart_data->chart_css_style = '';
	if (!isset($this->chart_data->db_driver))
		$this->chart_data->db_driver = 'mysqli';
	if (!isset($this->chart_data->db_host))
		$this->chart_data->db_host = '';
	if (!isset($this->chart_data->db_name))
		$this->chart_data->db_name = '';
	if (!isset($this->chart_data->db_user))
		$this->chart_data->db_user = '';
	if (!isset($this->chart_data->db_pass))
		$this->chart_data->db_pass = '';
	if (!isset($this->chart_data->db_prefix))
		$this->chart_data->db_prefix = '';
	if (!isset($this->chart_data->show_grid))
		$this->chart_data->show_grid = '';
	if (!isset($this->chart_data->legend_type))
		$this->chart_data->legend_type = LEGEND_NONE;
	if (!isset($this->chart_data->x_title))
		$this->chart_data->x_title = '';
	if (!isset($this->chart_data->x_start))
		$this->chart_data->x_start = '';
	if (!isset($this->chart_data->x_end))
		$this->chart_data->x_end = '';
	if (!isset($this->chart_data->x_format))
		$this->chart_data->x_format = FORMAT_NUM_UK_0;
	if (!isset($this->chart_data->x_labels))
		$this->chart_data->x_labels = -1;
	if (!isset($this->chart_data->y_title))
		$this->chart_data->y_title = '';
	if (!isset($this->chart_data->y_start))
		$this->chart_data->y_start = '';
	if (!isset($this->chart_data->y_end))
		$this->chart_data->y_end = '';
	if (!isset($this->chart_data->y_format))
		$this->chart_data->y_format = FORMAT_NUM_UK_0;
	if (!isset($this->chart_data->y_labels))
		$this->chart_data->y_labels = -1;
	if (!isset($this->chart_data->extra_parms))
		$this->chart_data->extra_parms = '';
	if (!isset($this->chart_data->extra_columns))
		$this->chart_data->extra_columns = '';
	if (!isset($this->chart_data->extra_script))
		$this->chart_data->extra_script = '';
	if (!isset($this->chart_data->png_link))
		$this->chart_data->png_link = '';
	if (!isset($this->chart_data->plot_params))
		$this->chart_data->plot_params = array();
	if (!isset($this->chart_data->design_pattern))
		$this->chart_data->design_pattern = DESIGN_CLASSIC;

// parse and check the extra columns

	if ($this->chart_data->extra_columns == '')
		{
		$this->extra_column_array = array();
		$this->extra_column_count = 0;
		}
	else
		{
		$extra_column_array = explode(',',$this->chart_data->extra_columns);
		$this->extra_column_array = array_map('trim',$extra_column_array);
		$this->extra_column_count = count($this->extra_column_array);
		foreach ($this->extra_column_array as $extra_column)
			if (self::extraColumnDataType($extra_column) == '')
				{
				$this->_error(JText::_('COM_PLOTALOT_INVALID').': '.JText::_('COM_PLOTALOT_EXTRA_COLUMNS')." ($extra_column)");
				return '';
				}
		}

// the x and y columns are different for bubble charts

	$this->x_column = 0;
	$this->y_column = 1;
	if ($this->chart_data->chart_type == CHART_TYPE_BUBBLE)
		{
		$this->x_column = 1;
		$this->y_column = 2;
		}

// get the data

	if (!$this->_getAllData())
		return '';
	$this->num_data_sets = count($this->datasets);
			
// Plotalot tables and single items are handled separately

	switch ($this->chart_data->chart_type)
		{
		case CHART_TYPE_SINGLE_ITEM: return $this->_drawSingleItem();
		case CHART_TYPE_PL_TABLE: return $this->_drawHtmlTable();
		case CHART_TYPE_PL_TABLE_CSS: return $this->_drawCssTable();
		}
	
// start the Google Vizualization Options Object

	$this->gvOptions = "{title:'".$this->chart_title."'";

// if the chart size is zero, don't specify it - the API will take the size from the container

	if (!empty($this->chart_data->x_size))
		$this->gvOptions .= ",width:".$this->chart_data->x_size;
	if (!empty($this->chart_data->y_size))
		$this->gvOptions .= ",height:".$this->chart_data->y_size;
	
// background colour - if it's white we don't need to specify it

	switch ($this->chart_data->back_colour)
		{
		case 'FFFFFF': break;
		case '': 
			if ($this->chart_data->chart_type != CHART_TYPE_GANTT)
				$this->gvOptions .= ",backgroundColor:{fill:'transparent'}"; 	// this crashes a Gantt Chart with "'none' is not a valid hex color"
			break;
		default: $this->gvOptions .= ",backgroundColor:{fill:'#".$this->chart_data->back_colour."'}";
		}
		
// title text colour

	if ($this->chart_data->chart_title_colour != '')
		$this->gvOptions .= ",titleTextStyle:{color:'#".$this->chart_data->chart_title_colour."'}";
		
// these chart specific functions continue building the data and options objects

	$this->gvPackages = 'corechart';
	$this->gvExtra = '';
	$this->gvConvertOptions = '';
	switch ($this->chart_data->chart_type)
		{
		case CHART_TYPE_LINE:
		case CHART_TYPE_AREA:
			$ret = $this->_lineOrAreaGraph();
			break;
		case CHART_TYPE_SCATTER:
			$ret = $this->_scatterGraph();
			break;
		case CHART_TYPE_GAUGE:
			$ret = $this->_gaugeChart();
			break;
		case CHART_TYPE_BAR_H_STACK:
		case CHART_TYPE_BAR_H_GROUP:
		case CHART_TYPE_BAR_V_STACK:
		case CHART_TYPE_BAR_V_GROUP:
			$ret = $this->_barChart();
			break;
		case CHART_TYPE_PIE_2D:
		case CHART_TYPE_PIE_3D:
		case CHART_TYPE_PIE_2D_V:
		case CHART_TYPE_PIE_3D_V:
			$ret = $this->_pieChart();
			break;
		case CHART_TYPE_GV_TABLE:
			$ret = $this->_tableChart();
			break;
		case CHART_TYPE_TIMELINE:
			$ret = $this->_timelineChart();
			break;
		case CHART_TYPE_BUBBLE:
			$ret = $this->_bubbleChart();
			break;
		case CHART_TYPE_COMBO_STACK:
		case CHART_TYPE_COMBO_GROUP:
			$ret = $this->_comboChart();
			break;
		case CHART_TYPE_CANDLESTICK:
			$ret = $this->_candlestickChart();
			break;
		case CHART_TYPE_ORG:
			$ret = $this->_orgChart();
			break;
		case CHART_TYPE_TREEMAP:
			$ret = $this->_treeMap();
			break;
		case CHART_TYPE_GEO:
			$ret = $this->_geoChart();
			break;
		case CHART_TYPE_GANTT:
			$ret = $this->_ganttChart();
			break;
		case CHART_TYPE_CALENDAR:
			$ret = $this->_calendarChart();
			break;
		case CHART_TYPE_ANNOTATION:
			$ret = $this->_annotationChart();
			break;
		default:
			{
			$this->_error(JText::_('COM_PLOTALOT_ERROR_WRONG_CHART_TYPE'));
			return '';
			}
		}
		
	if (!$ret)
		return '';
		
// finish off the Options Object

	$extra_parms = str_replace(array("\n","\r"), "", $this->chart_data->extra_parms); 	// remove any CR's or LF's
	if ((!empty($extra_parms)) && (substr($extra_parms,0,1) != ','))					// add a leading comma if there isn't one
		$extra_parms = ','.$extra_parms;
	$this->gvOptions .= "\n".$extra_parms."}";
		
// build the chart script
	
	$this->chart_script .= "\n".'<script type="text/javascript">';
	if ($this->experimental)
		$gversion = 'upcoming';
	else
		$gversion = 'current';
	if ($this->api_version != 0)
		$gversion = $this->api_version;
	if ($this->chart_locale !== '')
		$this->gvExtra .= ", 'language':'".$this->chart_locale."'";

	$this->chart_script .= "\n"."google.charts.load('".$gversion."', {'packages': ['$this->gvPackages']".$this->gvExtra."});";
	$this->chart_script .= "\n".'google.charts.setOnLoadCallback(create_chart);';

    if (!empty($this->chart_data->extra_script))
		{
		$this->chart_script .= "\nfunction plotalot_chart_".$chart_id."_extra() {"; 
        $this->chart_script .= "\n".$this->chart_data->extra_script;
		$this->chart_script .= "\n}";
		}

	$this->chart_script .= "\n".'function create_chart() {'; 
	$this->chart_script .= $this->gvDataTable;
	$this->chart_script .= "\nwindow.plotalot_chart_".$chart_id."_options = ".$this->gvOptions;
	$this->chart_script .= "\n"."window.plotalot_chart_$chart_id = new google.$this->gvClass(document.getElementById('chart_$chart_id'));";
	if ($this->chart_data->png_link != '')
		{
		$this->chart_script .= "\n"."google.visualization.events.addListener(window.plotalot_chart_$chart_id, 'ready', function () {";
		$this->chart_script .= "\n"." var pngDiv = document.getElementById('chart_".$chart_id."_png');";
		$this->chart_script .= "\n"." if (pngDiv !== null)";
		$this->chart_script .= "\n"."  pngDiv.innerHTML = '<a href=".'"'."'+window.plotalot_chart_".$chart_id.".getImageURI()+'".'" download>'.$this->chart_data->png_link."</a>'; });";
		}
    if (!empty($this->chart_data->extra_script))
		$this->chart_script .= "\nplotalot_chart_".$chart_id."_extra();"; 
	if (empty($this->gvConvertOptions))
		$this->chart_script .= "\n"."window.plotalot_chart_$chart_id.draw(window.plotalot_chart_".$chart_id."_data, window.plotalot_chart_".$chart_id."_options);";
	else
		$this->chart_script .= "\n"."window.plotalot_chart_$chart_id.draw(window.plotalot_chart_".$chart_id."_data, google.$this->gvConvertOptions(window.plotalot_chart_".$chart_id."_options));";
	$this->chart_script .= "\n}";				// end of the create_chart function
	$this->chart_script .= "\n</script>";
		
	$this->_trace("Script: ".$this->chart_script);
	$this->end_time = microtime(true);
	$runtime = 	$this->end_time - $this->start_time;
	$this->_trace("Runtime: $runtime seconds");

	return $this->chart_script;
}

}