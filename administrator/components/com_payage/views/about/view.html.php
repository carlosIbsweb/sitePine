<?php
/********************************************************************
Product     : Payage
Date		: 22 January 2022
Copyright	: Les Arbres Design 2014-2022
Contact	    : https://www.lesarbresdesign.info
Licence     : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class PayageViewAbout extends JViewLegacy
{
function display($tpl = null)
{
    LAPG_admin::addSubMenu('about');
    LAPG_admin::viewStart();
    JToolBarHelper::title('Payage: '.JText::_('COM_PAYAGE_ABOUT'), 'lad.png');
	JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');
    
	?>
	<form method="get" name="adminForm" id="adminForm" class="form-horizontal">
	<input type="hidden" name="option" value="com_payage" />
	<input type="hidden" name="controller" value="about" />
	<input type="hidden" name="task" value="" />
	<?php
	
// get the latest version info

    $this->latest_version = '';
    $this->transaction_status = '';
	$this->get_version('payage', '');

// build the help screen

	$about['name'] = 'Payage';
	$about['prefix'] = 'COM_PAYAGE';
	$about['current_version'] = PayageHelper::getComponentVersion();
	$about['latest_version'] = $this->latest_version;
	$about['reference'] = 'payage';
	$about['link_version'] = "https://www.lesarbresdesign.info/version-history/payage";
	$about['link_doc'] = "https://www.lesarbresdesign.info/extensions/payage";
	$about['link_rating'] = "https://extensions.joomla.org/extension/e-commerce/payment-systems/payage/";
    
	$about['extra'][0]['left']  = 'Open SSL version';
	$about['extra'][0]['right'] = OPENSSL_VERSION_TEXT;
    
	$about['extra'][1]['left']  = 'CURL version ';
    if (function_exists('curl_init'))
		{
        $curl_info = curl_version();
		$about['extra'][1]['right'] = $curl_info['version']; // curl 7.34.0 was the first to claim support for TLSv1.2
		}
    else
        $about['extra'][1]['right'] = 'Not installed';

// add the plugin status, if it's installed

	$plugin_status = LAPG_trace::getPluginStatus();
	if ($plugin_status)
		{
		$about['extra'][2]['left']  = 'Payage Plugin';
		$about['extra'][2]['right'] = $plugin_status;
		$this->get_version('plg_payage',$this->purchase_id);  // get the latest version and transaction_status of the plugin
		$about['extra'][3]['left']  = 'Payage Plugin latest version';
		$about['extra'][3]['right'] = $this->latest_version;
		}

	echo '<h3>'.$about['name'].': '.JText::_('COM_PAYAGE_HELP_TITLE').'</h3>';
    echo '<fieldset class="lad-fieldset lad-half">';
	$this->draw_about($about);
	echo '<p></p>';

// If the Payage Plugin update server is installed, show the Purchase ID field

	if ($this->purchase_id)		// if not false, the plugin update server is installed
        {
		JToolBarHelper::apply('save_about');
		if ($this->purchase_id === true)
			$this->purchase_id = '';
		$controls = '<input type="text" class="form-control input-xlarge" name="purchase_id" id="purchase_id" value = "'.$this->purchase_id.'" />';
		echo LAPG_admin::make_field(JText::_('COM_PAYAGE_PURCHASE_ID'), $controls, 'purchase_id', JText::_('COM_PAYAGE_PURCHASE_ID_DESC'));
		if (!empty($this->transaction_status))
			echo $this->transaction_status;
		}
	echo LAPG_trace::make_trace_controls();    // show the trace controls
    echo '</fieldset>';
	
// show the installed gateways and contact details

	if (!empty($this->gateway_list))
		{
        echo '<fieldset class="lad-fieldset lad-half">';
		echo '<h4>'.JText::_('COM_PAYAGE_GATEWAYS_INSTALLED').'</h4>';
		echo '<table class="table table-striped table-bordered table-condensed width-auto">';
		foreach ($this->gateway_list as $gateway_info)
			{
			echo '<tr>';
			echo '<td>'.$gateway_info['longName'].'</td>';
			echo '<td>'.$gateway_info['version'].'</td>';
			echo '<td>'.JHtml::link($gateway_info['authorUrl'], $gateway_info['author'],'target="_blank"').'</td>';
			if ($gateway_info['supported'])
				{
				echo '<td>'.JHtml::link($gateway_info['helpUrl'], JText::_('JHELP'), 'target="_blank"').'</td>';
				echo '<td>'.JHtml::link($gateway_info['docUrl'], JText::_('COM_PAYAGE_DOCUMENTATION'), 'target="_blank"').'</td>';
				}
			else
				echo '<td></td><td></td>';
			echo '</tr>';
			}
		echo '</table>';
        echo '</fieldset>';
		}
	else
		echo '<h4>'.JText::_('COM_PAYAGE_NO_GATEWAYS').'</h4>';
		
	echo '<p></p>';				// some blank lines so that we can get to the trace controls
	echo '<p></p>';
	echo '<p></p>';
	echo '</form>';
    LAPG_admin::viewEnd();
}

//------------------------------------------------------------------------------
// draw the about screen - this is the same in all our components
// (slightly non-standard, the top title is drawn above)
//
function draw_about($about)
{
// for Payage only, the title is drawn outside this function

	if (!empty($this->lad_info_notice))
		echo $this->lad_info_notice;
	else
		{
		echo '<h4>'.JText::_($about['prefix'].'_HELP_RATING').' ';
		echo JHTML::link($about['link_rating'], 'Joomla Extensions Directory', 'target="_blank"').'</h4>';
		}

	echo '<table class="table table-striped table-bordered width-auto table-condensed">';
	
	echo '<tr><td>'.JText::_($about['prefix'].'_VERSION').'</td>';
	echo '<td>'.$about['current_version'].'</td></tr>';
	
	if ($about['latest_version'] != '')
		echo '<tr><td>'.JText::_($about['prefix'].'_LATEST_VERSION').'</td><td>'.$about['latest_version'].'</td></tr>';

	echo '<tr><td>'.JText::_($about['prefix'].'_HELP_CHECK').'</td>';
	echo '<td>'.JHTML::link($about['link_version'], 'Les Arbres Design - '.$about['name'], 'target="_blank"').'</td></tr>';

	echo '<tr><td>'.JText::_($about['prefix'].'_HELP_DOC').'</td>';
	echo '<td>'.JHTML::link($about['link_doc'], "www.lesarbresdesign.info", 'target="_blank"').'</td></tr>';

	$link_jed = "https://extensions.joomla.org/extensions/owner/chrisguk";
	$link_ext = "https://www.lesarbresdesign.info/";

	echo '<tr><td>'.JText::_($about['prefix'].'_HELP_LES_ARBRES').'</td>';
	echo '<td>'.JHTML::link("https://www.lesarbresdesign.info/", 'Les Arbres Design', 'target="_blank"').'</td></tr>';
		
	if (!empty($about['extra']))
		foreach($about['extra'] as $row)
			echo '<tr><td>'.$row['left'].'</td><td>'.$row['right'].'</td></tr>';

	echo '</table>';
}
	
//------------------------------------------------------------------------------
// get the latest version info
//
function get_version($product, $purchase_id)
{
	$version_file = JPATH_ROOT.'/administrator/components/com_payage/latest_'.$product.'.xml';
	$version_info = '';
	$filetime = 0;
	if (file_exists($version_file))
		$filetime = @filemtime($version_file);
	if ((time() - $filetime) < 3600)				// version info is valid for one hour
		$version_info = file_get_contents($version_file);
	else
		{
		$url = 'https://www.lesarbresdesign.info/jupdate?product='.$product.'&src=about';
		if (strlen($purchase_id) == 32)
			$url .= '&tid='.$purchase_id;
		try
			{
			$http = JHttpFactory::getHttp();
			$response = $http->get($url, array(), 20);
			$version_info = $response->body;
			}
		catch (RuntimeException $e)
			{
			return;
			}
		}
	file_put_contents($version_file, $version_info);
    $this->latest_version = self::str_between($version_info, '<version>', '</version>');
    $this->transaction_status = self::str_between($version_info, '<lad_transaction_status><![CDATA[', ']]></lad_transaction_status>');
    $lad_info_notice = self::str_between($version_info, '<lad_info_notice><![CDATA[', ']]></lad_info_notice>');
	if (!empty($lad_info_notice))				// the second call for the plugin would overwrite the first
		$this->lad_info_notice = $lad_info_notice;  
}
				
function str_between($string, $start, $end)
{
    $string = ' '.$string;
    $pos = strpos($string, $start);
    if ($pos == 0)
        return '';
    $pos += strlen($start);
    $len = strpos($string, $end, $pos) - $pos;
    return substr($string, $pos, $len);
}
		
}