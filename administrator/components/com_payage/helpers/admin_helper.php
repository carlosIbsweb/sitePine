<?php
/********************************************************************
Product		: Payage
Date		: 30 June 2022
Copyright	: Les Arbres Design 2014-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

if (substr(JVERSION,0,1) == '3')
	define("LAPG_BS_DATA_CONTENT", "data-content");
else
	define("LAPG_BS_DATA_CONTENT", "data-bs-content");

if (class_exists("LAPG_admin"))
	return;

class LAPG_admin
{

//-------------------------------------------------------------------------------
// Make a select list
// $name          : Field name
// $current_value : Current value
// $list          : Array of ID => value items
// $extra         : Javascript or styling to be added to <select> tag
// $no_id         : if true, no "id" attribute is added
//
static function make_list($name, $current_value, &$items, $extra='', $no_id=false)
{
	if ($no_id)
		$id_attribute = '';
	else
		$id_attribute = ' id="'.$name.'"';
		
	$html = "\n".'<select name="'.$name.'"'.$id_attribute.' class="form-select form-control lad-input-inline" '.$extra.'>';
	if ($items == null)
		return '';
	foreach ($items as $key => $value)
		{
		if (strncmp($key,"OPTGROUP_START",14) == 0)
			{
			$html .= "\n".'<optgroup label="'.$value.'">';
			continue;
			}
		if (strncmp($key,"OPTGROUP_END",12) == 0)
			{
			$html .= "\n".'</optgroup>';
			continue;
			}
		$selected = '';

		if ($current_value == $key)
			$selected = ' selected="selected"';
		$html .= "\n".'<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		}
	$html .= '</select>'."\n";

	return $html;
}

//---------------------------------------------------------------------------------------------
// Make a date picker field for the back end
//
static function make_date_picker($field_name, $date_value, $format = '%Y-%m-%d' )
{
    $form = JForm::getInstance('a_form', '<form> </form>');
    $element = new SimpleXMLElement('<fieldset name="a_fieldset"><field name="'.$field_name.'" type="calendar" showtime="false" format="'.$format.'" /></fieldset>');
    $form->setField($element);
    $form->setValue($field_name, null, $date_value);
    $html = $form->getInput($field_name);    
    return $html;
}

// -------------------------------------------------------------------------------
// Draw the menu and make the current item active
//
static function addSubMenu($submenu = '')
{
	$component_params = JComponentHelper::getParams('com_payage');
	$params = $component_params->toObject();	
	if (!empty($params->hide_submenu))
		return;
    JHtmlSidebar::addEntry(JText::_('COM_PAYAGE_PAYMENTS'), 'index.php?option=com_payage&controller=payment', $submenu == 'payment');
    JHtmlSidebar::addEntry(JText::_('COM_PAYAGE_UNCONFIRMED_PAYMENTS'), 'index.php?option=com_payage&controller=payment&task=unconfirmed', $submenu == 'unconfirmed');
    JHtmlSidebar::addEntry(JText::_('COM_PAYAGE_ACCOUNTS'), 'index.php?option=com_payage&controller=account', $submenu == 'account');
    JHtmlSidebar::addEntry(JText::_('COM_PAYAGE_REPORTS'), 'index.php?option=com_payage&controller=report', $submenu == 'report');
    JHtmlSidebar::addEntry(JText::_('COM_PAYAGE_SYSTEM_LOG'), 'index.php?option=com_payage&controller=syslog', $submenu == 'syslog');
    JHtmlSidebar::addEntry(JText::_('COM_PAYAGE_ABOUT'), 'index.php?option=com_payage&controller=about', $submenu == 'about');
}

// -------------------------------------------------------------------------------
// Draw the component menu
// - called at the start of every view
//
static function viewStart()
{
	$entries = JHtmlSidebar::getEntries();
    if (substr(JVERSION,0,1) == '3')
        {
        if (empty($entries))
            echo '<div id="j-main-container">';
        else
            {
            $sidebar = JHtmlSidebar::render();
            echo '<div id="j-sidebar-container" class="span2">'.$sidebar.'</div>';
            echo '<div id="j-main-container" class="span10">';
            }
        }
    else        // Joomla 4
        {
        echo '<div class="row">';
        if (empty($entries))
			echo '<div class="col-md-12">';
        else
            {
            $sidebar = JHtmlSidebar::render();
            echo '<div id="j-sidebar-container" class="col-md-2">'.$sidebar.'</div>';
            echo '<div class="col-md-10">';
            echo '<div id="j-main-container" class="j-main-container">';
            }
        }
}

// -------------------------------------------------------------------------------
// Called at the end of every view that calls viewStart()
//
static function viewEnd()
{
    if (substr(JVERSION,0,1) == '3')
    	echo "</div>";                          // close "j-main-container"
    else        // Joomla 4
        {
       	echo "</div>";                          // close "j-main-container"
    	$entries = JHtmlSidebar::getEntries();
        if (!empty($entries))
        	echo "</div>";                      // close "col-md-10"
       	echo "</div>";                          // close "row"
        }
}

//-------------------------------------------------------------------------------
// Return the icon and Javascript for published status
//
static function published($value, $i, $link=true)
{
    if (is_object($value))
        $value = $value->published;
        
	if ($value)
		{
		$img = '<span class="icon-publish"></span>';
		$task = 'unpublish';
        $text = JText::_('JLIB_HTML_UNPUBLISH_ITEM');
		}
	else
		{
		$img = '<span class="icon-remove" style="color:gray"></span>';
		$task = 'publish';
        $text = JText::_('JLIB_HTML_PUBLISH_ITEM');
		}   
    
    if (!$link)
        return $img;

   	return '<a href="#" onclick="return Joomla.listItemTask(\'cb'.$i.'\',\''.$task.'\')" title="'.$text.'">'.$img.'</a>';
}

//-------------------------------------------------------------------------------
// Check that a date is valid YYYY-MM-DD
// Returns true if valid, false if not
//
static function validDate($date, $allow_blank = false)
{
	if ($allow_blank && (empty($date) || ($date == '0000-00-00')))
		return true;
	if (strlen($date) != 10)
		return false;
	if (($date[4] != '-') || ($date[7] != '-'))
		return false;
	if (!is_numeric(substr($date,0,4).substr($date,5,2).substr($date,8,2)))
		return false;
	return checkdate(substr($date,5,2), substr($date,8,2), substr($date,0,4));	// month, day, year
}

//-------------------------------------------------------------------------------
// Make a field the way Joomla would
//
static function make_field($label, $controls, $for = '', $title = '')
{
	if (empty($for))
		$for_html = '';
	else
		$for_html = ' for="'.$for.'"';
	if (empty($title))
		$title_html = '';
	else
		{
		JHtml::_('bootstrap.popover', 'span.hasPopover', ['trigger' => 'hover focus']);
		$title_html = ' title="'.$label.'" '.LAPG_BS_DATA_CONTENT.'="'.$title.'" class="hasPopover"';
		}
	$html = "\n".'<div class="control-group"><div class="control-label"><label'.$for_html.$title_html.'>'.$label.'</label></div>';
    $html .= '<div class="controls">'.$controls.'</div></div>';
	return $html;
}

// -------------------------------------------------------------------------------
// Cleanup and validate a string
//
static function is_string(&$str, $min=0, $max=0, $alpha_check=true)
{
	$str = trim(str_replace('"',"'",$str));			// trim and replace double quotes with single quotes
	$str = filter_var($str, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);	// remove tags
	if ((strlen($str) > 0) && $alpha_check)
		if (!preg_match('#[a-zA-Z]+#', $str))       // must have at least one alpha
			return false;
	if (strlen($str) < $min)
		return false;
	if (($max != 0) && (strlen($str) > $max))
		$str = substr($str, 0, $max);
	return true;
}

//-------------------------------------------------------------------------------
// Return true if supplied argument is a positive integer, else false
//
static function is_posint($arg, $allow_blank=true, $min=0, $max=0)
{
	if ($arg === '')
		{
		if ($allow_blank)
			return true;
		else
			return false;
		}
	$filter_options = array('options' => array('min_range' => $min));
	if ($max != 0) 
		$filter_options['options']['max_range'] = $max;
	if (filter_var($arg, FILTER_VALIDATE_INT, $filter_options) === false)
		return false;   // filter_var() returns the filtered data, or false if the filter fails
	else
		return true;
}

// -------------------------------------------------------------------------------
// Download a buffer as a file
//
static function file_download($data, $file_name, $mime_type)
{
	$data_length = strlen($data);
	while (ob_get_length() !== false)
  		ob_end_clean();
	if (function_exists('apache_setenv'))
		@apache_setenv('no-gzip', 1);
	@ini_set('zlib.output_compression', 0);
	header("Content-Description: File Transfer");
	header('Content-Disposition: attachment; filename="'.$file_name.'"');
	header("Content-Type: ".$mime_type);
	header("Cache-Control: public");
	header('Pragma: public');
	header("Expires: 0");
	header("Content-Length: ".$data_length);
	header("Content-Range: bytes 0-" .($data_length - 1).'/'.$data_length);
	header("Accept-Ranges: none");
	echo $data;
	@ob_flush();
	@flush();
}

}