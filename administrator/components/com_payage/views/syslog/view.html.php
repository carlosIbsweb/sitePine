<?php
/********************************************************************
Product		: Payage
Date		: 22 January 2022
Copyright	: Les Arbres Design 2009-2022
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

Class PayageViewSyslog extends JViewLegacy
{

//-----------------------------------------------------------------------------
// Show the log list
//
function display($tpl = null)
{
	LAPG_admin::addSubMenu('syslog');
	LAPG_admin::viewStart();
	JToolbarHelper::title('Payage: '.JText::_('COM_PAYAGE_SYSTEM_LOG'), 'lad.png');
	JToolbarHelper::deleteList();

// get the filter states

	$app = JFactory::getApplication();
	$search = $app->getUserStateFromRequest(LAPG_COMPONENT.'.log_search','log_search','','string');
	$search	 = mb_strtolower($search);
    
// make the filter list

	echo '<form method="post" name="adminForm" id="adminForm" class="lad-filterform">';
	echo '<input type="hidden" name="option" value="'.LAPG_COMPONENT.'" />';
	echo '<input type="hidden" name="task" id="task" value="" />';
	echo '<input type="hidden" name="boxchecked" value="0" />';
	echo '<input type="hidden" name="controller" value="syslog" />';
        
	echo '<div>&nbsp;<div class="lad-filterform-left">';
	JHtml::_('bootstrap.tooltip', 'span.hasTooltip', ['trigger' => 'hover focus']);
	$icon = '<span class="icon-search"></span>';
	echo '<span class="hasTooltip" title="'.JText::_('JSEARCH_FILTER').'">'.$icon.'</span>';
    echo ' <input type="text" class="form-control lad-input-inline input-large" name="log_search" id="log_search" value="'.$search.'" />';
    echo ' <button type="button" class="btn btn-primary" onclick="this.form.task.value='."''".';this.form.submit();">'.JText::_('COM_PAYAGE_GO').'</button>';
	echo '</div>';
	echo '<div class="lad-filterform-right">';
    $onclick = " onclick=\"document.getElementById('log_search').value='';
        if (typeof(document.adminForm.limitstart) != 'undefined')
	        document.adminForm.limitstart.value=0;
        document.adminForm.task.value='';
        this.form.submit();\"";
    echo '<button type="button" class="btn btn-primary"'.$onclick.' >'.JText::_('JSEARCH_RESET').'</button>';
	echo '</div></div>';

	echo '<table class="table table-striped">';
	echo '<thead><tr>';
	echo '<th style="width:20px; text-align:center;"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>';
	echo '<th>'.JText::_('COM_PAYAGE_DATE_TIME').'</th>';
	echo '<th></th>';
	echo '<th style="width:100%">'.JText::_('COM_PAYAGE_DESCRIPTION').'</th>';
	echo '</tr></thead>';
        
	echo '<tbody>';
    $i = 0;
	foreach ($this->log_list as $row) 
		{
        $log_type_info = $this->get_info($row);
            
		$link = LAPG_COMPONENT_LINK.'&task=edit&controller=syslog&cid[]='.$row->id;
		$checked = JHTML::_('grid.id', $i, $row->id);
            
		echo "<tr>";
		echo '<td style="text-align:center;">'.$checked.'</td>';
        if (empty($row->detail))
    		echo '<td style="white-space:nowrap;">'.$row->date_time.'</td>';
        else
    		echo '<td style="white-space:nowrap;">'.JHTML::link($link, $row->date_time).'</td>';
		echo '<td style="text-align:center;">'.$log_type_info['icon'].'</td>';
        if (empty($row->detail))
            echo '<td>'.$log_type_info['title'].'</td>';
        else
            echo '<td>'.JHTML::link($link, $log_type_info['title']).'</td>';
		echo "</tr>\n";
		$i ++;
		}
        
	echo '</tbody>';
	echo '<tfoot><tr><td colspan="4">'.$this->pagination->getListFooter().'</td></tr></tfoot>';
	echo '</table></form>';
	LAPG_admin::viewEnd();
}

//-----------------------------------------------------------------------------
// Show a single log entry
//
function edit($tpl = null)
{
	LAPG_admin::addSubMenu('syslog');
	LAPG_admin::viewStart();
	JToolbarHelper::title('Payage: '.JText::_('COM_PAYAGE_SYSTEM_LOG'), 'lad.png');
	JToolbarHelper::cancel();

	?>
	<form method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="<?php echo LAPG_COMPONENT ?>" />
	<input type="hidden" name="id" value="<?php echo $this->log_data->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="syslog" />
    </form>
	<?php
    
    $log_type_info = $this->get_info($this->log_data);

	echo '<h3>'.$this->log_data->date_time.' - '.$log_type_info['title'].'</h3>';
    if (!empty($this->log_data->client_ip))
        echo '<h4>'.JText::_('COM_PAYAGE_IP_ADDRESS').': '.$this->log_data->client_ip.'</h4>';
    echo '<div class="ms_log_entry">'.$this->log_data->detail.'</div>';
        
	LAPG_admin::viewEnd();
}

//-----------------------------------------------------------------------------
// Get the default title and icon for a log entry type
//
function get_info($data)
{
    $log_type_info = array();
    switch ($data->log_type)
        {
        case LAPG_LOG_INFO:
            $log_type_info['icon'] = '<span class="icon-info-circle" style="font-size:18px;color:#1e88e5"></span>';
            $log_type_info['title'] = $data->title;
            break;
        case LAPG_LOG_DATABASE_ERROR:
            $log_type_info['icon'] = '<span class="icon-database" style="font-size:18px;color:orange"></span>';
            $log_type_info['title'] = JText::_('COM_PAYAGE_DATABASE_ERROR');
            break;
        case LAPG_LOG_OTHER_ERROR:
            $log_type_info['icon'] = '<span class="icon-warning" style="font-size:18px;color:orange"></span>';
            $log_type_info['title'] = $data->title;
            break;
        case LAPG_LOG_REFUND:
            $log_type_info['icon'] = '<span class="icon-reply" style="font-size:18px;color:firebrick"></span>';
            $log_type_info['title'] = $data->title;
            break;
        default:
            $log_type_info['icon'] = '<span class="icon-unpublish" style="font-size:18px"></span>';
            $log_type_info['title'] = '?';
            break;
        }
    return $log_type_info;
}

}