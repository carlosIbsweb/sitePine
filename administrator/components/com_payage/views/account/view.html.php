<?php
/********************************************************************
Product		: Payage
Date		: 30 April 2022
Copyright	: Les Arbres Design 2014-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class PayageViewAccount extends JViewLegacy
{

//-------------------------------------------------------------------------------
// Show the list of accounts
//
function display($tpl = null)
{
    LAPG_admin::addSubMenu('account');
    LAPG_admin::viewStart();
	JToolBarHelper::title('Payage: '.JText::_('COM_PAYAGE_ACCOUNTS'), 'lad.png');
	JToolBarHelper::addNew('account_choice');
	JToolBarHelper::publishList();
	JToolBarHelper::unpublishList();
	JToolBarHelper::deleteList();
	JToolBarHelper::cancel('cancellist','JTOOLBAR_CLOSE');
    if (JFactory::getUser()->authorise('core.admin', LAPG_COMPONENT))
		JToolBarHelper::preferences('com_payage');

	if ($this->account_list === false)
        {
        LAPG_admin::viewEnd();
		return;	                                // the db is broken so don't try to do anything
        }

// get the filter states

	$app = JFactory::getApplication();
	$filter_state = $app->getUserStateFromRequest('com_payage.account','filter_state','0','word');
    $onchange = 'onchange="this.form.submit();"';
    $state_list = array('0' => '- '.JText::_('JLIB_HTML_SELECT_STATE').' -', 'P' => JText::_('JPUBLISHED'), 'U' => JText::_('JUNPUBLISHED'));
	$state_list_html = LAPG_admin::make_list('filter_state', $filter_state, $state_list, $onchange);
        
// Show the list of accounts

	JHtml::_('jquery.framework');
	JHtml::_('bootstrap.tooltip');
    $order_heading = '<a href="javascript:jQuery(\'[id^=cb]\').prop(\'checked\', true);Joomla.submitform(\'saveorder\');" class="hasTooltip btn lad_order_btn"
        title="'.JText::_('JLIB_HTML_SAVE_ORDER').'"><span class="icon-redo"></span></a>';
	$error_icon = '<span class="icon-cancel-2" style="color:firebrick;font-size:larger;margin-right:.5em"></span>';
	$numrows = count($this->account_list);

	?>
	<form method="get" name="adminForm" id="adminForm" class="lad-filterform">
	<input type="hidden" name="option" value="com_payage" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="account" />
    <?php
    
    echo '<div>&nbsp;<div class="lad-filterform-left"></div>'; 
	echo '<div class="lad-filterform-right">';
    echo $state_list_html;
    echo ' <button type="button" class="btn btn-primary" onclick="'."
            document.getElementById('filter_state').value='0';
            this.form.submit();".'">'.JText::_('JSEARCH_RESET').'</button>';
	echo '</div></div>';

    if ($numrows == 0)
        {
    	$app = JFactory::getApplication();
		$app->enqueueMessage(JText::_('COM_PAYAGE_NO_ACCOUNTS'), 'notice');
        echo '</form>';
        LAPG_admin::viewEnd();
        return;
        }

	echo '<table class="table table-striped">';
	echo '<thead><tr>';
	echo '<th style="width:20px; text-align:center;"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>';
	echo '<th style="width:20px; text-align:center;">'.JText::_('JPUBLISHED').'</th>';
	echo '<th style="width:5%;  text-align:center;" colspan="2">'.JText::_('JFIELD_ORDERING_LABEL').'</th>';
	echo '<th style="width:2%;  text-align:center;" >'.$order_heading.'</th>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_NAME').'</th>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_TYPE').'</th>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_GROUP').'</th>';
	echo '<th style="white-space:nowrap; text-align:center;">'.JText::_('COM_PAYAGE_BUTTON').'</th>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_CURRENCY').'</th>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_FEE').'</th>';
	echo '<th style="width:25px;text-align:center;">'.JText::_('JGLOBAL_FIELD_ID_LABEL').'</th>';
	echo '</tr></thead>';

	echo '<tbody>';

	for ($i=0; $i < $numrows; $i++) 
		{
		$row_error = false;
		$editable = true;
		$row = $this->account_list[$i];
		$link = LAPG_COMPONENT_LINK.'&task=edit&controller=account&cid[]='.$row->id;
		$img = '<img src="'.JURI::root(true).'/'.$row->button_image.'" alt="" title="'.$row->button_title.'" style="height:28px;" />';
		$gateway_shortname = $row->gateway_shortname;
		switch ($row->gateway_type)
			{
			case 'Skrill_LesArbres': 
				$row->gateway_shortname = ' Skrill - '.strtoupper(JText::_('COM_PAYAGE_DISCONTINUED'));
				$gateway_shortname = $error_icon.$row->gateway_shortname;
				if ($row->published)
					$row_error = true;
				break;
			case 
				'PayPlug_LesArbres': 
				$row->gateway_shortname = ' PayPlug - '.strtoupper(JText::_('COM_PAYAGE_DISCONTINUED'));
				$gateway_shortname = $error_icon.$row->gateway_shortname;
				if ($row->published)
					$row_error = true;
				break;
			case 
				'Stripe_LesArbres': 
				$row->gateway_shortname = ' Stripe Legacy - '.strtoupper(JText::_('COM_PAYAGE_DISCONTINUED'));
				$gateway_shortname = $error_icon.$row->gateway_shortname;
				if ($row->published)
					$row_error = true;
				break;
			}
		if (!isset($this->gateway_list[$row->gateway_type]))
			{
			$gateway_shortname = $error_icon.' '.$row->gateway_shortname.' - '.JText::_('COM_PAYAGE_GATEWAY_NOT_INSTALLED');
			$editable = false;
			if ($row->published)
				$row_error = true;
			}

		if ($row_error)
			echo "\n".'<tr class="lad-row-error">';
		else
			echo "\n".'<tr>';
		echo '<td style="text-align:center;">'.JHtml::_('grid.id', $i, $row->id).'</td>';
		echo '<td style="text-align:center;">'.LAPG_admin::published($row, $i).'</td>';
		echo '<td>'.$this->pagination->orderUpIcon($i).'</td>';
		echo '<td>'.$this->pagination->orderDownIcon($i, $numrows).'</td>';
		echo '<td style="text-align:center"><input type="text" name="order[]" value="'.$row->ordering.'" style="text-align:center; padding:0 0; margin:0 0 0 0; width:35px;" /></td>';        
		if ($editable)
			echo '<td>'.JHtml::link($link, $row->account_name).'</td>';
		else
			echo '<td>'.$row->account_name.'</td>';		
		echo '<td>'.$gateway_shortname.'</td>';
		echo '<td>'.$row->account_group.'</td>';
		echo '<td style="text-align:center;">'.$img.'</td>';
		echo '<td>'.$row->account_currency.'</td>';
		echo '<td>'.self::make_fee_summary($row).'</td>';
        echo '<td style="text-align:center;">'.$row->id.'</td>';
		echo "</tr>\n";
		}
        
    echo '</tbody></table></form>';
    LAPG_admin::viewEnd();
}

static function make_fee_summary($row)
{
	switch ($row->fee_type)
		{
		case LAPG_FEE_TYPE_NONE:
			return '';
			
		case LAPG_FEE_TYPE_FIXED:
			return PayageHelper::format_amount($row->fee_amount, $row->currency_format, $row->currency_symbol);
			
		case LAPG_FEE_TYPE_PERCENT:
			$fee_summary = '';
			if ($row->fee_min != 0)
				$fee_summary .= PayageHelper::format_amount($row->fee_min, $row->currency_format, $row->currency_symbol).' .. ';
			$fee_summary .= $row->fee_amount.'%';
			if ($row->fee_max != 0)					// 0 means no maximum
				$fee_summary .= ' .. '.PayageHelper::format_amount($row->fee_max, $row->currency_format, $row->currency_symbol);
			return $fee_summary;
		default:
			return '';
		}
}

//-------------------------------------------------------------------------------
// Show the choice of gateway types
//
function choice()
{
    LAPG_admin::addSubMenu('account');
    LAPG_admin::viewStart();
	JToolBarHelper::title('Payage: '.JText::_('COM_PAYAGE_SELECT_GATEWAY_TYPE'), 'lad.png');
	JToolBarHelper::cancel();

// build the list of supported gateways

	$account_model = $this->getModel('account');
	$gateway_list = $account_model->getGatewayList();
		
// Show the list

	?>
	<form method="get" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_payage" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="account" />
	<?php
    
    if ($gateway_list === false)
        {
        echo '</form>';
        LAPG_admin::viewEnd();
        return;
        }

	echo '<table class="table table-striped">';
	echo '<thead><tr>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_GATEWAY_TYPE').'</th>';
	echo '<th style="white-space:nowrap;">'.JText::_('COM_PAYAGE_DESCRIPTION').'</th>';
	echo '<th style="white-space:nowrap;">URL</th>';
	echo '<th style="white-space:nowrap;">'.JText::_('JAUTHOR').'</th>';
	echo '</tr></thead>';

	echo '<tbody>';

	foreach ($gateway_list as $gateway_type => $gateway_info)
		{
		$link = LAPG_COMPONENT_LINK.'&task=new_account&controller=account&gateway_type='.$gateway_info['type'];
		echo "\n".'<tr>';
		echo '<td>'.JHTML::link($link, $gateway_info['shortName']).'</td>';
		echo '<td>'.$gateway_info['longName'].'</td>';
		$domain = parse_url($gateway_info['gatewayUrl'],PHP_URL_HOST);
		echo '<td>'.JHTML::link($gateway_info['gatewayUrl'], $domain, 'target="_blank"').'</td>';
		echo '<td>'.$gateway_info['author'].'</td>';
		echo "</tr>\n";
		}

	echo '</tbody></table></form>';
    LAPG_admin::viewEnd();
}

//-------------------------------------------------------------------------------
// The account edit screen
//
function edit()
{
    LAPG_admin::viewStart();
	$gateway_model = $this->getModel();	
	$gateway_type = $this->gateway_info['type'];
	$title = 'Payage: '.$this->gateway_info['longName'];
	JToolBarHelper::title($title, 'payage.png');

	if ( ($this->common_data->id > 0) and (method_exists($gateway_model,'Gateway_test')) )
		JToolBarHelper::custom('test', 'star.png', 'star.png', 'COM_PAYAGE_TEST',false);

	JToolBarHelper::apply();
	JToolBarHelper::save();
	if ($this->common_data->id > 0)
		JToolBarHelper::save2copy();
	JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');
    
// if the site has multiple languages, we show a tab for each language

    $languages = PayageHelper::get_site_languages();
    $num_languages = count($languages);
	
// load the JForm definition for the current gateway

	JForm::addFieldPath(JPATH_ADMINISTRATOR.'/components/com_payage/forms');
	$form = JForm::getInstance('account_edit', JPATH_ADMINISTRATOR.'/components/com_payage/forms/'.strtolower($gateway_type).'.xml');	
	$field_sets = $form->getFieldsets();

	echo '<form method="post" name="adminForm" id="adminForm" class="form-horizontal">';
	?>
	<input type="hidden" name="option" value="com_payage" />
	<input type="hidden" name="id" value="<?php echo $this->common_data->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="account" />
	<input type="hidden" name="gateway_type" value="<?php echo $this->common_data->gateway_type; ?>" />
	<?php
	if (!$form->getField('gateway_shortname'))	// the gateway-specific form can choose to allow this to be edited
		echo '<input type="hidden" name="gateway_shortname" value="'.$this->common_data->gateway_shortname.'" />';
	?>
	<input type="hidden" name="published" value="<?php echo $this->common_data->published; ?>" />
	<input type="hidden" name="ordering" value="<?php echo $this->common_data->ordering; ?>" />
	<?php
    
    if ($num_languages > 1)
        {
		if (substr(JVERSION,0,1) == '3')
			$uitab = 'bootstrap';
		else
			$uitab = 'uitab';
        echo JHtml::_($uitab.'.startTabSet','myTab', array('active' => 'tab1'));
        echo JHtml::_($uitab.'.addTab', 'myTab', 'tab1', JText::_('COM_PAYAGE_DETAILS'));
        }

	foreach ($field_sets as $fieldset_name => $fieldset)	
		{
		echo '<div style="display:inline-block;vertical-align:top;margin-right:2px">';            
		echo '<fieldset class="lad-fieldset lad-border width-auto">';
		if (!empty($fieldset->label))
			echo '<legend>'.JText::_($fieldset->label).'</legend>';
            
// set the form field values

		$fields = $form->getFieldset($fieldset_name);
		foreach ($fields as $field)
			{
			$field_name = $field->name;
			$field_name = trim($field_name,'[]');			 // handle arrays
			if (isset($this->common_data->$field_name))
				$form->setValue($field_name, null, $this->common_data->$field_name);
			else
				if (isset($this->specific_data->$field_name))
					{
					if (($field->getAttribute('multiple',false) == true) && is_array($this->specific_data->$field_name))
						$value = json_encode($this->specific_data->$field_name);
					else
						$value = $this->specific_data->$field_name;
					$form->setValue($field_name, null, $value);				
					}
			}

        echo $form->renderFieldset($fieldset_name);
		echo '</fieldset>';
		echo '</div>';
		}
        
// if we have multiple languages, draw the language tabs

    if ($num_languages > 1)
        {
        echo JHtml::_($uitab.'.endTab');
        foreach ($field_sets as $fieldset_name => $fieldset)	
    		if ($fieldset_name == 'main')
        		$fields = $form->getFieldset($fieldset_name);   // get the fields of the 'main' fieldset

        foreach ($languages as $tag => $name)
            {
            echo JHtml::_($uitab.'.addTab', 'myTab', $tag, $tag);
            foreach ($fields as $field)
                {
    			$field_name = $field->name;
                $field_translatable = $field->getAttribute('translatable','');
                if (in_array($field_name,array('button_title', 'button_image', 'account_description')) or ($field_translatable == 'yes') )
                    {
        			if (isset($this->translations[$tag][$field_name]))
                        $field->setValue($this->translations[$tag][$field_name]);                        
                    $html = $field->renderField(array());
                    $lang_html = str_replace($field_name,$tag.'_'.$field_name,$html);  // modify the field name and id
                    echo $lang_html;
                    }
				}
            echo JHtml::_($uitab.'.endTab');
            }
        echo JHtml::_($uitab.'.endTabSet');
        }
        
	echo '</form>';
    LAPG_admin::viewEnd();
}

}